<?php

include('autoload.php');
UserInterface::printHead();
if(isset($_SESSION['handle'])) {
    header("Location: app/index.php");
}

?>

<div class="wrapper fadeInDown">
    <div id="formContent">
        <!-- Tabs Titles -->

        <!-- Icon -->
        <div class="fadeIn first">
            <img src="app/assets/img/logo-large.png" id="logo" alt="Vendor Holonet" />
        </div>

        <!-- Login Form -->
        <div class="d-grid gap-2">
            <a href="oauth/index.php"><button type="button" class="btn btn-lg btn-secondary">Login</button></a>
        </div>

    </div>
</div>