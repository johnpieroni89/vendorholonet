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
                <h1 class="mt-4">Crontab</h1>
                <hr/>
                <div class="container-fluid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                        <?php
                            $vendorSchedule = '0 */8 * * *';
                            $topLevelDir = dirname(dirname(__DIR__));
                            $vendorScript = $topLevelDir."/scripts/updateVendors.php";
                            $vendorcommand = 'php '.$vendorScript;
                            Echo "Site url: ".UserInterface::siteURL();
                            Echo "<br>This directory: ".__DIR__;
                            Echo "<br>Top level: ".$topLevelDir;

                            Echo "<br><br>Current crontab: <br>".CronTab::view();
                            
                            Echo "<br><br>Schedule for updateVendors: ".CronTab::getScheduleForCommand($vendorcommand);
                            if (CronTab::getScheduleForCommand($vendorcommand) != $vendorSchedule) {
                                echo "<br><br>Vendor script cronjob out of sync, fixing...";
                                Crontab::replace($vendorcommand,$vendorcommand,$vendorSchedule);
                                echo "<br>New schedule for updateVendors: ".CronTab::getScheduleForCommand($vendorcommand);
                            }
                        ?>
                        <a href="index.php"><button class="btn btn-lg btn-primary"><i class="fa-solid fa-users-viewfinder"></i> Back to admin panel</button></a>
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

