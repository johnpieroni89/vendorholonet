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
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <?php Vendor::printVendorTable(Vendor::getAll()); ?>
                    </div>
                </main>
                <?php UserInterface::printFooter(); ?>
            </div>
        </div>
        <?php UserInterface::printScripts(); ?>
    </body>
</html>
