<?php

namespace TAMAS\ALFABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LoadXMLController extends Controller {

    public function transformXMLAction($id) {

        /* ========================================== Partie procédurale : utilisation des fonctions préalablements crées ======================== */

        $msFileName = $this->getDoctrine()->getManager()->getRepository(\TAMAS\ALFABundle\Entity\ALFAXMLFile::class)->find($id)->getFileName();
        $msEncoded = simplexml_load_file('./xml/manuscriptDescriptionTEI/' . $msFileName); //transformation de mon doc TEI en un gros objet PHP (simpleXML). 

        foreach ($msEncoded->xpath('//hi') as $hi) {//Ajout des futures balises HTML pour la mise en forme de certains aspects éditoriaux: il s'agit des seuls éléments de mise en page à proprement parler
            $rend = $hi['rend'];
            switch ($rend) {
                case "sup":
                    $element = "sup";
                    break;
                case "italic":
                    $element = "i";
                    break;
                case "bold":
                    $element = "b";
                    break;
                default :
                    $element = "span";
                    break;
            }
            $hiText = (string) $hi;

            //echo $element." => ".$hiText."<br/>";
            $hi[0] = "<" . $element . ">" . $hiText . "</" . $element . ">";
        }
        

        \TAMAS\ALFABundle\XMLEntity\XMLEntity::$msEncoded = $msEncoded;
        \TAMAS\ALFABundle\XMLEntity\XMLEntity::fillXMLEntities();
        $manuscript = new \TAMAS\ALFABundle\XMLEntity\ManuscriptXML($msEncoded);
        //$json_manuscript = json_encode(get_object_vars($manuscript));
        $jsonStructure = $manuscript->getJsonStructure();

        $indexes = \TAMAS\ALFABundle\XMLEntity\XMLEntity::$personsXML;
//        foreach ($msEncoded->xpath('//person') as $person) {
//            $indexes[] = new \TAMAS\ALFABundle\XMLEntity\PersonXML($person);
//        }

        $hands = \TAMAS\ALFABundle\XMLEntity\XMLEntity::$handsXML;
//        foreach ($msEncoded->xpath('//handNote') as $hand) {
//            $hands[] = new \TAMAS\ALFABundle\XMLEntity\HandXML($hand);
//        }
        $notes = \TAMAS\ALFABundle\XMLEntity\XMLEntity::$notesAndFigures;


        //Les données sont renvoyées à la vue. 
        $personByRole = [];
        foreach ($indexes as $person) {
            $role = $person->getRole();
            $personByRole[$role][] = $person;
        }

        $citations = \TAMAS\ALFABundle\XMLEntity\XMLEntity::$citationsXML;
        $orderedCitation = [];
        foreach ($citations as $citation) {
            $authors = [];
            foreach ($citation->getAuthors() as $author) {
                $authors[] = ['family' => $author['lastName'], 'given' => $author['firstName']];
            }
            $orderedCitation['items'][] = [
                'id' => $citation->getId(),
                'type' => 'book',
                'title' => $citation->getTitle(),
                'author' => $authors,
                'issued' => ['raw' => $citation->getDate()],
                'editor' => [['family' => $citation->getEditor()]],
                'publisher' => [[$citation->getPublisher()]],
                'publisher-place' => [[$citation->getPubPlace()]]
            ];
        }
        $jsonCitation = json_encode($orderedCitation);

        $personByOrderedRole = ['copyist' => $personByRole['copyist'], 'author' => $personByRole['author'], 'owner' => $personByRole['owner'], 'other' => $personByRole['other']];
        return $this->render('TAMASALFABundle:View:template_ms_description.html.twig', ['manuscript' => $manuscript, 'jsonStructure' => $jsonStructure, 'indexes' => $personByOrderedRole, 'hands' => $hands, 'citation' => $jsonCitation, 'notes' => json_encode($notes)]);
    }

}
