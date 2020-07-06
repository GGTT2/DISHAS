<?php

namespace TAMAS\AstroBundle\DISHASToolbox;

class GenericTools
{
    static $plural = array(
        '/(quiz)$/i'               => "$1zes",
        '/^(ox)$/i'                => "$1en",
        '/([m|l])ouse$/i'          => "$1ice",
        '/(matr|vert|ind)ix|ex$/i' => "$1ices",
        '/(x|ch|ss|sh)$/i'         => "$1es",
        '/([^aeiouy]|qu)y$/i'      => "$1ies",
        '/(hive)$/i'               => "$1s",
        '/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
        '/(shea|lea|loa|thie)f$/i' => "$1ves",
        '/sis$/i'                  => "ses",
        '/([ti])um$/i'             => "$1a",
        '/(tomat|potat|ech|her|vet)o$/i' => "$1oes",
        '/(bu)s$/i'                => "$1ses",
        '/(alias)$/i'              => "$1es",
        '/(octop)us$/i'            => "$1i",
        '/(ax|test)is$/i'          => "$1es",
        '/(us)$/i'                 => "$1es",
        '/s$/i'                    => "s",
        '/$/'                      => "s"
    );

    static $singular = array(
        '/(quiz)zes$/i'             => "$1",
        '/(matr)ices$/i'            => "$1ix",
        '/(vert|ind)ices$/i'        => "$1ex",
        '/^(ox)en$/i'               => "$1",
        '/(alias)es$/i'             => "$1",
        '/(octop|vir)i$/i'          => "$1us",
        '/(cris|ax|test)es$/i'      => "$1is",
        '/(shoe)s$/i'               => "$1",
        '/(o)es$/i'                 => "$1",
        '/(bus)es$/i'               => "$1",
        '/([m|l])ice$/i'            => "$1ouse",
        '/(x|ch|ss|sh)es$/i'        => "$1",
        '/(m)ovies$/i'              => "$1ovie",
        '/(s)eries$/i'              => "$1eries",
        '/([^aeiouy]|qu)ies$/i'     => "$1y",
        '/([lr])ves$/i'             => "$1f",
        '/(tive)s$/i'               => "$1",
        '/(hive)s$/i'               => "$1",
        '/(li|wi|kni)ves$/i'        => "$1fe",
        '/(shea|loa|lea|thie)ves$/i' => "$1f",
        '/(^analy)ses$/i'           => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
        '/([ti])a$/i'               => "$1um",
        '/(n)ews$/i'                => "$1ews",
        '/(h|bl)ouses$/i'           => "$1ouse",
        '/(corpse)s$/i'             => "$1",
        '/(us)es$/i'                => "$1",
        '/s$/i'                     => ""
    );

    static $irregular = array(
        'move'   => 'moves',
        'foot'   => 'feet',
        'goose'  => 'geese',
        'sex'    => 'sexes',
        'child'  => 'children',
        'man'    => 'men',
        'tooth'  => 'teeth',
        'person' => 'people'
    );

    static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );


    /**
     * This method is used to merge array with key=>value
     * the content of each key is checked
     * if the same key content is present in both, only one remains
     * new contents from same keys are added
     * new keys comming from array2 may be added
     * 
     **/
    public static function mergeArrays($Arr1, $Arr2)
    {
        foreach ($Arr2 as $key => $Value) {
            if (array_key_exists($key, $Arr1) && is_array($Value))
                $Arr1[$key] = self::mergeArrays($Arr1[$key], $Arr2[$key]);

            else
                $Arr1[$key] = $Value;
        }

        return $Arr1;
    }


    public static function getClassName($obj)
    {
        $classname = get_class($obj);
        if ($pos = strrpos($classname, '\\')) return mb_substr($classname, $pos + 1);
        return $pos;
    }


    static function inArray($object, $array)
    {
        foreach ($array as $o) {
            if (spl_object_id($o) == spl_object_id($object)) {
                return true;
            }
        }
        return false;
    }

    static function inArrayValues($object, $array)
    {
        foreach ($array as $key => $o) {
            if (spl_object_id($o) == spl_object_id($object)) {
                return true;
            }
        }
        return false;
    }

    static function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    static function getObjectKeyInArray($object, $array)
    {
        $keys = [];
        foreach ($array as $k => $o) {
            if (spl_object_id($o) == spl_object_id($object)) {
                $keys[] = $k;
            }
        }

        return $keys;
    }

    static function testRange($int, $min, $max)
    {
        return ($min < $int && $int < $max);
    }

    static function str_ends($string, $end)
    {
        return (mb_substr($string, -strlen($end), strlen($end)) === $end);
    }

    static function str_begins($string, $start)
    {
        return (mb_substr($string, 0, strlen($start)) === $start);
    }

    static function truncate($text, $chars = 25, $end = "…")
    {
        return $text; //substring ne fonctionne pas avec les scripts arabes !
        /*if (strlen($text) <= $chars) {
            return $text;
        }
        $text = $text . " ";
        $text = mb_substr($text, 0, $chars);
        $text = $text . $end;
        return $text;*/
    }

    public static function shortenString($text, $chars = 25, $end = "…")
    {
        if (strlen($text) <= $chars) {
            return $text;
        }
        $text = $text . " ";
        $text = mb_substr($text, 0, $chars);
        $text = $text . $end;
        return $text;
    }

    /**
     * Creates a valid date from a year
     * Returns a string if it successfully creates it, a date in the format 'Year-Month-Day'
     * Return false if it couldn't
     *
     * @param $date
     * @return bool|string
     * @throws \Exception
     */
    public static function createValidDate($date)
    {
        if ($date) { // if the field containing the date is not empty/null
            if (substr(strval($date), 0, 1) == "-") { // if the year is negative
                while (strlen(substr(strval($date), 1)) < 4) { // if the tpq is less than 4 digit, it makes an invalid date
                    $date = "-0" . substr(strval($date), 1); // we had a 0 to the year until it's 4 digit long
                }
            } else { // if the year is positive
                while (strlen(strval($date)) < 4) { // if the tpq is less than 4 digit, it makes an invalid date
                    $date = "0" . strval($date); // we had a 0 to the year until it's 4 digit long
                }
            }

            $temp = new \DateTime(strval($date) . '-01-01'); // we create a DateTime with the now 4 digit year
            return $temp->format('Y-m-d');
        }

        return false;
    }

    /**
     * @param $object
     * @param $dateFieldName
     * @param $outputFieldName
     * @param null $addParam : additional parameter to pass to the getter
     * @throws \Exception
     */
    public static function setValidDateFromYear($object, $dateFieldName, $outputFieldName, $addParam = null)
    {
        $getter = "get" . ucfirst($dateFieldName);
        $setter = "set" . ucfirst($dateFieldName);
        $outputSetter = "set" . ucfirst($outputFieldName);

        if (method_exists($object, $getter) && method_exists($object, $setter) && method_exists($object, $outputSetter)) { // check if all getter/setters exists
            $date = $addParam ? self::createValidDate($object->$getter($addParam)) : self::createValidDate($object->$getter());
            if ($date) { // if we successfully create a valid date
                $object->$outputSetter($date); // we set the output field to the date created
            }
        }
    }

    public static function setMetadata(string $hover = "", array $values = [], array $queries = [], array $titles = [])
    {
        return ["val" => $values, "search" => ["json" => $queries, "hover" => $hover, "title" => $titles]];
    }

    /**
     * This method checks if every element of an array is empty or null
     *
     * @param array $array
     * @return bool
     */
    public static function is_array_empty(array $array)
    {
        if (empty($array)) {
            return true;
        } else {
            $value = 0;
            foreach ($array as $element) {
                if (!empty($element)) {
                    $value += 1;
                }
            }
            if ($value == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * This method returns an array containing all the deepest levels
     * of the array given as parameter
     *
     * EXAMPLE : $array = ["id" => 5, "primary_source" => ["shelfmark" => "Latin 2074", "library" => ["library_name" => "BnF"]]];
     * $leaves = ["id" => 5, "shelfmark" => "Latin 2074", "library_name" => "BnF"];
     *
     * @param array $array
     * @param array $leaves
     * @return array
     */
    public static function getLeaves(array $array, $leaves = [])
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $leaves = self::getLeaves($value, $leaves);
            } else {
                $leaves[$key] = $value;
            }
        }
        return $leaves;
    }

    /**
     * This function returns a formatted date
     *
     * @param $tpq
     * @param $taq
     * @param $unknown string value to display in case there is no data
     * @param $withHTML boolean value indicating if html tags are allowed or not
     * @return string
     */
    public static function getTpaq($tpq, $taq, $unknown = "?–?", $withHTML = false)
    {
        if (isset($tpq) || isset($taq)) {
            $tpq = isset($tpq) ? $tpq : "?";
            $taq = isset($taq) ? $taq : "?";
            if (($tpq == $taq) && ($tpq != "?")) {
                return "$tpq";
            } else {
                return "$tpq" . "–" . "$taq";
            }
        } else {
            return $withHTML ? "<span class='noInfo'>$unknown</span>" : "$unknown";
        }
    }

    /**
     * This function takes a string in snake_case and returns a string in PascalCase/camelCase
     *
     * Ex : 'this_is_a_string' => 'ThisIsAString'
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @return string
     */
    public static function underscoreToCamelCase(string $string, bool $capitalizeFirstCharacter = true)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * This function takes a string in PascalCase/camelCase
     * and turns it to a snake_case
     * 
     * @param string $string
     * @return string
     */
    public static function camelCaseToUnderscore(string $string)
    {
        return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $string)), '_');
    }

    /**
     * This function returns the value in between the value given as parameter
     * @param $values : array of numerical values
     * @return float|int
     */
    public static function getMiddleValue($values)
    {
        return min($values) + ((max($values) - min($values)) / 2);
    }

    /**
     * Callback function to compare date of two objects
     * EX: usort($array, array('TAMAS\AstroBundle\DISHASToolbox\GenericTools', 'compareDate'))
     * @param $object1
     * @param $object2
     * @return bool
     */
    public static function compareDate($object1, $object2)
    {
        $date1 = $object1->getDate() ? $object1->getDate() : 0;
        $date2 = $object2->getDate() ? $object2->getDate() : 0;
        return $date1 > $date2;
    }

    /**
     * Callback function to compare tpq of two objects
     * EX: usort($array, array('TAMAS\AstroBundle\DISHASToolbox\GenericTools', 'compareTpq'))
     * @param $object1
     * @param $object2
     * @return bool
     */
    public static function compareTpq($object1, $object2)
    {
        $date1 = $object1->getTpq() ? $object1->getTpq() : 0;
        $date2 = $object2->getTpq() ? $object2->getTpq() : 0;
        return $date1 > $date2;
    }

    /**
     * Callback function to compare astronomical object ids of two original texts
     * EX: usort($array, array('TAMAS\AstroBundle\DISHASToolbox\GenericTools', 'compareAstroObject'))
     * @param $object1
     * @param $object2
     * @return bool
     */
    public static function compareAstroObject($object1, $object2)
    {
        $astroObject1 = $object1->getTableType()->getAstronomicalObject()->getId() ?
            $object1->getTableType()->getAstronomicalObject()->getId() : 0;
        $astroObject2 = $object2->getTableType()->getAstronomicalObject()->getId() ?
            $object2->getTableType()->getAstronomicalObject()->getId() : 0;
        return $astroObject1 > $astroObject2;
    }

    public function compareDateAndObject($obj1, $obj2)
    {
        $date1 = $obj1->getTpq() ? intval($obj1->getTpq()) : 0;
        $date2 = $obj2->getTpq() ? intval($obj2->getTpq()) : 0;

        $astroObject1 = $obj1->getTableType() ? $obj1->getTableType()->getAstronomicalObject()->getId() : 0;
        $astroObject2 = $obj2->getTableType() ? $obj2->getTableType()->getAstronomicalObject()->getId() : 0;

        $o1 = ["astro" => $astroObject1, "date" => $date1];
        $o2 = ["astro" => $astroObject2, "date" => $date2];

        if ($o1['date'] > $o2['date'])
            return 1;
        if ($o1['date'] < $o2['date'])
            return -1;
        if ($o1['date'] == $o2['date']) {
            if ($o1['astro'] > $o2['astro'])
                return 1;
            if ($o1['astro'] < $o2['astro'])
                return -1;
            if ($o1['astro'] == $o2['astro'])
                return 0;
        }
        return 0;
    }

    /**
     * @author http://www.mendoweb.be/blog/php-convert-string-to-camelcase-string/
     * @param $str
     * @param array $noStrip
     * @return string|string[]|null
     */
    public static function toCamel($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

    /**
     * http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/
     * @param $string
     * @return string
     */
    public static function toPlural($string)
    {
        /*if (self::str_ends($string,"y")){
            return substr($string, 0, strlen($string-1))."ies";
        } elseif (self::str_ends($string,"s") || self::str_ends($string,"x")){
            return $string."es";
        } else {
            return$string."s";
        }*/
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable))
            return $string;


        // check for irregular singular forms
        foreach (self::$irregular as $pattern => $result) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach (self::$plural as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        return $string;
    }

    public static function toSingular($string)
    {
        // save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable))
            return $string;

        // check for irregular plural forms
        foreach (self::$irregular as $result => $pattern) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        // check for matches using regular expressions
        foreach (self::$singular as $pattern => $result) {
            if (preg_match($pattern, $string))
                return preg_replace($pattern, $result, $string);
        }

        return $string;
    }

    public static function toDeterminer($string)
    {
        if (in_array(mb_substr($string, 0, 1), ['a', 'e', 'i', 'o', 'u'])) {
            return "an " . $string;
        } else {
            return "a " . $string;
        }
    }

    public static function toColumnName($c)
    {
        $c = intval($c);
        if ($c <= 0) return '';

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return $letter;
    }


    /*
    *
    * Get the content of a website from its url (can send data, credentials and HTTP request type)
    * Returns false if there's an error, and if not, the content of the webpage and the http code 
    *
    */
    public static function getWebpages($url, $data = null, $username = null, $password = null, $requestType = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if ($username && $password) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        }

        if ($requestType) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        }

        if ($data) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        $results = [];

        if (!curl_errno($ch)) {
            $results['http_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $results['output'] = $output;
        }

        curl_close($ch);

        return $results;
    }


    /**
     * This function prints info into a specific log document
     * 
     */
    public static function logPrint($file, $status = null, $message = null)
    {
        $date = new \DateTime();
        $more = $message ? " [$message] " : "";
        file_put_contents($file, $status . $date->format('Y-m-d H:i:s') . "$more\n", FILE_APPEND);
    }
}
