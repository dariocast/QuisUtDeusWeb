<?php
require  'Medoo.php';

use Medoo\Medoo;

$database = new Medoo([
    // required
    'database_type' => 'mysql',
    'database_name' => 'my_dariocast',
    'server' => 'localhost',
    'username' => 'dariocast',
    'password' => '',
]);

$datas = $database->select("incontro", [
    'titolo', 'data', 'luogo', 'occasione', 'momento', 'url'
], [
    //no where clause
]);

$incontri = array();
foreach($datas as $data)
{
    $incontri[] =  array(
        "titolo" => $data['titolo'],
        "data" => $data['data'],
        "luogo" => $data['luogo'],
        "occasione" => $data['occasione'],
        "momento" => $data['momento'],
        "mediaType" => "",
        "url" => "http://dariocast.altervista.org/quisutdeus/audio/".$data['url']
    );
}

function mySort($a, $b)
{
    return $a['data'] < $b['data'];
}

usort($incontri, "mySort");
echo json_encode($incontri);