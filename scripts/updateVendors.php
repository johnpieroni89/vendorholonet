<?php

include(__DIR__.'/../config.php');
include(__DIR__.'/../classes/Database.php');
include(__DIR__.'/../classes/User.php');
include(__DIR__.'/../classes/Vendor.php');
include(__DIR__.'/../classes/Location.php');
include(__DIR__.'/../classes/Ware.php');
include(__DIR__.'/../classes/WebService.php');

/*
$ws = new WebService();
$ws->updateVendorList();
*/
var_dump(User::getCharacter(User::getUID('Cedron Tryonel'), '69cd8a3470701736ee31b1829c2aa980'));