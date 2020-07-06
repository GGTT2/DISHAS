<?php

namespace TAMAS\AstroBundle\Controller;

use Exception;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Security\ExpressionLanguage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use TAMAS\AstroBundle\Entity\TableContent;
use Symfony\Component\Yaml\Yaml;
use TAMAS\AstroBundle\Entity\EditedText;
use TAMAS\AstroBundle\Entity\TableType as EntityTableType;

/**
 * PythonController
 * 
 * Controller for executing a python script on the server.
 */
class PythonController extends Controller
{

    /**
     * This method generate a serialized image of the table content according to the same serialization rules as Elasticsearch
     * It is used mainly to generate a downloadable json file for DIPS compatibility.
     * 
     * @param {Request} $request 
     * @param {int} $id : the id of the table content, if we are in the backoffice DTI
     * @param {int} $ttid : the id of the table type of the table content, if we are in the frontoffice DTI
     * 
     */
    public function ajaxExportJSONAction(Request $request, $id = null, $ttid = null)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            // 1. We get the serialization rules for tableContent from the Yalm file    
            $projectDir = $this->get('kernel')->getProjectDir();
            $yamlFile = Yaml::parse(file_get_contents($projectDir . '/app/config/config.yml'));
            $indexInfos = $yamlFile['fos_elastica']['indexes'];
            $groups = $indexInfos['table_content']['types']['table_content']['serializer']['groups'];
            $serializer = SerializerBuilder::create()
                ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
                ->build();

            // 2. Generation of the json file

            // CASE ONE : IT COMES FROM THE PUBLIC SIDE
            if (!$id) { // we need to create a fake table in order to serialize it
                $tableContent = new TableContent();
                $form = $this->get('form.factory')->create(\TAMAS\AstroBundle\Form\TableContentType::class, $tableContent, [
                    'em' => $em,
                    'user' => null
                ]);
                if ($request->isMethod('POST')) {
                    $tableType = $em->getRepository(EntityTableType::class)->find($ttid);
                    $tableContent->setEditedText(new EditedText());
                    $tableContent->setTableType($tableType);
                    $form->handleRequest($request);
                }
            } else { // CASE TWO : IT COMES FROM THE PRIVATE SIDE
                $tableContent = $em->getRepository(TableContent::class)->find($id);
                if ((!$tableContent->getPublic()) && ($this->getUser() !== $tableContent->getCreatedBy() && !$this->getUser()->hasRole("ROLE_SUPER_ADMIN"))) {
                    $response = new JsonResponse("This table is not public yet and cannot be exported in this format.");
                    return $response;
                }
            }
            $serialized = $serializer->serialize($tableContent, "json", SerializationContext::create()->setGroups($groups));
            
            //3. Sending the response
            $response = new JsonResponse();
            $response = JsonResponse::fromJsonString($serialized);
            return $response;
        } catch (Exception $e) {
            $response = new JsonResponse("Something went wrong. Please contact DISHAS team #JSONExportError");
            return $response;
        }
    }

    /**
     * This method is used for most case of the export. 
     */
    public function ajaxExportAction(Request $request)
    {
        // We only process POST requests
        if (!$request->isMethod('POST')) {
            $response = new Response();
            $response->setContent('');
            return $response;
        }

        /* ===============================================
            Here we process the post data to retrieve
            the arguments that will be provided to
            the command line tool (dist/tableExport)
         * =============================================== */

        $option = $request->request->get('option');
        $options = json_decode($option, true);

        $filename = null;
        if (array_key_exists('filename', $options)) {
            $filename = $options['filename'];
        }

        chdir('pythonScript');

        // proc_open bug quand on lui passe une ligne de commande trop longue...
        // on enregistre donc la table dans un fichier temporaire

        $tmpfname = tempnam(".", "table_content_");
        $handle = fopen($tmpfname, "w");

        /* ***************************** Managing the table content data ******************************************/

        //TODO : if the requested format is JSON; just use JMS serizaliser or the ElasticSearialization end over. 

        //CASE 1: the table comes from the "public" interface. 
        if (array_key_exists('tableContentJSON', $options)) {
            // if the JSON of the table has been provided in the post data
            // (i.e. if we come from the public interface), then we use it to build
            // the argument
            $valueOriginal = $options['tableContentJSON']['original'];
            $valueFloat = $options['tableContentJSON']['decimal'];
            $differenceValueOriginal = "";
            $differenceValueFloat = "";
            $correctedValueFloat = $options['tableContentJSON']['corrected'];
            if (array_key_exists('differenceOriginal', $options['tableContentJSON'])) {
                $differenceValueOriginal = $options['tableContentJSON']['differenceOriginal'];
            }
            if (array_key_exists('differenceDecimal', $options['tableContentJSON'])) {
                $differenceValueFloat = $options['tableContentJSON']['differenceDecimal'];
            }
            $template = $options['tableContentJSON']['template'];
            $mathematicalParameterSet = $options['tableContentJSON']['mathematicalParameterId']; // mettre vide si n'existe pas comme cas 2?
            $astronomicalParameterSets = $options['tableContentJSON']['astronomicalParameterIds']; // mettre vide si n'existe pas comme cas 2?
            $editedText = (string) $options['tableContentJSON']['editedText']; // souvent null dans l'interface de saisie !!
            $userId = (string) $options['tableContentJSON']['userId'];
            $username = $options['tableContentJSON']['username'];
        } //CASE 2: the table comes from the "admin" interface.
        else {
            // if the JSON of the table content has not been provided,
            // we expect a tableContent id. The argument is then built
            // from the corresponding tableContent in the dataBase.
            $id = $options['tableContent'];
            $em = $this->getDoctrine()->getManager();
            $tableContent = $em->getRepository('TAMASAstroBundle:TableContent')->find($id);

            $valueOriginal = $tableContent->getValueOriginal();
            $valueFloat = $tableContent->getValueFloat();
            $differenceValueOriginal = $tableContent->getDifferenceValueOriginal();
            $differenceValueFloat = $tableContent->getDifferenceValueFloat();
            $correctedValueFloat = $tableContent->getCorrectedValueFloat();
            $template = $tableContent->getTemplate();
            $mathematicalParameterSet = "";
            if ($tableContent->getMathematicalParameter()) {
                $mathematicalParameterSet = (string) $tableContent->getMathematicalParameter()->getId();
            }
            $astronomicalParameterSets = array();
            $aps = $tableContent->getParameterSets();
            foreach ($aps as $index => $ap) {
                $astronomicalParameterSets[] = (string) $ap->getId();
            }
            $editedText = "";
            if ($tableContent->getEditedText() != null) {
                $editedText = (string) $tableContent->getEditedText()->getId();
            }

            $userId = (string) $tableContent->getCreatedBy()->getId();
            $username = $tableContent->getCreatedBy()->getUsername();
        }

        /* ******************************************** Managing the export spec ****************************** */

        // depending on the export options, we store the correct value in the
        // temporary file.
        if ($options['exportFormat'] == 'json') {
            $json_db = [];
            $json_db['original'] = $valueOriginal;
            $json_db['float'] = $valueFloat;
            if ($differenceValueOriginal != "") {
                $json_db['differenceOriginal'] = $differenceValueOriginal;
            }
            if ($differenceValueFloat != "") {
                $json_db['differenceFloat'] = $differenceValueFloat;
            }
            $json_db['template'] = $template;
            $json_db['table_type'] = $template['table_type'];
            $json_db['mathematical_parameter_set'] = $mathematicalParameterSet;
            $json_db['astronomical_parameter_sets'] = $astronomicalParameterSets;
            $json_db['edited_text'] = $editedText;
            $json_db['corrected'] = $correctedValueFloat;

            $json_db['user_id'] = $userId;
            $json_db['user_name'] = $username;
            fwrite($handle, json_encode($json_db));
        } else {
            if ($options['exportOptions']['export-asFloat'] && !$options['exportOptions']['export-difference']) {
                fwrite($handle, json_encode($valueOriginal)); // NOTE : the table is actually converted by the python script
            } else if (!$options['exportOptions']['export-difference']) {
                fwrite($handle, json_encode($valueOriginal));
            } else if ($options['exportOptions']['export-asFloat'] && $options['exportOptions']['export-difference']) {
                fwrite($handle, json_encode($differenceValueOriginal));
            } else if ($options['exportOptions']['export-difference']) {
                fwrite($handle, json_encode($differenceValueOriginal));
            } else {
                fwrite($handle, json_encode($valueOriginal));
            }
        }

        // because there is a limit to the size of the argument we can provide
        // to open_proc, the content of the table is stored in a temporary file
        // and we only pass the name of this file to the script
        $options['tableContent'] = $tmpfname;
        unset($options['tableContentJSON']);

        fclose($handle);

        /* ===============================================
            The arguments have been created.
            We proceed to the command line call.
         * =============================================== */

        $argument = json_encode($options);
        $commandline = "./dist/tableExport '" . $argument . "'";

        $pipes = array();
        // We create unix pipes where the outputs of the command line will be stored

        /* ===============================================
           As we create and return a file directly from the standard output
           of the command line (stdout), the stdout pipe MUST BE OPENED AS A BINARY,
           with the "wb" option.
         * =============================================== */
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "wb"),  // stdout
            2 => array("pipe", "w"),  // stderr
        );

        // actual call to the command line
        $process = proc_open($commandline, $descriptorspec, $pipes);

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);

        // remove temporary file
        unlink($tmpfname);

        chdir('..');

        if ($returnCode == 0) {
            // if there was no error, we return the resulting output as a file
            if ($filename == null) {
                $filename = "download.txt";
            }
            $response = new Response($stdout);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );
            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        } else {
            // if there was an error, we return an error file containing the stderr
            // to be disabled in prod when the export tools are finished.
            $filename = "error.txt";
            $response = new Response($commandline . "\n-------- \n" . $stdout . "\n-------- \n" . $stderr);
            $disposition = $response->headers->makeDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $filename
            );
            $response->headers->set('Content-Disposition', $disposition);
            return $response;
        }
    }

    /**
     * ajaxAutoToolPythonAction
     * 
     * Process an ajax request: execute the corresponding python script (web/pythonscripts/$scriptName.py)
     * against the given command-line arguments (in $options), and returns the result.
     * Command line arguments and results must be expressed as valid JSON strings
     * THIS IS NOT USED CURRENTLY, AND PROBABLY NOT WORKING ANYMORE.
     * 
     * @param Request $request
     * @param string $scriptName
     * @param string $option
     * @return JsonResponse
     */
    public function ajaxAutoToolPythonAction(Request $request, $scriptName, $option = null)
    {
        $commandline = "cd pythonScript && python3 main.py '" . $scriptName . "' '" . $option . "'";
        //$commandline = "fakeroot fakechroot chroot --userspec=7001 /home/jail /usr/bin/python3.5 /home/user1/'".$scriptName."' ".$option;

        //$result = array();
        //exec($commandline,$result);
        $pipes = array();
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin
            1 => array("pipe", "w"),  // stdout
            2 => array("pipe", "w"),  // stderr
        );
        $process = proc_open($commandline, $descriptorspec, $pipes);

        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $returnCode = proc_close($process);
        if ($returnCode == 0) {
            $response = new JsonResponse();
            return $response->setData($stdout);
        } else {
            $response = new JsonResponse(json_encode(array('error' => $stderr)));
            return $response;
        }
    }
}
