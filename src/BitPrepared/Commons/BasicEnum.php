<?php 

namespace BitPrepared\Commons;

use \ReflectionClass;

abstract class BasicEnum {

    private static $constCacheArray = NULL;

    protected static $typeLabels = array();

    /**
     * @param int $typeValue
     * @return string
     */
    public static function label($typeValue)
    {
        return isset( static::$typeLabels[$typeValue] ) ? static::$typeLabels[$typeValue] : '';
    }

    /**
     * @return array
     */
    public static function labels()
    {
        return static::$typeLabels;
    }

    /**
     * @return array
     */
    public static function values()
    {
        return array_keys( static::$typeLabels );
    }

    protected static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = array();
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, true);
    }

    public static function fromValue($number){
        $consts = self::getConstants();
        foreach ($consts as $key => $value) {
            if ( $value == $number ) {
                return $key;
            }
        }
        return null;
    }

    public static function fromLabel($text){
        $consts = static::$typeLabels;
        foreach ($consts as $key => $value) {
            if ( $value == $text ) {
                return $key;
            }
        }
        return null;
    }
    
}