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
                <h1 class="mt-4">Sessions</h1>
                <hr/>
                <?php
                    $db = new Database();
                    $db->connect();
                    $sessionsSQL = mysqli_query($db->connection, "SELECT * FROM sessions ORDER BY date_active DESC");

                    echo '
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Active Sessions
                            </div>
                            <div class="card-body">
                                <table id="sessionTable" class="table table-sm table-striped table-responsive table-hover table-bordered">
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
                                    <tbody>
                    ';

                        while($row = mysqli_fetch_object($sessionsSQL)) {
                            echo '
                                <tr>
                                    <td>'.$row->id.'</td>
                                    <td>'.$row->handle.'</td>
                                    <td>'.date('d M Y H:i:s', $row->date_active).'</td>
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
        </main>
        <?php UserInterface::printFooter(1); ?>
    </div>
</div>
<?php UserInterface::printScripts(1); ?>
</body>
<script>
    <?php
    $search = '';
    if(isset($_GET['search'])) {
        $search = $_GET['search'];
    }
    ?>
    $(document).ready(function () {
        $('#sessionTable').DataTable({
            searchable: true,
            pageLength: 50,
            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, 'All'],
            ],
            columns: [
                { data: 'id', render: DataTable.render.number() },
                { data: 'handle' },
                { data: 'timestamp' },
            ],
        });
    });
</script>
</html>
