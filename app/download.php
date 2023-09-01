<?php 
include('../autoload.php');
if(!isset($_SESSION['handle'])) {
    header("Location: ../index.php");
}



if(isset($_GET['type'])) {
    $wares = Ware::getWaresByType($_GET['type']);
} elseif(isset($_GET['container_uid'])) {
    $wares = Ware::getWaresByContainer($_GET['container_uid']);
} elseif(isset($_GET['id'])) {
    $vendor = Vendor::getVendor($_GET['id']);
    $wares = Ware::getVendorWares($vendor);
} else {
    echo "<center><h5><a href='index.php'>Download selected without content, return to home.</a></h5></center>";
}

if (isset($_GET['download'])) {
        Ware::saveWareTable($wares);
}

if(isset($_GET['type'])) {
    header("Location: wares.php?type=".$_GET['type']);
} elseif(isset($_GET['container_uid'])) {
    header("Location: wares.php?container_uid=".$_GET['container_uid']);
} else {
    echo "<center><h5><a href='index.php'>Download selected without content, return to home.</a></h5></center>";
}