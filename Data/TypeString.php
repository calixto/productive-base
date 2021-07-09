<?php

namespace Productive\Data;

class TypeString {

    public static function dashesToUpperCamelCase($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        $str[0] = strtolower($str[0]);
    }

    public static function dashesToLowerCamelCase($string) {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        $str[0] = strtolower($str[0]);
        return $str;
    }

    public static function toDashed($string) {
        
    }

    public static function underlineToLowerCamelCase($string) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public static function underlineToUpperCamelCase($string) {
        return ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    public static function accentRemove($string) {
        $string = str_replace('ç', 'c', $string);
        $string = str_replace('à', 'a', $string);
        $string = str_replace('è', 'e', $string);
        $string = str_replace('ì', 'i', $string);
        $string = str_replace('ò', 'o', $string);
        $string = str_replace('ù', 'u', $string);
        $string = str_replace('â', 'a', $string);
        $string = str_replace('ê', 'e', $string);
        $string = str_replace('î', 'i', $string);
        $string = str_replace('ô', 'o', $string);
        $string = str_replace('û', 'u', $string);
        $string = str_replace('ä', 'a', $string);
        $string = str_replace('ë', 'e', $string);
        $string = str_replace('ï', 'i', $string);
        $string = str_replace('ö', 'o', $string);
        $string = str_replace('ü', 'u', $string);
        $string = str_replace('á', 'a', $string);
        $string = str_replace('é', 'e', $string);
        $string = str_replace('í', 'i', $string);
        $string = str_replace('ó', 'o', $string);
        $string = str_replace('ú', 'u', $string);
        $string = str_replace('ã', 'a', $string);
        $string = str_replace('õ', 'o', $string);
        $string = str_replace('À', 'A', $string);
        $string = str_replace('Ç', 'C', $string);
        $string = str_replace('È', 'E', $string);
        $string = str_replace('Ì', 'I', $string);
        $string = str_replace('Ò', 'O', $string);
        $string = str_replace('Ù', 'U', $string);
        $string = str_replace('Â', 'A', $string);
        $string = str_replace('Ê', 'E', $string);
        $string = str_replace('Î', 'I', $string);
        $string = str_replace('Ô', 'O', $string);
        $string = str_replace('Û', 'U', $string);
        $string = str_replace('Ä', 'A', $string);
        $string = str_replace('Ë', 'E', $string);
        $string = str_replace('Ï', 'I', $string);
        $string = str_replace('Ö', 'O', $string);
        $string = str_replace('Ü', 'U', $string);
        $string = str_replace('Á', 'A', $string);
        $string = str_replace('É', 'E', $string);
        $string = str_replace('Í', 'I', $string);
        $string = str_replace('Ó', 'O', $string);
        $string = str_replace('Ú', 'U', $string);
        $string = str_replace('Ã', 'A', $string);
        $string = str_replace('Õ', 'O', $string);
        return $string;
    }
}
