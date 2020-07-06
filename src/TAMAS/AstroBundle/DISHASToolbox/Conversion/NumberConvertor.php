<?php
namespace TAMAS\AstroBundle\DISHASToolbox\Conversion;

// use \Doctrine\ORM\EntityManagerInterface;

class NumberConvertor
{

    private $em;

    private $bases = [];

    function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        $this->em = $em;
        $typeOfNumbers = $em->getRepository(\TAMAS\AstroBundle\Entity\TypeOfNumber::class)->findAll();
        $classicArray = [];
        $separatorArray = [];
        foreach ($typeOfNumbers as $typeOfNumber) {
            $classicArray[$typeOfNumber->getCodeName()] = json_decode($typeOfNumber->getBaseJSON(), 1);
            if ($typeOfNumber->getIntegerSeparatorJSON() === null) {
                $separatorArray[$typeOfNumber->getCodeName()] = null;
            } else {
                $separatorArray[$typeOfNumber->getCodeName()] = json_decode($typeOfNumber->getIntegerSeparatorJSON(), 1);
            }
        }
        foreach ($classicArray as $key => $baseCouple) {
            $left = new RadixBase();
            $left->fromArray($baseCouple[0]);
            $right = new RadixBase();
            $right->fromArray($baseCouple[1]);
            if ($separatorArray[$key] == null) {
                $this->bases[$key] = [
                    "left" => $left,
                    "right" => $right,
                    "name" => $key,
                    'integerSeparator' => []
                ];
            } else {
                $this->bases[$key] = [
                    "left" => $left,
                    "right" => $right,
                    "name" => $key,
                    'integerSeparator' => $separatorArray[$key]
                ];
            }
        }

        // $test = new NumberView([1, 7, 0, 1, 4],[7,30,40,23,17,15,23],2.1,-1,$this->getBases()["historical"]);
        /*
         * $newtest = NumberView::float64ToBase(2.50833533333,$this->getBases()["sexagesimal"],15);
         * var_dump($test);
         * $test->sanitize();
         * var_dump($test);
         * var_dump($newtest->toFloat64());
         * var_dump($test->toFloat64());
         * $test->resize(2);
         * var_dump($test);
         * var_dump($test->toFloat64());
         * $test->resize(3);
         * var_dump($test);
         * $test->resize(6);
         * //$test->round();
         * var_dump($test->toFloat64());
         * //$test->simplifyIntegerPart();
         * var_dump($test);
         * var_dump($newtest);
         * var_dump($newtest->toBase($this->getBases()["decimal"], 15));
         */
        /*
         * var_dump($test->toString());
         * var_dump(NumberView::fromString('-101 R 2 s12 ; 22,45 , 47 , 7, 30, 10', $this->getBases()["historical"]));
         * die;
         */

        /*
         * $test = new NumberView([1,7],[20,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,5,0],0.0,1,$this->getBases()["sexagesimal"]);
         * $test1 = $test->toBase($this->getBases()["decimal"], 200);
         * $test2 = $test1->toBase($this->getBases()["sexagesimal"], 100);
         *
         * $test1->round();
         * $test2->round();
         * var_dump($test1);
         * var_dump($test2);
         * die;
         */
    }

    public function fromString($string, $baseName)
    {
        return NumberView::fromString($string, $this->getBases()[$baseName]);
    }

    public function getBases()
    {
        return $this->bases;
    }

    static public function atIndexInBase($index, $base)
    {
        if ($index >= count($base)) {
            return $base[count($base) - 1];
        }
        return $base[$index];
    }
}






// class SmartNumber {
    
    
//     static private function baseToDecimal($n) {
        
//     }
// }


