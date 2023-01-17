<?php 

include('../autoload.php');

$scopes = [
    'CHARACTER_AUTH',
    'CHARACTER_READ',
    'CHARACTER_LOCATION'
];

$auth = new SWC();
$auth->AttemptAuthorize($scopes);

?>