<?php



namespace TAMAS\AstroBundle\DISHASToolbox\Conversion;
function sign($x)
{
    if ($x > 0) {
        return 1.0;
    } elseif ($x < 0) {
        return - 1.0;
    }
    return 0.0;
}

function ndigitForRadix($radix)
{
    return ceil(log10($radix));
}


/**
 * NumberView
 *
 * Representation of a number in a given radix base (i.e. typeOfNumber)
 *
 * $leftList represents the list of "digits" for the integer part
 * $rightList represents the list of "digits" for the fractionnal part
 *
 * $leftList is indexed from left to right
 * $rightList is indexed from right to left
 * so that [n1,n2,0,0,0];[f1,f2,0,0,0] === [n1,n2];[f1;f2]
 * For example the decimal representation of 145.758 would be
 *      $leftList = [5,4,1]
 *      $rightList = [7,5,8]
 *
 * The number of significant places is given by the length of $rightList
 *
 * When a number is converted to a base with a fixed precision (a maximum length for $rightList),
 * the remainder is store in $remainder.
 *
 * $base is the array representation of the base.
 * $sign is the sign of the number.
 *
 */
class NumberView {
    public $leftList;
    public $rightList;
    public $remainder;
    public $sign;
    
    private $base;
    
    public function __construct($leftList, $rightList, $remainder, $sign, $base) {
        $this->leftList = $leftList;
        $this->rightList = $rightList;
        $this->remainder = $remainder;
        $this->sign = $sign;
        $this->base = $base;
    }
    
    public function toString() {
        if($this->sign < 0) {
            $res = "-";
        }
        else {
            $res = "";
        }
        
        for($i=count($this->leftList)-1; $i>=0; $i--) {
            
            $num = $this->leftList[$i];
            $digit = ndigitForRadix($this->base['left'][$i]);
            while(strlen($num) < $digit) {
                $num = '0'.$num;
            }
            
            $res .= $num;
            
            if($i != 0) {
                if($i-1 < count($this->base['integerSeparator']) && $i-1 >= 0) {
                    $res .= $this->base['integerSeparator'][$i-1];
                }
                else {
                    if($this->base['left'][$i] != 10) {
                        $res .= ',';
                    }
                }
            }
        }
        if($this->base->name != "decimal") {
            $res .= " ; ";
        }
        else {
            $res .= " . ";
        }
        for($i=0; $i<count($this->rightList); $i++) {
            //$res .= $this->rightList[$i];
            $num = $this->rightList[$i];
            $digit = ndigitForRadix($this->base['right'][$i]);
            while(strlen($num) < $digit) {
                $num = '0'.$num;
            }
            
            $res .= $num;
            
            if($i != count($this->rightList)-1) {
                if($this->base->name != "decimal") {
                    $res .= ',';
                }
            }
        }
        return $res;
    }
    
    static public function fromString($string, $base) {
        $string = strtolower(trim($string));
        if($string === "") {
            throw new NumberViewException("String is empty");
        }
        if (substr($string, 0, 1) === '-') {
            $sign = -1;
            $string = substr($string, 1);
        }
        else {
            $sign = 1;
        }
        if($base['name'] != "decimal") {
            $leftRight = explode(';', $string);
        }
        else {
            $leftRight = explode('.', $string);
        }
        if(count($leftRight) < 2) {
            $left = $leftRight[0];
            $right = "";
        }
        elseif(count($leftRight) === 2) {
            $left = $leftRight[0];
            $right = $leftRight[1];
        }
        else {
            throw new NumberViewException("Too many semi-columns (;)");
        }
        $left = trim($left);
        $right = trim($right);
        
        $rightNumbers = [];
        $leftNumbers = [];
        
        if($right != "") {
            if($base['name'] == "decimal") {
                $rightNumberString = str_split($right);
            }
            else {
                $rightNumberString = explode(',', $right);
            }
            for($i=0; $i<count($rightNumberString); $i++) {
                if(!ctype_digit(trim($rightNumberString[$i]))) {
                    //throw new \Exception('nooo');
                    throw new NumberViewException("Parsing error. Cannot convert '".trim($rightNumberString[$i])."' to positive integer");
                }
                $rightNumbers[] = (int)(trim($rightNumberString[$i]));
            }
        }
        
        if($left != "") {
            for($i=0; $i<count($base['integerSeparator']); $i++) {
                $leftNumberString = explode(strtolower(trim($base['integerSeparator'][$i])), $left);
                if(!ctype_digit(trim($leftNumberString[count($leftNumberString)-1]))) {
                    //throw new \Exception('nooo');
                    
                    throw new NumberViewException("Parsing error. Cannot convert '".trim($leftNumberString[count($leftNumberString)-1])."' to positive integer");
                }
                $leftNumbers[] = (int)(trim($leftNumberString[count($leftNumberString)-1]));
                if(count($leftNumberString) < 2) {
                    $left = "";
                    break;
                }
                array_splice($leftNumberString, -1);
                $left = implode(strtolower(trim($base['integerSeparator'][$i])), $leftNumberString);
            }
        }
        
        if($left != "") {
            if($base["left"][count($base["left"])-1] == 10) {
                $leftNumberString = str_split($left);
            }
            else {
                $leftNumberString = explode(',', $left);
            }
            for($i=0; $i<count($leftNumberString); $i++) {
                if(!ctype_digit(trim($leftNumberString[count($leftNumberString)-1-$i]))) {
                    //throw new \Exception('nooo');
                    
                   throw new NumberViewException("Parsing error. Cannot convert '".trim($leftNumberString[count($leftNumberString)-1-$i])."' to positive integer");
                }
                $leftNumbers[] = (int)(trim($leftNumberString[count($leftNumberString)-1-$i]));
            }
        }
        
        $res = new NumberView($leftNumbers, $rightNumbers, 0.0, $sign, $base);
        return $res->sanitize();
    }
    
    public function copy() {
        return new NumberView($this->leftList, $this->rightList, $this->remainder, $this->sign, $this->base);
    }
    
    public function set($other) {
        $this->leftList = $other->leftList;
        $this->rightList = $other->rightList;
        $this->remainder = $other->remainder;
        $this->sign = $other->sign;
        $this->base = $other->base;
        
        return $this;
    }
    
    /*
     * resize
     */
    public function resize($significant) {
        if($significant === count($this->rightList)) {
            return $this;
        }
        // convert the remainder into its flaoting value
        $factor = 1.0;
        for($i=0; $i<count($this->rightList); $i++) {
            $factor /= $this->base["right"][$i];
        }
        $remainderValue = $factor * $this->remainder;
        
        if($significant > count($this->rightList)) { // stretching the number
            $this->remainder = 0.0;
            for($i=0; $i<$significant-count($this->rightList); $i++) {
                $this->rightList[] = 0;
            }
            $this->add(NumberView::float64ToBase($this->sign * $remainderValue,$this->base,$significant));
            return $this;
        }
        else { // truncating the number and updating the remainder
            //add to $remainderValue the value of truncated positions
            $factor = 1.0;
            for($i=0; $i<$significant; $i++) {
                $factor /= $this->base["right"][$i];
            }
            for($i=$significant; $i<count($this->rightList); $i++) {
                $factor /= $this->base["right"][$i];
                $remainderValue += $factor * $this->rightList[$i];
            }
            $this->truncate($significant);
            $this->add(NumberView::float64ToBase($this->sign * $remainderValue,$this->base,$significant));
            return $this;
        }
        
        return $this;
    }
    
    /*
     * add
     */
    public function add($o) {
        $other = $o->copy();
        if(count($this->rightList) < count($other->rightList)) {
            $this->resize(count($other->rightList));
        }
        if(count($this->rightList) > count($other->rightList)) {
            $other->resize(count($this->rightList));
        }
        while(count($this->leftList) < count($other->leftList)) {
            $this->leftList[] = 0;
        }
        while(count($this->leftList) > count($other->leftList)) {
            $other->leftList[] = 0;
        }
        for($pos=1-count($this->leftList); $pos<count($this->rightList)+1; $pos++) {
            $this->setValueAtPos($pos, $this->posToValue($pos) + $this->sign * $other->sign * $other->posToValue($pos));
        }
        $this->remainder += $this->sign * $other->sign * $other->remainder;
        $this->sanitize();
        
        return $this;
    }
    
    /*
     * simplifyIntegerPart: will remove the useless zeros in $leftList
     */
    public function simplifyIntegerPart() {
        // reverse loop the integer part
        for($i = count($this->leftList) - 1; $i >= 0; $i--) {
            if($this->leftList[$i] != 0) {
                break;
            }
        }
        // remove trailing zeros
        //$nEnding = count($this->leftList) -1 - $i;
        array_splice($this->leftList, $i+1);
        
        return $this;
    }
    
    /*
     * Truncate the number up to to the nth position
     */
    public function truncate($n) {
        if($n > count($this->rightList)) {
            return $this;
        }
        $this->remainder = 0.0;
        array_splice($this->rightList, $n);
        
        return $this;
    }
    
    public function round($significant = null) {
        if($significant === null) {
            $significant = count($this->rightList);
        }
        $this->resize($significant);
        if($this->remainder > 0.5) {
            $this->addToPos($significant, 1);
        }
        $this->remainder = 0.0;
        
        return $this;
    }
    
    public function posToBase($pos) {
        if($pos <= 0) {
            return $this->base["left"][-$pos];
        }
        return $this->base["right"][$pos-1];
    }
    public function posToValue($pos) {
        if($pos <= 0) {
            return $this->leftList[-$pos];
        }
        return $this->rightList[$pos-1];
    }
    public function setValueAtPos($pos, $value) {
        if($pos <= 0) {
            $this->leftList[-$pos] = $value;
        }
        else {
            $this->rightList[$pos-1] = $value;
        }
    }
    
    public function sanitize($pos = null) {
        if($pos === null) {
            $pos = count($this->rightList);
        }
        if($pos === count($this->rightList)) {
            $this->setValueAtPos($pos, $this->posToValue($pos) + (int)floor($this->remainder));
            $this->remainder -= floor($this->remainder);
        }
        
        $this->naiveSanitize();
        if(count($this->leftList) > 0 && $this->leftList[count($this->leftList)-1] < 0) {
            for($i=0; $i<count($this->leftList); $i++) {
                $this->leftList[$i] *= -1;
            }
            for($i=0; $i<count($this->rightList); $i++) {
                $this->rightList[$i] *= -1;
            }
            $this->naiveSanitize();
            $this->sign *= -1;
        }
        return $this;
    }
    
    private function naiveSanitize($pos = null) {
        if($pos === null) {
            $pos = count($this->rightList);
        }
        if($pos <= 0 && -$pos >= count($this->leftList)) {
            return $this;
        }
        if($this->posToValue($pos) >= $this->posToBase($pos)) {
            $factor = floor($this->posToValue($pos)/$this->posToBase($pos));
            $this->setValueAtPos($pos, $this->posToValue($pos) % $this->posToBase($pos));
            if($pos-1 <= 0 && -($pos-1) >= count($this->leftList)) {
                $this->leftList[] = 0;
            }
            $this->setValueAtPos($pos-1, (int)($factor + $this->posToValue($pos-1)));
        }
        if($this->posToValue($pos) < 0) {
            if($pos-1 <= 0 && -($pos-1) >= count($this->leftList)) {
                return $this;
            }
            $factor = -(1+floor(-$this->posToValue($pos)/$this->posToBase($pos)));
            $modulo = ($this->posToBase($pos) - ((-$this->posToValue($pos)) % $this->posToBase($pos)));
            if($modulo === $this->posToBase($pos)) {
                $modulo = 0;
                $factor += 1;
            }
            $this->setValueAtPos($pos, $modulo);
            $this->setValueAtPos($pos-1, (int)($factor + $this->posToValue($pos-1)));
        }
        
        return $this->naiveSanitize($pos-1);
    }
    
    /*
     * Add a number to the nth position
     */
    public function addToPos($pos, $nb) {
        if($nb === 0) {
            return $this;
        }
        // The user wants to add at a position outside the left list (to the "left" of the number)
        if($pos <= 0 && -1.0*$pos >= count($this->leftList)) {
            for($i=0; $i<count($this->leftList)-$pos+1; $i++) {
                $this->leftList[] = 0;
            }
        }
        // The user wants to add at a position outside the right list (to the "right" of the number)
        // update the remainder accordingly
        if($pos-1 >= count($this->rightList)) {
            //TODO
        }
        $this->setValueAtPos($pos, $this->posToValue($pos) + $this->sign * $nb);
        $this->sanitize();
        
        return $this;
    }
    
    public function equals($object) {
        if(!checkListSimilarity($this->leftList, $object->leftList)) {
            return false;
        }
        if(!checkListSimilarity($this->rightList, $object->rightList)) {
            return false;
        }
        if(!checkListQualifyingNumber($this->leftList, $object->leftList)) {
            return false;
        }
        if(!checkListQualifyingNumber($this->rightList, $object->rightList)) {
            return false;
        }
        return true;
    }
    
    static public function float64ToBase($float, $base, $significant) {
        $sign = (int)sign($float);
        
        // turn $float into a positive number
        $float *= $sign;
        
        // compute the number of necessary places for the integer part
        $pos = 0;
        $maxInteger = 1;
        
        while($float >= $maxInteger) {
            $maxInteger *= $base["left"][$pos];
            $pos++;
        }
        
        $left = array_fill(0, $pos, 0);
        $right = array_fill(0, $significant, 0);
        
        $factor = $maxInteger;
        // compute the integer (i.e. left) part
        for($i=$pos-1;$i>=0;$i--) {
            $factor /= $base["left"][$i];
            $positionValue = (int)floor($float / $factor);
            $float -= $positionValue * $factor;
            $left[$i] = $positionValue;
        }
        
        //compute the fractionnal part
        $factor = 1.0;
        for($i=0; $i<$significant; $i++) {
            $factor /= $base["right"][$i];
            $positionValue = (int)floor($float / $factor);
            $float -= $positionValue * $factor;
            $right[$i] = $positionValue;
        }
        
        $remainder = $float / $factor;
        
        return new NumberView($left, $right, $remainder, $sign, $base);
    }
    
    function toFloat64() {
        $dec = 0.0;
        $factor = 1.0;
        for($i=0; $i<count($this->leftList); $i++) {
            $dec += ($factor * $this->leftList[$i]);
            $factor *= $this->base["left"][$i];
        }
        $factor = 1.0;
        for($i=0; $i<count($this->rightList); $i++) {
            $factor /= $this->base["right"][$i];
            $dec += ($factor * $this->rightList[$i]);
        }
        
        //ajout du remainder
        $dec += $factor * $this->remainder;
        
        return $dec * $this->sign;
    }
    
    public function toBase($base, $significant) { // unsing only integer arithmetic
        if($this->sign == 0) {
            $this->sign = 1;
        }
        // integer part
        $result = NumberView::IntToBase($this->sign * $this->toInt(), $base, $significant);
        
        // fractionnal part
        for($i=0; $i<count($this->rightList); $i++) {
            $result->add(NumberView::fractionnal_position_base_to_base($this->rightList[$i], $i, $this->base, $base, $significant));
        }
        
        // convert the remainder into its flaoting value
        $factor = 1.0;
        for($i=0; $i<count($this->rightList); $i++) {
            $factor /= $this->base["right"][$i];
        }
        $remainderValue = $factor * $this->remainder;
        
        $result->add(NumberView::float64ToBase($remainderValue, $base, $significant));
        $result->sign = $this->sign;
        return $result;
    }
    
    static private function fractionnal_position_base_to_base($value, $pos, $base1, $base2, $significant) {
        $left = [0];
        $right = array_fill(0, $significant, 0);
        
        $denom = gmp_init(1);
        for($i=0; $i<=$pos; $i++) {
            $denom = gmp_mul($denom, gmp_init($base1["right"][$i]));
        }
        
        $num = gmp_init($value);
        for($i=0; $i<$significant; $i++) {
            $num = gmp_mul($num, gmp_init($base2["right"][$i]));
            //check infinity here too
            $quo = gmp_div_q($num, $denom);
            $rem = gmp_div_r($num, $denom);
            //check infinity of quo ?
            $right[$i] = gmp_intval($quo);
            $num = $rem;
        }
        
        // if rem defined
        $float_rem = (float)gmp_intval($rem);
        $int_denom = gmp_intval($denom);
        if($int_denom != 0) {
            $remainder = $float_rem/$int_denom;
        }
        else {
            $app_rem = gmp_div_q(gmp_mul($rem, gmp_init(100000000)),$denom);
            $remainder = gmp_intval($app_rem)/100000000.0;
        }
        
        $result = new NumberView($left, $right, $remainder, 1, $base2);
        return $result;
    }
    
    static public function IntToBase($int, $base, $significant) {
        $sign = (int)sign($int);
        
        // turn $int into a positive number
        $int *= $sign;
        
        // compute the number of necessary places for the integer part
        $pos = 0;
        $maxInteger = 1;
        
        while($int >= $maxInteger) {
            $maxInteger *= $base["left"][$pos];
            $pos++;
        }
        
        $left = array_fill(0, $pos, 0);
        $right = array_fill(0, $significant, 0);
        
        $factor = $maxInteger;
        // compute the integer (i.e. left) part
        for($i=$pos-1;$i>=0;$i--) {
            $factor /= $base["left"][$i];
            $positionValue = (int)($int / $factor);
            $int -= $positionValue * $factor;
            $left[$i] = $positionValue;
        }
        $remainder = 0.0;
        return new NumberView($left, $right, $remainder, $sign, $base);
    }
    
    function toInt() {
        $dec = 0;
        $factor = 1;
        for($i=0; $i<count($this->leftList); $i++) {
            $dec += ($factor * $this->leftList[$i]);
            $factor *= $this->base["left"][$i];
        }
        
        return $dec * $this->sign;
    }
    
    static private function checkListSimilarity($array1, $array2) {
        for($i=0; $i<min(count($array1), count($array2)); $i++) {
            if($array1[$i] != $array2[$i]) {
                return false;
            }
        }
        return true;
    }
    
    static private function checkListQualifyingNumber($array1, $array2) {
        if(($a = count($array1)) < ($b = count($array2))) {
            $maxLeftObject = $array2;
            $numberOfExpectedZeros = $b - $a;
        }
        else {
            $maxLeftObject = $array1;
            $numberOfExpectedZeros = $a - $b;
        }
        if(!isFollowedByZeros($maxLeftObject, count($maxLeftObject)-$numberOfExpectedZeros)) {
            return false;
        }
        return true;
    }
    
    static private function isFollowedByZeros($array, $index) {
        for($i=$index; $i<count($array); $i++) {
            if($array[$i] != 0) {
                return false;
            }
        }
        return true;
    }
}