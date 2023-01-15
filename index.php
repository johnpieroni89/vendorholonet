<?php

include('autoload.php');
if(!isset($_SESSION['handle'])) {
    header("Location: oauth/index.php");
} else {
    header("Location: app/index.php");
}