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
<?php UserInterface::printHead(1); ?>
<body class="sb-nav-fixed">
<?php UserInterface::printNav(1); ?>
<div id="layoutSidenav">
    <?php UserInterface::printSideNav(1); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Malls</h1>
                <hr/>
                <div class="container-fluid">
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
                        <?php
                            $db = new Database();
                            $db->connect;
                            $sessionsSQL = mysqli_query($db->connection, "SELECT * FROM sessions ORDER BY date_active DESC");

                        echo '
                            <div class="card mb-4">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    Active Sessions
                                </div>
                                <div class="card-body">
                                    <table id="vendorTable" class="table table-sm table-striped table-responsive table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Handle</th>
                                                <th>Activity Timestamp</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>ID</th>
                                                <th>Handle</th>
                                                <th>Activity Timestamp</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>';

                            while($row = mysqli_fetch_object($sessionsSQL)) {
                                echo '
                                    <tr>
                                        <td>'.$row->id.'</td>
                                        <td>'.$row->handle.'</td>
                                        <td>'.$row->date_active.'</td>
                                    </tr>
                                ';
                            }

                        echo '                    
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        ';
                        ?>
                    </div>
                </div>
            </div>
        </main>
        <?php UserInterface::printFooter(1); ?>
    </div>
</div>
<?php UserInterface::printScripts(1); ?>
</body>
</html>
