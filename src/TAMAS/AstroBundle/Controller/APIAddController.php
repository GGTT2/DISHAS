<?php

namespace TAMAS\AstroBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Get;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Validation;

use JMS\Serializer\SerializationContext;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

// TODO :
//      - journal id/titre du book en cas d'article ou de book chapter for secondary source
//      - automatiser le check id/creation dans historicalactor et primarysource
class APIAddController extends APIDefaultController {

    private $ENTITIES_ALLOWED = ["PRIMARYSOURCE", "HISTORICALACTOR", "PLACE", "LIBRARY", "HISTORIAN", 
                                 "SECONDARYSOURCE", "JOURNAL"];
    private $FORBIDDEN_FIELDS = ["ID", "CREATED", "UPDATED", "CREATEDBY", "UPDATEDBY"];
    private $constraintsClassName = "Symfony\\Component\\Validator\\ConstraintViolationList";

    /**
     * persistData
     *
     * This method perists an object in the database
     * It deals with new object (creation)
     *
     * @param object $data
     *            : the object to persist
     */
    private function persistData($data) {
        // persisting the data
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush($data);
    }

    /**
     * CheckEditAccess
     *
     * This method checks if the user has the right to access this page depending on (1) whether is is the creator of the item and (2) if he is a super administrator.
     *
     * @param object $session
     * @param object $creator
     *            : creator of the item
     * @param object $user
     *            : logged-in user
     * @return boolean : true if the rights are granted to this user; false if not.
    */  
    private function checkEditAccess($creator, $user) {
        return $user == $creator || $user->hasRole('ROLE_SUPER_ADMIN');
    }
    
    /*
    *
    * Get the content of a website from its url
    *
    */
    private function getWebpages($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($output = curl_exec($ch)) {
            curl_close($ch);
            return $output;
        }

        return false;
    }

    /*
    *
    * Check if an Entity raises a Unique Entity violation when validated
    * TODO : Generaliser à d'autres violations de contraintes si besoin (en la passant en param)
    *
    */
    private function hasUniqueEntityViolation($errors) {
        $constraint_class = "Symfony\\Bridge\\Doctrine\\Validator\\Constraints\\UniqueEntity";

        foreach($errors as $error)
            if ($error->getConstraint() && $error->getConstraint() instanceof $constraint_class) 
                return true;

        return false;
    }

    /*
    *
    * Create an entity, return false if it couldn't
    * If the parameter "object" is set, it means it will try to edit an existing entity
    * If the parameter "add" is true, it means it's trying to create an entity that will be used into another
    * one (ex : a secondary source has historians), and if it already exists, it will try to retrieve it 
    * 
    */
    private function createEntity($entity, $infos, $object = null, $add = false) {
        if ($object == null) {
            $objectName = "\TAMAS\AstroBundle\Entity\\" . $this->formattedEntityName($entity);
            $object = new $objectName();
        }

        foreach($infos as $key => $value) {
            if(strcmp($key, "_comment") == 0) 
                continue;

            $method = "set" . ucfirst($key); // we chose the field to edit
            if (empty($value)) { // if a field is empty, its set to null
                $value = null;
                $infos[$key] = null;
            }

            if (!method_exists($object, $method)) // if a field doesn't exist (for pbs like : typos)
                return $key . " : This field does not exist";

            // check if it's not a protected field
            if (!in_array(strtoupper($key), $this->FORBIDDEN_FIELDS)) // try catch a voir
                $object->$method($value); // on set le champ a la valeur defini dans le fichier
        }

        $errors = $this->container->get('validator')->validate($object);
        if (count($errors) == 0) {
            $this->persistData($object);
            return $object;
        }
        
        if ($add && $this->hasUniqueEntityViolation($errors)) { // if we try to retrieve it to insert it into a special entity (like secondarysource)
            $object = $this->selectDataBy($infos, $entity);
            if ($object) return $object[0];
        }

        return $errors;
    } 

    /*
    *
    * Create a library, return false if it couldn't
    *
    */
    private function createLibrary($value, $add = false) {
        $raw_content = $this->getWebpages("https://isni.ringgold.com/api/stable/institution/" . $value);
            
        if ($raw_content) {
            $infos_array = json_decode($raw_content, true);

            if (!empty($infos_array) && $infos_array != null) { // we check if what the website can be of use
                $library = new \TAMAS\AstroBundle\Entity\Library();
                $library->setLibraryName($infos_array['name']);
                $library->setLibraryCountry($infos_array['country_code']);
                $library->setLibraryIdentifier($infos_array['isni']);
                $library->setCity($infos_array['locality']);

                $errors = $this->container->get('validator')->validate($library);
                if (count($errors) == 0) { // if there's no error or if we try to edit
                    $this->persistData($library);
                    return $library;
                }

                if ($add && $this->hasUniqueEntityViolation($errors)) {
                    $library = $this->selectDataBy(array('libraryIdentifier' => $value), 'library');
                    if ($library) return $library[0]; // if we find it in the db, we send back the first result
                }

                return $errors; 
            }
        }
       
       return false;
    }

    /**
    *
    * Add entities in bulk
    * @Post("/api/add/{entity}")
    *
    */
    public function addEntitiesAction($entity) {
        $response = new JsonResponse(); // initalize the JSON that will be returned at the end

        if (in_array(strtoupper($entity), $this->ENTITIES_ALLOWED)) {
            $input_content = file_get_contents("php://input"); // get input file through the HTTP request

            if ($input_content) {
                $array = json_decode($input_content, true);

                if ($array) {
                    $output_json_array = []; // initialize output JSON array
                    $output_json_array["correctEntities"] = array();
                    $output_json_array["wrongEntities"] = array();

                    if (array_key_exists($entity, $array)) {
                        // if the entity is a library one, it needs a special method, otherwise it's the generic method
                        switch ($entity) {
                            case 'library':
                            case 'historicalactor':
                            case 'primarysource':
                            case 'secondarysource':
                                $entityType = $this->formattedEntityName($entity);
                                break;
                            default :
                                $entityType = 'Entity';
                                break;
                        }
                        $addMethod = "add" . $entityType . "Action";

                        // calls the addMethod for each entity described in the input JSON array
                        foreach ($array[$entity] as $value) { // we get each entities values
                            if(empty($value))
                                continue;

                            $output_json_array = $this->$addMethod($entity, $value, $output_json_array);
                        }

                        // returns entities that have been treated
                        return $response->setData($output_json_array);
                    }
                    else
                        return $response->setData("Your JSON array must have a {" . $entity . "} key but only has {" . implode(array_keys($array)) . "}");
                }
                else
                    return $response->setData("Your JSON array is empty or wrongly formated");
            }
            else
                return $response->setData("Input file error : File does not exists or is empty");
        }
        else
           return $response->setData("Entity named $entity cannot be manipulated by the API"); 
    }

    /**
    *
    * Edit entities in bulk
    * @Put("/api/edit/{entity}")
    *
    */
    public function editEntitiesAction($entity) {
        $response = new JsonResponse(); // initalize the JSON that will be returned at the end

        if (in_array(strtoupper($entity), $this->ENTITIES_ALLOWED)) {
            $input_content = file_get_contents("php://input"); // get upload file with the http request (ex : curl)

            // get all necessary informations to check if the user has enough rights to edit the entity
            $em = $this->getDoctrine()->getManager();
            $thatRepo = $em->getRepository('TAMASAstroBundle:' . $this->formattedEntityName($entity));
            $user = $this->getUser();

            if ($input_content) {
                $array = json_decode($input_content, true);

                if ($array) {
                    $output_json_array = []; // initialize output JSON array
                    $output_json_array["correctEntities"] = array();
                    $output_json_array["wrongEntities"] = array();

                    if (array_key_exists($entity, $array)) {
                        // if the entity is a library one, it needs a special method, otherwise it's the generic method
                        switch ($entity) {
                            case 'library':
                            case 'historicalactor':
                            case 'primarysource':
                            case 'secondarysource':
                                $entityType = $this->formattedEntityName($entity);
                                break;
                            default :
                                $entityType = 'Entity';
                                break;
                        }
                        $addMethod = "add" . $entityType . "Action";

                        // calls the addMethod for each entity described in the input JSON array
                        foreach ($array[$entity] as $key => $value) { // we get ids from the JSON array as keys to each entities values
                            if(empty($value))
                                continue;

                            if ($this->isInteger($key) && $thatRepo->find((int)$key)) {
                                $creator = $thatRepo->find((int)$key)->getCreatedBy();
                                if ($this->checkEditAccess($creator, $user)) {
                                    $object = $thatRepo->find((int)$key);
                                    if ($object)
                                        $output_json_array = $this->$addMethod($entity, $value, $output_json_array, $object);
                                }
                            }
                        }

                        // returns entities that have been treated
                        return $response->setData($output_json_array);
                    }
                    else 
                        return $response->setData("Your JSON array must have a {" . $entity . "} key but only has {" . implode(array_keys($array)) . "}");
                }
                else
                    return $response->setData("Your JSON array is empty or wrongly formated");
            }
            else
                return $response->setData("Input file error : File does not exists or is empty");
        }
        else
            return $response->setData("Entity named $entity cannot be manipulated by the API"); 
    }

    /**
    *
    * Add an entity
    *
    */
    private function addEntityAction($entity, $value, $output_json_array, $object = null) {
        $object = $object != null ? $this->createEntity($entity, $value, $object) : $this->createEntity($entity, $value); // we try to edit/create the entity

        if ($object instanceof $this->constraintsClassName) { // if we got an array of errors rather than an valid entity
            $value += ["errorMessage" => "Couldn't create or edit this entity with given informations", "DoctrineValidationErrors" => (string)$object];
            array_push($output_json_array["wrongEntities"], $value);
        }
        elseif(is_string($object)) {
            $value += ["errorMessage" => $object];
            array_push($output_json_array["wrongEntities"], $value);
        }
        else {
            $value += ["Entity ID" => $object->getId()];
            array_push($output_json_array["correctEntities"], $value);
        }

        return $output_json_array;
    }

    /**
    *
    * Add a library from an online API (isni.ringgold.com)
    *
    */
    private function addLibraryAction($entity, $value, $output_json_array, $object = null) {
        $raw_content = $this->getWebpages("https://isni.ringgold.com/api/stable/institution/" . $value);
            
        if ($raw_content) {
            $infos_array = json_decode($raw_content, true);

            if (!empty($infos_array) && $infos_array != null) {
                if ($object == null) // if we try to add
                    $object = new \TAMAS\AstroBundle\Entity\Library();

                $object->setLibraryName($infos_array['name']);
                $object->setLibraryCountry($infos_array['country_code']);
                $object->setLibraryIdentifier($infos_array['isni']);
                $object->setCity($infos_array['locality']);

                $errors = $this->container->get('validator')->validate($object); // we try to validate the entity
                if (count($errors) == 0) { // if there's no error sent by the validation
                    $this->persistData($object);
                    $output[] = ["ISNI" => $value, "Library name" => $object->getLibraryName(), "Entity ID" => $object->getId()];
                    array_push($output_json_array["correctEntities"], $output);
                }
                else { 
                    $output[] = ["ISNI" => $value, "errorMessage" => "Informations about this library are incomplete or it already exists in the database", "DoctrineValidationErrors" => (string)$errors];
                    array_push($output_json_array["wrongEntities"], $output);
                }
            }
            else { 
                $output[] = ["ISNI" => $value, "errorMessage" => "Check if your ISNI id is valid and if informations about this library are complete"];
                array_push($output_json_array["wrongEntities"], $output);
            }
        }
        else { 
            $output[] = ["ISNI" => $value, "errorMessage" => "Check if your ISNI id is valid and if isni.ringgold.com is still online"];
            array_push($output_json_array["wrongEntities"], $output);
        }

        return $output_json_array;
    }

    /*
    *
    * When an entity contains another one, takes care of creating the entity or retrieve it if it exists
    * If it can't, it will send an array with the reasons
    *
    */
    private function getEntityForParentEntity($entity, $infos, $output_json_array) {
        $output = [];

        if (ctype_digit($infos) || is_int($infos)) { // if it's an id of an existing entity
            $object = $this->selectData(intval($infos), $entity);
            if (!$object) {
                $output[] = ["errorMessage" => "Couldn't find a $entity with this id"];
                return $output;
            }
        }
        elseif (is_array($infos)) { // if it's an array of info to create a new entity
            $object = $this->createEntity($entity, $infos, null, true);
            if ($object instanceof $this->constraintsClassName) { // if we got an array of the validation errors
                $output[] = ["errorMessage" => "Array is not valid or this $entity exists already", "DoctrineValidationErrors" => (string)$object];
                return $output;
            }
            elseif(is_string($object)) {
                $output[] = ["errorMessage" => $object];
                return $output;
            }
        }
        else { // if it's none of the above, we can't add this entity
            $output[] = ["errorMessage" => "Historian values should be an id of an existing $entity or an array to create a new one"];
            return $output;
        }

        return $object;
    }

    /*
    *
    * Add a Historical Actor
    *
    */
    private function addHistoricalActorAction($entity, $value, $output_json_array, $object = null) {
        // if we try to add
        if ($object == null)
            $object = new \TAMAS\AstroBundle\Entity\HistoricalActor(); // we create a new object

        // for each field of the current object
        foreach ($value as $key => $info) {
            if(strcmp($key, "_comment") == 0) 
                continue;

            if (strcmp(ucfirst($key), "Place") == 0) { // if the field we're filling is place
                // we try to create or get the entity from the db
                $place = $this->getEntityForParentEntity('place', $info, $output_json_array);

                // if we get an array of errors
                if (is_array($place)) {
                    $value += $place;
                    array_push($output_json_array, $value);
                    return $output_json_array;
                } 
                else $object->setPlace($place);
            }
            else {
                $method = "set" . ucfirst($key); // we chose the field to edit
                if (empty($info)) $info = null;

                // if a field doesn't exist (for pbs like : typos)
                if (!method_exists($object, $method)) {
                    $value += ["errorMessage" => $key . " : This field does not exist"]; 
                    array_push($output_json_array, $value);
                    return $output_json_array;
                }

                // check if it's not a protected field
                if (method_exists($object, $method) && !in_array(strtoupper($key), $this->FORBIDDEN_FIELDS)) 
                    $object->$method($info); // on set le champ a la valeur defini dans le fichier
            }
        }

        $errors = $this->container->get('validator')->validate($object); // we try to validate the entity
        if (count($errors) == 0) { // if there's no error sent by the validation and tpq <= taq
            $this->persistData($object); // we add the entity to the db
            $value += ["Entity ID" => $object->getId()];
            array_push($output_json_array["correctEntities"], $value);
        }
        else {
            $value += ["errorMessage" => "Either missing necessary fields or has invalid years (check TPQ and TAQ)", "DoctrineValidationErrors" => (string)$errors];
            array_push($output_json_array["wrongEntities"], $value);
        }

        return $output_json_array;
    }

    /*
    *
    * Add a primary source 
    *
    */
    private function addPrimarySourceAction($entity, $value, $output_json_array, $object = null) {
        // if we try to add
        if ($object == null)
            $object = new \TAMAS\AstroBundle\Entity\PrimarySource(); // we create a new object

        // for each field of the current object
        foreach ($value as $key => $info) {
            if(strcmp($key, "_comment") == 0) 
                continue;

            if (strcmp(ucfirst($key), "Library") == 0) { // if the field we're filling is library
                // we try to create or get the entity from the db
                if (ctype_digit($info) || is_int($info)) { // if it's an id of an existing library
                    $library = $this->selectData(intval($info), 'library');
                    if (!$library) {
                        $value += ["errorMessage" => "Couldn't find an existing library with this id"];
                        array_push($output_json_array["wrongEntities"], $value);
                        return $output_json_array;
                    }
                }
                elseif (is_array($info)) { // if it's an array of info to create a new library
                    if (array_key_exists('isni', $info)) {
                        $library = $this->createLibrary($info['isni'], true);
                        if ($library instanceof $this->constraintsClassName || !$library) { // if we got an array of the validation errors
                            $value += ["errorMessage" => "Couldn't find a library with the following ISNI or it already exists : $info[isni]", "DoctrineValidationErrors" => (string)$library];
                            array_push($output_json_array["wrongEntities"], $value);
                            return $output_json_array;
                        }
                    }
                    else {
                        $value += ["errorMessage" => "Your array for the library must contain a 'isni' key, with the valid ISNI as its value"];
                        array_push($output_json_array["wrongEntities"], $value);
                        return $output_json_array;
                    }
                }
                else { // if it's none of the above, we can't add this historical actor
                    $value += ["errorMessage" => "Library should be an id of an existing library or an ISNI to create a new one"];
                    array_push($output_json_array["wrongEntities"], $value);
                    return $output_json_array;
                }
                
                $object->setLibrary($library);
            }
            else {
                $method = "set" . ucfirst($key); // we chose the field to edit
                if (empty($info)) $info = null;

                // if a field doesn't exist (for pbs like : typos)
                if (!method_exists($object, $method)) {
                    $value += ["errorMessage" => $key . " : This field does not exist"]; 
                    array_push($output_json_array, $value);
                    return $output_json_array;
                }

                // check if it's not a protected field
                if (method_exists($object, $method) && !in_array(strtoupper($key), $this->FORBIDDEN_FIELDS)) 
                    $object->$method($info); // on set le champ a la valeur defini dans le fichier
            }
        }

        $errors = $this->container->get('validator')->validate($object); // we try to validate the entity
        if (count($errors) == 0) { // if there's no error sent by the validation
            $this->persistData($object); // we add the entity to the db
            $value += ["Entity ID" => $object->getId()];
            array_push($output_json_array["correctEntities"], $value);
        }
        else { 
            $value += ["errorMessage" => "Missing necessary fields", "DoctrineValidationErrors" => (string)$errors];
            array_push($output_json_array["wrongEntities"], $value);
        }

        return $output_json_array;        
    }

    /*
    *
    * Add a secondary source
    *
    */
    private function addSecondarySourceAction($entity, $value, $output_json_array, $object = null) {
        // if we try to add
        if ($object == null)
            $object = new \TAMAS\AstroBundle\Entity\SecondarySource(); // we create a new object

        // we set the type of the secondary source beforehand (needed to know if we can add some values)
        if (array_key_exists('secType', $value))
            $object->setSecType($value['secType']);
        else {
            $value += ["errorMessage" => "Secondary Source Type must be defined"];
            array_push($output_json_array, $value);
            return $output_json_array;
        }

        // for each field of the current object
        foreach ($value as $key => $info) {
            if(strcmp(ucfirst($key), "SecType") == 0 || strcmp($key, "_comment") == 0) 
                continue;

            if (strcmp(ucfirst($key), "Historian") == 0) { // if the field we're filling is historian
                if (is_array($info)) { // if it's an array of historians
                    foreach ($info as $currentLine) {
                        // we try to create or get the entity from the db
                        $historian = $this->getEntityForParentEntity('historian', $currentLine, $output_json_array);

                        // if we get an array of errors
                        if (is_array($historian)) {
                            $value += $historian;
                            array_push($output_json_array, $value);
                            return $output_json_array;
                        } 
                        else $object->addHistorian($historian);
                    }
                }
                else { // if it's only a single historian
                    // we try to create or get the entity from the db
                    $historian = $this->getEntityForParentEntity('historian', $info, $output_json_array);

                    // if we get an array of errors
                    if (is_array($historian)) {
                        $value += $historian;
                        array_push($output_json_array, $value);
                        return $output_json_array;
                    }
                    else $object->addHistorian($historian);
                }
            }
            elseif(strcmp(ucfirst($key), "Journal") == 0) {
                if (strcmp($object->getSecType(), "journalArticle") == 0) {
                    // we try to create or get the entity from the db
                    $journal = $this->getEntityForParentEntity('journal', $info, $output_json_array);

                    // if we get an array of errors
                    if (is_array($journal)) {
                        $value += $journal;
                        array_push($output_json_array, $value);
                        return $output_json_array;
                    }
                    else $object->setJournal($journal);
                }
                else {
                    $value += ["errorMessage" => "This secondary source should be a Journal Article if you try to add a Journal"];
                    array_push($output_json_array["wrongEntities"], $value);
                    return $output_json_array;
                }
            }
            elseif(strcmp(ucfirst($key), "CollectiveBook") == 0) {
                if (strcmp($object->getSecType(), "bookChapter") == 0) {
                    if (ctype_digit($info)) { // if it's an id of an existing secondary source
                        $collectiveBook = $this->selectData(intval($info), 'secondarySource');
                        if (!$collectiveBook) {
                            $value += ["errorMessage" => "Couldn't find an existing book with this id"];
                            array_push($output_json_array["wrongEntities"], $value);
                            return $output_json_array;
                        }
                        else $object->setCollectiveBook($collectiveBook);
                    }
                    else { // if it's none of the above, we can't add this secondary source
                            $value += ["errorMessage" => "Book value should be an id of an existing secondary source"];
                            array_push($output_json_array["wrongEntities"], $value);
                            return $output_json_array;
                    }
                }
                else {
                    $value += ["errorMessage" => "This secondary source should be a Book Chapter if you try to add a Book Title"];
                    array_push($output_json_array["wrongEntities"], $value);
                    return $output_json_array;
                }
            }
            else {
                $method = "set" . ucfirst($key); // we chose the field to edit
                if (empty($info)) $info = null;

                // if a field doesn't exist (for pbs like : typos)
                if (!method_exists($object, $method)) { 
                    $value += ["errorMessage" => $key . " : This field does not exist"]; 
                    array_push($output_json_array, $value);
                    return $output_json_array;
                }

                // check if it's not a protected field
                if (!in_array(strtoupper($key), $this->FORBIDDEN_FIELDS)) 
                    $object->$method($info); // setting the field to the value from the json array
            }
        }

        $errors = $this->container->get('validator')->validate($object); // we try to validate the entity
        if (count($errors) == 0) { // if there's no error sent by the validation
            $this->persistData($object); // we add the entity to the db
            $value += ["Entity ID" => $object->getId()];
            array_push($output_json_array["correctEntities"], $value);
        }
        else {
            $value += ["errorMessage" => "Missing necessary fields", "DoctrineValidationErrors" => (string)$errors]; 
            array_push($output_json_array["wrongEntities"], $value);
        }

        return $output_json_array;        
    }

    /*
    *
    * @Get("/truc")
    *
    */
    public function getTestAction() {
        $response = new JsonResponse(); // initalize the JSON that will be returned at the end
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "localhost:9200/place");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, "elastic:2erPAcQzfPgrxtpn2UFM");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($ch);

        $response->setData($output);
        curl_close($ch);
        */

        /*
        $monfichier = fopen('preserialize.txt', 'w+');
        fputs($monfichier, "preserializé");
        fclose($monfichier);
        */

        /*
        $client = new \Elastica\Client(array("username" => "mhamelin", "password" => "generation"));

        $search = new \Elastica\Search($client);
        $search->addIndex('place');

        $query = new \Elastica\Query();
        $query->setSize(100);
        $query->setSource(['place_lat']);

        //$search->setQuery($query);

        $results = $search->search();
        $a = [];

        foreach ($results as $res) {
            $a[] = $res->getData();
        }
        */

        $expressionLanguage = new ExpressionLanguage();
        $a = 'false';

        $response->setData($expressionLanguage->evaluate($a));
        return $response;
    }

}

?>