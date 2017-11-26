<?php
/**
 * Created by PhpStorm.
 * User: dariocastellano
 * Date: 14/11/17
 * Time: 16:42
 */
require  'Medoo.php';
require  'constants.php';

use Medoo\Medoo;

$percorso = "./audio/";

if(isset($_POST['titolo'])) {
    $titolo = filter_var(trim($_POST['titolo']), FILTER_SANITIZE_STRING);
    /*$temp = date_parse($_POST['data']);
    $data = $temp["day"]."-".$temp["month"]."-".$temp["year"];*/
    $data = filter_var(trim($_POST['data']),FILTER_SANITIZE_STRING);
    $luogo = filter_var(trim($_POST['luogo']),FILTER_SANITIZE_STRING);
    $occasione = filter_var(trim($_POST['occasione']),FILTER_SANITIZE_STRING);
    $momento = filter_var(trim($_POST['momento']),FILTER_SANITIZE_STRING);
    $url="";

    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if ($_FILES['file']['type']=="audio/mp3") {
            move_uploaded_file($_FILES['file']['tmp_name'], $percorso.$_FILES['file']['name']);
            $url = "audio_".date("m.d.y");

            $database = new Medoo([
                // required
                'database_type' => Constants::$DB_TYPE,
                'database_name' => Constants::$DB_NAME,
                'server' => Constants::$DB_SERVER,
                'username' => Constants::$DB_USERNAME,
                'password' => Constants::$DB_PASS,
            ]);

            $database->insert('incontro',[
                'titolo' => $titolo,
                'data' => $data,
                'luogo' => $luogo,
                'occasione' => $occasione,
                'momento' => $momento,
                'url' => $url
            ]);

            setcookie("success", "true", time() + (60), "/"); // 86400 = 1 day
            header("location: index.php");
        } else {
            setcookie("mp3error", "true", time() + (60), "/"); // 86400 = 1 day
            header("location: index.php");
        }
    }
} else {
    setcookie("success", "false", time() + (60), "/"); //60 = 60 secondi, 86400 = 1 day
    header("location: index.php");
}
