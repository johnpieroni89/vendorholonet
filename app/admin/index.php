<?php

include('../../autoload.php');
if(!isset($_SESSION['handle'])) {
    header("Location: ../../index.php");
}
if(!$_SESSION['handle'] == CONFIG_GLOBAL_ADMIN) {
    header("Location: ../index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<?php UserInterface::printHead(); ?>
<body class="sb-nav-fixed">
<?php UserInterface::printNav(); ?>
<div id="layoutSidenav">
    <?php UserInterface::printSideNav(); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Malls</h1>
                <hr/>
                <div class="container-fluid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                        <a href="sessions.php">Sessions</a>
                    </div>
                </div>
            </div>
        </main>
        <?php UserInterface::printFooter(); ?>
    </div>
</div>
<?php UserInterface::printScripts(); ?>
</body>
</html>
