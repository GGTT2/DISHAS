<?php

namespace TAMAS\AstroBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vich\UploaderBundle\Mapping;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use TAMAS\AstroBundle\Entity\PythonScript;
use TAMAS\AstroBundle\Entity\TableContent;
use Doctrine\ORM\EntityManagerInterface;


class TableContentTest extends WebTestCase
{

    public function printTestAction(EntityManagerInterface $em, Request $request, ValidatorInterface $validator)
    {
        $tableContentRepo = $em->getRepository('TAMASAstroBundle:TableContent');
        $withCollectedYears = $tableContentRepo->find(113);
        $notmm = $tableContentRepo->find(111);
        $mm = $tableContentRepo->find(12);
/* 
        //TEST 1. check if we can add other table content to notMM parent edited text 
        $editedText1 = $notmm->getEditedText();
        $newTableContent = new TableContent();
        $editedText1->addTableContent($newTableContent);
        $errorsEmptyTC = $validator->validate($newTableContent); // should not be valid: not enough metadata; ===> TRUE
        //comment the preceding line to go on with the test.
        $tt = $editedText1->getTableType();
        $newTableContent->setTableType($tt);
        $errorsEmptyTC = $validator->validate($newTableContent); // should not be valid + raise exception: "No extra table content allowed for this type of table"; ===> TRUE
        //comment the preceding line to go on with the test.
        $errorsET = $validator->validate($editedText1); // should not be valid (warning on form): the edited text cannot have 2 table content; ====> TRUE (in the error, not thrown exception)
        $errorsTC = $validator->validate($notmm); // should not be valid + raise exception:"No extra table content allowed for this type of table"; ==> TRUE
        //comment the preceding line to go on with the test.

        var_dump($errorsEmptyTC);
        var_dump($errorsET);
        var_dump($errorsTC);


*/        

/*         //TEST 2. check if we can add astronomical parameter to $mm and $notmm (devrait être faux) 
         //   Check if we can remove all astro paramater;
        $meanMotion = $mm->getMeanMotion();
        $meanMotion->setPlaceNameTranslit('coucou');
        $meanMotion->setPlaceNameOriginalChar('coucou');
        $meanMotion->setLongOrigBase('coucou');
        $meanMotion->setLongFloat('12.3');
        $meanMotion->setMeridian('coucou');
        $meanMotion->setMeridianOriginalChar('coucou');
        $meanMotion->setMeridianTranslit('coucou');
        $meanMotion->setRootOrigBase ('coucou');
        $meanMotion->setRootFloat ('10');
        $meanMotion->setEpoch ('22');
        echo $meanMotion->hasLocalizationParameters(); //Should be TRUE ===> TRUE
        $errors = $validator->validate($mm); // should not be valid ==> TRUE
        var_dump($errors); // ==> Only "collected years" mean motion can accept localization parameters. They will be automatically removed. You can save again the document. ===> TRUE
        var_dump($meanMotion->hasLocalizationParameters());// should be null ===> TRUE;

        //$meanMotion = $notmm->getMeanMotion()->setPlaceName('coucou'); // should raise an exception : Call to a member function setPlaceName() on null ====> TRUE
 */
      

/*         //3. check if we can add mean motion to notmm

        $meanMotion = new MeanMotion();
        $notmm->setMeanMotion($meanMotion); // should not be OK and raise an exception: This table type is not linkable with mean-motion table ===> TRUE 
        var_dump($validator->validate($notmm)); */
       

        //4. Check if we can save edited text of mm without a calendar. 
/*         $editedText = $mm->getEditedText();
        $editedText->setEra(null);
        var_dump($validator->validate($editedText)); //should not be OK : lease fill the sub-calendar type ===> TRUE
        die; */

/*         //5. Check that the format of localization parameters are ok
        $meanMotion = $withCollectedYears->getMeanMotion();
        echo $meanMotion->getSubTimeUnit()->getId();
        $meanMotion->setPlaceNameTranslit(123); // should not be OK : This value should be of type string. ==> TRUE
        $meanMotion->setPlaceNameOriginalChar(123);  // should not be OK : This value should be of type string. ==> TRUE
        $meanMotion->setLongOrigBase(13);
        $meanMotion->setLongFloat('coucou'); //should not be OK : 'The value "coucou" is not a valid numeric.' ===> TRUE
        $meanMotion->setMeridian(123); // should not be OK : This value should be of type string. ==> TRUE
        $meanMotion->setMeridianOriginalChar(123);
        $meanMotion->setMeridianTranslit(123); // should not be OK : This value should be of type string. ==> TRUE
        $meanMotion->setRootOrigBase (123); // should not be OK : This value should be of type string. ==> TRUE
        $meanMotion->setRootFloat ("coucou"); //should not be OK: 'The value "coucou" is not a valid numeric.'  ==> true
        $meanMotion->setEpoch ("123$"); // should not be OK: The value "123$" is not a valid numeric.

        $errors = $validator->validate($meanMotion); // should not be valid ==> TRUE
        var_dump($errors);  */


        //6. Check that the remaining data for arg 2 and 3 are deleted if the number of args is inferior
        //this routine is supposed to be managed by the entity when validating
        /*
                $tableContentRepo = $em->getRepository('TAMASAstroBundle:TableContent');
        $toCorrect = $tableContentRepo->find(X); // change X for a test table in which the argument nb is 1, but the number unit for arg2 and 3 is manually added : reproduction of the natural behaviour of the front interface. 
        if ($toCorrect->getArgument2NumberUnit()) {
            echo $toCorrect->getArgument2NumberUnit()->getId();
            echo "<br/>";
        }
        if ($toCorrect->getArgument3NumberUnit()) {
            echo $toCorrect->getArgument3NumberUnit()->getId();
            echo "<br/>";

        }
        var_dump($validator->validate($toCorrect));

        if ($toCorrect->getArgument2NumberUnit()) {
            echo $toCorrect->getArgument2NumberUnit()->getId(); ==> should be null =====> TRUE
            echo "<br/>";
        }
        if ($toCorrect->getArgument3NumberUnit()) {
            echo $toCorrect->getArgument3NumberUnit()->getId(); ==> should be null =====> TRUE
        }
*/
        die;
    }
}