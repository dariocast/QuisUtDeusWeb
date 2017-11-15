<?php
/**
 * Created by PhpStorm.
 * User: dariocastellano
 * Date: 14/11/17
 * Time: 13:41
 */
$realm = 'Restricted area';

//user => password
$users = array('quisutdeus' => 'Qui5@pp','dario' => '232323');


if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
        '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Devi autenticarti per proseguire!');
}


// analisi della variabile PHP_AUTH_DIGEST
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
    !isset($users[$data['username']]))
    die('Nome utente o password errati!');


// generazione di una risposta valida
$A1 = md5($data['username'] . ':' . $realm . ':' . $users[$data['username']]);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response)
    die('Nome utente o password errati!');

// Ok, utente/password validi
if(isset($_COOKIE['success'])) {
    if($_COOKIE['success'] == "true") {
        echo "<script type='text/javascript'>alert('Inserimento completato con successo!');</script>";
        setcookie('success', null, -1, '/');
    } else {
        echo "<script type='text/javascript'>alert('Errore nell\'inserimento, riprovare');</script>";
        setcookie('success', null, -1, '/');
    }
}
if(isset($_COOKIE['mp3error'])) {
    echo "<script type='text/javascript'>alert('ERRORE! Selezionare un file MP3 e riprovare');</script>";
    setcookie('mp3error', null, -1, '/');
}

echo <<<EOL
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quis Ut Deus - Form di Inserimento</title>
    <link rel="stylesheet" href="./css/bootstrap.css"/>
    <link rel="script" href="./js/bootstrap.js">
</head>
<body>
<div class="jumbotron">
    <div class="container">
        <h1>Salve {$data['username']}</h1>
        <p>qui puoi caricare un nuovo incontro nel database di QuisUtDeus</p>
    </div>
</div>
<div class="container">
    <p>
    <form  enctype="multipart/form-data" action="insertDB.php" method="post" role="form" id="form" name="form" novalidate>
        <legend>Inserisci un nuovo incontro</legend>

        <div class="form-group">
            <label for="titolo">Titolo dell'incontro</label>
            <input type="text" class="form-control" name="titolo" id="titolo" placeholder="Titolo" required>
            <div class="invalid-feedback">
                Inserisci un titolo.
            </div>
        </div>
        <div class="form-group">
            <label for="data">Quando si è tenuto?</label>
            <input type="date" class="form-control" name="data" id="data" placeholder="Data" required>
            <div class="invalid-feedback">
                Seleziona una data.
            </div>
        </div>
        <div class="form-group">
            <label for="luogo">Dove si è tenuto?</label>
            <input type="text" class="form-control" name="luogo" id="luogo" placeholder="Luogo" required>
            <div class="invalid-feedback">
                Inserisci un luogo.
            </div>
        </div>
        <div class="form-group">
            <label for="occasione">Quale era l'occasione?</label>
            <input type="text" class="form-control" name="occasione" id="occasione" placeholder="Occasione" required>
            <div class="invalid-feedback">
                Inserisci l'occasione.
            </div>
        </div>
        <div class="form-group">
            <label for="momento">Quale era il momento?</label>
            <select class="form-control" id="momento" name="momento" required>
                <option value="celebrazione" selected="selected">Celebrazione</option>
                <option value="preghiera">Preghiera</option>
            </select>
            <div class="invalid-feedback">
                Seleziona il tipo di incontro.
            </div>
        </div>
        <div class="form-group">
            <label for="file">Carica il file audio</label>
            <input type="file" class="form-control" name="file" id="file" accept=".mp3,.ogg" placeholder="File Mp3" required>
            <div class="invalid-feedback">
                Carica un file mp3.
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Inserisci</button>
    </form>
    </p>
</div>
<script>
    (function() {
        'use strict';

        window.addEventListener('load', function() {
            var form = document.getElementById('form');
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }, false);
    })();
</script>
</body>
</html>
EOL;


// funzione che analizza l'header http auth
function http_digest_parse($txt)
{
    // protezione contro i dati mancanti
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}
