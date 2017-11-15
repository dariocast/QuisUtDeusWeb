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

$datiIncontri = $database->select("incontro", [
    'titolo', 'data', 'luogo', 'occasione', 'momento', 'url'
], [
    "ORDER" => ["data" => "DESC"],
    "LIMIT" => 20
]);

$incontri = array();
foreach($datiIncontri as $incontro)
{
    $temp = date_parse($incontro['data']);
    $data = $temp["day"]."-".$temp["month"]."-".$temp["year"];
    $incontri[] =  array(
        "titolo" => $incontro['titolo'],
        "data" => $data,
        "luogo" => $incontro['luogo'],
        "occasione" => $incontro['occasione'],
        "momento" => $incontro['momento'],
        "mediaType" => "",
        "url" => "http://dariocast.altervista.org/quisutdeus/audio/".$incontro['url']
    );
}

function mySort($a, $b)
{
    return $a['data'] < $b['data'];
}

usort($incontri, "mySort");
echo json_encode($incontri);