<?php

include('../autoload.php');
if(!isset($_SESSION['handle'])) {
    header("Location: ../index.php");
}

?>

Welcome <?php echo $_SESSION['handle']; ?>!