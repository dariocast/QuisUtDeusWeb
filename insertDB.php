<?php
/**
 * Created by PhpStorm.
 * User: dariocastellano
 * Date: 14/11/17
 * Time: 16:42
 */
require  'Medoo.php';

use Medoo\Medoo;

$percorso = "./audio/";

if(isset($_POST['titolo'])) {
    $titolo = $_POST['titolo'];
    /*$temp = date_parse($_POST['data']);
    $data = $temp["day"]."-".$temp["month"]."-".$temp["year"];*/
    $data = $_POST['data'];
    $luogo = $_POST['luogo'];
    $occasione = $_POST['occasione'];
    $momento = $_POST['momento'];
    $url="";

    if (is_uploaded_file($_FILES['file']['tmp_name'])) {
        if ($_FILES['file']['type']=="audio/mp3") {
            move_uploaded_file($_FILES['file']['tmp_name'], $percorso.$_FILES['file']['name']);
            $url = $_FILES['file']['name'];

            $database = new Medoo([
                // required
                'database_type' => 'mysql',
                'database_name' => 'my_dariocast',
                'server' => 'localhost',
                'username' => 'dariocast',
                'password' => '',
            ]);

            $database->insert('incontro',[
                'titolo' => $titolo,
                'data' => $data,
                'luogo' => $luogo,
                'occasione' => $occasione,
                'momento' => $momento,
                'url' => $url
            ]);

            setcookie("success", "true", time() + (86400 * 30), "/"); // 86400 = 1 day
            header("location: index.php");
        } else {
            setcookie("mp3error", "true", time() + (86400 * 30), "/"); // 86400 = 1 day
            header("location: index.php");
        }
    }
} else {
    setcookie("success", "false", time() + (86400 * 30), "/"); // 86400 = 1 day
    header("location: index.php");
}
