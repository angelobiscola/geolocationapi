<?php

namespace Angelo\GeoLocation;

class GoogleMap{
    // API URL
    const API_URL = 'maps.googleapis.com/maps/api/geocode/json';

    private $api_key;
    private $https;

    public function __construct($api_key = null, $https = false){

        $this->https = $https;
        if ($api_key) {
            $this->api_key = $api_key;
            $this->https = true;
        }
    }

    public function setLocation($latitude, $longitude){
        $addressSuggestions = $this->getLocation($latitude, $longitude);


        if($addressSuggestions){
            $address = $addressSuggestions[0];
        }else{

            $address = [];
        }

        return new GoogleAddress($address);
    }

    private function getLocation($latitude, $longitude){
        // init results
        $addresses = array();
        // define result
        $addressSuggestions = $this->doCall(array(
            'latlng' => $latitude . ',' . $longitude,
            'sensor' => 'false'
        ));

        // loop addresses
        foreach ($addressSuggestions as $key => $addressSuggestion) {

            // init address
            $address = array();
            // define label
            $address['label'] = isset($addressSuggestion->formatted_address) ?
                $addressSuggestion->formatted_address : null
            ;
            // define address components by looping all address components
            foreach ($addressSuggestion->address_components as $component) {

                $address['components'][$component->types[0]] = array(
                    'long_name' => $component->long_name,
                    'short_name' => $component->short_name,
                );
            }
            $addresses[$key] = $address;
        }
        return $addresses;
    }

    protected function doCall($parameters = array()){
        // verificar se curl esta disponivel
        if (!function_exists('curl_init')) {

            throw new GoopleMapException('Este método requer cURL (http://php.net/curl), parece que a extensão não está instalada.');
        }
        // define url
        $url = ($this->https ? 'https://' : 'http://') . self::API_URL . '?';

        // agrupa todos os parametos na url
        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }

        $url = trim($url, '&');
        if ($this->api_key) {
            $url .= '&key=' . $this->api_key;
        }
        // inicia curl
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        }
        // executa
        $response = curl_exec($curl);

        // buscar erros
        //$errorNumber = curl_errno($curl);
        //$errorMessage = curl_error($curl);
        // ecerra curl
        curl_close($curl);
        $response = json_decode($response);

        if ($response->status != 'OK') {

            throw new GoopleMapException($response->error_message);
        }

        $tipo = $response->results;
        foreach ($tipo as $key => $value) {

          switch ($value->types[0]){
                case 'establishment':
                    return $tipo;
                    break;
                case 'street_address':
                    return $tipo;
                    break;
                case 'premise':
                    return $tipo;
                    break;
                default:
                    break;
            }
        }
    }

    public function getEnderecoCompleto()
    {
        return $this->address['label'];
    }

    public function getCoordinates(
        $street = null,
        $streetNumber = null,
        $city = null,
        $zip = null,
        $country = null
    ) {
        // iniciar item
        $item = array();
        // agregar calle
        if (!empty($street)) $item[] = $street;
        // agregar el nÃºmero de la calle
        if (!empty($streetNumber)) $item[] = $streetNumber;
        // agregar ciudad - comuna
        if (!empty($city)) $item[] = $city;
        // agregar zip
        if (!empty($zip)) $item[] = $zip;
        // agregar paÃ­s
        if (!empty($country)) $item[] = $country;
        // definir value
        $address = implode(' ', $item);

        //print_r($address);
        // definir result
        $results = $this->doCall(array(
            'address' => $address,
            'sensor' => 'false'
        ));
        // coordenadas de retorno latitud / longitud
        if($results != FALSE){
            return array(
                'latitude' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lat : null,
                'longitude' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lng : null
            );
        }else{

            return false;
        }
    }
}

