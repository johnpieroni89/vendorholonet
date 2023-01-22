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
                        <?php
                            if(isset($_GET['container_uid'])) {
                                Vendor::printVendorTable(Vendor::getAll($_GET['container_uid']));
                            } else {
                                Vendor::printVendorTable(Vendor::getAll());
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
            $('#vendorTable').DataTable({
                searchable: true,
                pageLength: 50,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All'],
                ],
                columns: [
                    { data: 'id', render: DataTable.render.number() },
                    { data: 'name', render: function (data, type, row) {
                            return '<a href="vendor_profile.php?id=' + row.id + '">' + data + '</a>';
                        } },
                    { data: 'owner' },
                    { data: 'wares', render: DataTable.render.number() },
                    { data: 'distance', render: DataTable.render.number() }
                ],
                columnDefs: [{
                    targets: [3, 4],
                    className: 'dt-body-right'
                }],
                search: { search: '<?php echo $search; ?>' },
                order: [[4, 'asc']]
            });
        });
    </script>
</html>
