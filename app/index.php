<?php

include('../autoload.php');
if(!isset($_SESSION['handle'])) {
    header("Location: ../index.php");
}

$ws = new WebService();
$ws->updateVendorList();