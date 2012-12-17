<?php

/**
 * JSON to XML Converter
 *
 * @author		Brent Burgoyne
 * @link		http://brentburgoyne.com
 */

class JsonToXml
{
    private static $dom;

    public static function convert($json, $return = 'document')
    {
        self::$dom = new DOMDocument('1.0', 'utf-8');
        self::$dom->formatOutput = true;
        self::$dom->preserveWhiteSpace = false; // Manuel - This is needed for formatOutput

        // remove callback functions from JSONP
//        if (preg_match('/(\{|\[).*(\}|\])/s', $json, $matches)) {
//            $json = $matches[0];
//        } elseif($json!="null" && !is_numeric (str_replace ('"', '', $json))&&"false"!=$json&&"true"!=$json) {
//            throw new Exception('JSON not formatted correctly');
//        }
        
        if (is_string($json)) {
            $data = json_decode($json);
        }
        
        $data_element = self::process($data, self::$dom->createElement('data'));
        self::$dom->appendChild($data_element);

        switch ($return)
        {
            case 'fragment': return self::$dom->saveXML($data_element);
            case 'object': return self::$dom;
            default: return self::$dom->saveXML();
        }
    }

    private static function process($data, $element)
    {
        if (is_array($data)) {
            foreach ($data as $item) {
                $item_element = self::process($item, self::$dom->createElement('item'));
                $element->appendChild($item_element);
            }
        } elseif (is_object($data)) {
            $vars = get_object_vars($data);
            foreach ($vars as $key => $value) {
                $key = self::validElementName($key);
                $var_element = self::process($value, self::$dom->createElement($key));
                $element->appendChild($var_element);
            }
        } else {
//            if($data==null) $data="null";
            $element->appendChild(self::$dom->createTextNode($data));
        }
        return $element;
    }

    private static function validElementName($name)
    {
        $name = preg_replace('/^(.*?)(xml)([a-z])/i', '$3', $name);
        $name = preg_replace('/[^a-z0-9_\-]/i', '', $name);
        return $name;
    }
}