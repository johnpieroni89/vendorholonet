<?php 

include('../autoload.php');

$scopes = [
    'CHARACTER_AUTH',
    'CHARACTER_READ'
];

$auth = new SWC();
$auth->AttemptAuthorize($scopes);

?>