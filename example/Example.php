<?php

use Angelo\GeoLocation\GoogleAddress;
use Angelo\GeoLocation\GoogleMap;

//iniciando class
$map = new GoogleMap('KEY');

//passe o endereco completo, rua, numero, cidade, cep, estado
$latiLong = $map->getCoordinates('Av visconde de guarapuava','2894','curitiba','','Parana');

echo '<pre> ';
echo 'latitude e longitude <br>';
print_r($latiLong);
echo '<br>';

//location (latitude e longitude)
$result = $map->setLocation(-25.4370031, -49.2694293);

//Retorna endereco completo
echo 'retorna endereco completo <br>';
print_r($result->getFullAddress());
echo '<br>';

//Outras consultas
echo 'cidade <br>';
print_r($result->getComponentsAddress( GoogleAddress::CIDADE));
echo 'Bairro <br>';
print_r($result->getComponentsAddress( GoogleAddress::BAIRRO));
echo 'Estado <br>';
print_r($result->getComponentsAddress( GoogleAddress::ESTADO));
echo 'Numero <br>';
print_r($result->getComponentsAddress( GoogleAddress::NUMERO));
echo 'Pais <br>';
print_r($result->getComponentsAddress( GoogleAddress::PAIS));
echo 'Cep <br>';
print_r($result->getComponentsAddress( GoogleAddress::CEP));
echo 'Rua <br>';
print_r($result->getComponentsAddress( GoogleAddress::RUA));
echo '</pre>';
die();