<?php

include('config.php');
include('classes/Database.php');
include('classes/User.php');
include('classes/Vendor.php');
include('classes/Location.php');
include('classes/Ware.php');
include('classes/WebService.php');

$ws = new WebService();
$ws->updateVendorList();