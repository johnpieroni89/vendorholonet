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

                        <form>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Ware Type</label>
                                <select class="form-control" id="wareType" aria-describedby="typeHelp">
                                    <option></option>
                                </select>
                                <div id="typeHelp" class="form-text">Only types registered to public vendors will show up!</div>
                            </div>
                            <button type="submit" class="btn btn-primary">Search</button>
                        </form>

                        <?php
                            if(isset($_GET['type'])) {
                                $wares = Ware::getWaresByType($_GET['type']);
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
                    { data: 'quantity', render: DataTable.render.number() },
                    { data: 'price', render: DataTable.render.number() },
                    { data: 'vendor', render: function (data, type, row) {
                            return '<a href="vendor_profile.php?id=' + row.id + '">' + data + '</a>';
                        } },
                    { data: 'distance', render: DataTable.render.number() }
                ],
                columnDefs: [{
                    targets: [2, 3, 5],
                    className: 'dt-body-right'
                }],
                search: { search: '<?php echo $search; ?>' }
            });
        });
    </script>
</html>
