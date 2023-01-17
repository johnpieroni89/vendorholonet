<?php

include('../autoload.php');
if(!isset($_SESSION['handle'])) {
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
                        <h1 class="mt-4">Vendors</h1>
                        <hr/>
                        <?php Vendor::printVendorTable(Vendor::getAll()); ?>
                    </div>
                </main>
                <?php UserInterface::printFooter(); ?>
            </div>
        </div>
        <?php UserInterface::printScripts(); ?>
    </body>
</html>
