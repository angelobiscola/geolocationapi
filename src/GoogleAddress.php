<?php

namespace Angelo\GeoLocation;

class GoogleAddress
{
    const NUMERO = 'street_number';
    const RUA    = 'route';
    const BAIRRO = 'political';
    const CIDADE = 'political';
    const ESTADO = 'administrative_area_level_1';
    const PAIS   = 'country';
    const CEP    = 'postal_code';

    private $address;

    public function __construct(array $address)
    {
        $this->address = $address;
    }

    public function getFullAddress()
    {
        return $this->arrayToObject($this->address['label']);
    }

    private function arrayToObject($array) {
        if (!is_array($array)) {
            return $array;
        }
        $object = new \stdClass();
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $name=>$value) {
                $name = strtolower(trim($name));
                if (!empty($name)) {
                    $object->$name = arrayToObject($value);
                }
            }
            return $object;
        }
        else {
            return FALSE;
        }
    }

    public function getComponentsAddress(string $name)
    {
        if(array_key_exists($name,$this->address['components']))
        {
           return $this->arrayToObject($this->address['components'][$name]);
        }
    }
}