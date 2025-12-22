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
                <h1 class="mt-4">Admin</h1>
                <hr/>
                <div class="container-fluid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                        <a href="sessions.php"><button class="btn btn-lg btn-primary"><i class="fa-solid fa-users-viewfinder"></i> Sessions</button></a>
                        <a href="updateVendorsManual.php"><button class="btn btn-lg btn-primary"><i class="fa-solid fa-arrows-rotate"></i> Update info</button></a>
                        <a href="cronTab.php"><button class="btn btn-lg btn-primary"><i class="fa-solid fa-clock-rotate-left"></i> Crontab</button></a>
                        <a href="displayLogfile.php"><button class="btn btn-lg btn-primary"><i class="fa-solid fa-list"></i> Log file</button></a>
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
