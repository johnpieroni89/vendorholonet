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
                        <h1 class="mt-4">Wares</h1>
                        <hr/>

                        <div class="container p-5">
                            <form method="get" action="">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Ware Type</label>
                                    <select name="type" class="form-control" id="wareType" aria-describedby="typeHelp">
                                    <?php
                                        $types = Ware::getWareTypes();
                                        foreach ($types as $type) {
                                            echo '<option value="'.$type.'">'.ucfirst($type).'</option>';
                                        }
                                    ?>
                                    </select>
                                    <div id="typeHelp" class="form-text">Only types registered to public vendors will show up!</div>
                                </div>
                                <button type="submit" class="btn btn-primary" style="width: 100%">Search</button>
                            </form>
                        </div>

                        <?php
                            if(isset($_GET['type'])) {
                                $wares = Ware::getWaresByType($_GET['type']);
                                echo "<div><form action='download.php'><input type='hidden' name='type' value='".$_GET['type']."'><button type='submit' class='btn btn-primary' name='download' value='download'>Download</button></form></div>";
                                Ware::printWareTable($wares);
                            } elseif(isset($_GET['container_uid'])) {
                                $wares = Ware::getWaresByContainer($_GET['container_uid']);
                                echo "<div><form action='download.php'><input type='hidden' name='type' value='".$_GET['type']."'><button type='submit' class='btn btn-primary' name='download' value='download'>Download</button></form></div>";
                                Ware::printWareTable($wares);
                            } else {
                                echo '<center><h5>Please select a type in the form above!</h5></center>';
                            }

                        ?>
                    </div>
                </main>
                <?php UserInterface::printFooter(); ?>
            </div>
        </div>
        <?php UserInterface::printScripts(); ?>
    </body>
    <script>
        <?php
            $search = '';
            if(isset($_GET['search'])) {
                $search = $_GET['search'];
            }
        ?>
        $(document).ready(function () {
            $('#wareTable').DataTable({
                searchable: true,
                pageLength: 50,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All'],
                ],
                columns: [
                    { data: 'id', render: DataTable.render.number() },
                    { data: 'type' },
                    { data: 'image' },
                    { data: 'quantity', render: DataTable.render.number() },
                    { data: 'price', render: DataTable.render.number() },
                    { data: 'vendor', render: function (data, type, row) {
                            return '<a href="vendor_profile.php?id=' + row.id + '">' + data + '</a>';
                        } },
                    { data: 'distance', render: DataTable.render.number() }
                ],
                columnDefs: [
                    {
                        targets: [2],
                        className: 'dt-body-center'
                    },
                    {
                        targets: [3, 4, 6],
                        className: 'dt-body-right'
                    },
                    {
                        targets: [0, 1, 2, 3, 4, 5, 6],
                        className: 'align-middle p-2'
                    }
                ],
                search: { search: '<?php echo $search; ?>' },
                order: [[3, 'asc']]
            });
        });
    </script>
</html>
