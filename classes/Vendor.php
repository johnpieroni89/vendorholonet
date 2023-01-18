<?php

class Vendor {

    /**
     * @property int $id
     * @property string $name
     * @property string $description
     * @property string $owner
     * @property Location $location
     */
    public $id;
    public $name;
    public $description;
    public $owner;
    public $location; // Location obj

    /**
     * Get Vendor object
     * @param int $id
     * @return Vendor|false
     */
    static function getVendor(int $id): ?Vendor {
        $db = new Database();
        $db->connect();
        $id = mysqli_real_escape_string($db->connection, $id);
        $vendor = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors WHERE id = '$id'"), 'Vendor');
        $vendor->location = Location::getVendorLocation($vendor->id);
        return $vendor;
    }

    /**
     * @return Vendor[]
     */
    static function getAll() {
        $db = new Database();
        $db->connect();
        $vendorQuery = mysqli_query($db->connection, "SELECT id FROM vendors ORDER BY name");
        $vendorArr = array();
        while($row = mysqli_fetch_object($vendorQuery)) {
            $vendorArr[] = Vendor::getVendor($row->id);
        }
        return $vendorArr;
    }

    static function deleteAll() {
        $db = new Database();
        $db->connect();
        mysqli_query($db->connection, "TRUNCATE TABLE vendors");
    }

    /**
     * add or update vendor from api
     * @param int $id
     * @param string $name
     * @param string $description
     * @param string $owner
     * @return void
     */
    static function parseVendor(int $id, string $name, $description, string $owner) {
        $db = new Database();
        $db->connect();

        $id = mysqli_real_escape_string($db->connection, $id);
        $name = mysqli_real_escape_string($db->connection, $name);
        $description = mysqli_real_escape_string($db->connection, $description);
        $owner = mysqli_real_escape_string($db->connection, $owner);
        str_replace('&amp;', '&', $name);

        if(self::getVendor($id)) {
            mysqli_query($db->connection, "UPDATE vendors SET name = '$name', description = '$description', owner = '$owner' WHERE id = '$id'");
        } else {
            mysqli_query($db->connection, "INSERT INTO vendors (id, name, description, owner) VALUES ('$id', '$name', '$description', '$owner')");
        }
    }

    static function printVendorTable($vendors) {
        echo '
            <div class="card mb-4" style="overflow-x: scroll; overflow-y: scroll;">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Vendor Registry
                </div>
                <div class="card-body">
                    <table id="vendorTable" class="table table-sm table-striped table-responsive table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Wares</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Wares</th>
                                <th>Distance</th>
                            </tr>
                        </tfoot>
                        <tbody>';

        foreach ($vendors as $vendor) {
            $wares = Ware::getVendorWares($vendor);
            $distance = '(In Hyperspace)';
            if($_SESSION['location']) {
                $distance = max(abs($vendor->location->galaxyCoords->x - $_SESSION['location']->x), abs($vendor->location->galaxyCoords->y - $_SESSION['location']->y));
            }
            echo '
                <tr>
                    <td>'.$vendor->id.'</td>
                    <td>'.$vendor->name.'</td>
                    <td>Name of vendor owner soon!</td>
                    <td>'.count($wares).'</td>
                    <td>'.$distance.'</td>
                </tr>
            ';
        }

        echo '                    
                        </tbody>
                    </table>
                </div>
            </div>
        ';
    }

    static function printVendorProfile(Vendor $vendor) {
        echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-6 col-sm-6 offset-sm-0 p-0">
                        <div class="card-header bg-light w-100" style="text-align: center;"><h4>Vendor</h4></div>
                        <div class="card-body w-100">
                            <p><b>Shop Name</b>: '.$vendor->name.'</p>
                            <p><b>Shop Description</b>: '.$vendor->description.'</p>
                            <p><b>Owner</b>: (work in progress)</p>
                        </div>
                        <div class="card-footer d-grid w-100" style="text-align: center;"><button class="btn btn-primary btn-lg">Message</button></div>
                    </div>
                    
                    <div class="card col-6 col-sm-5 offset-sm-1 p-0">
                        <div class="card-header bg-light w-100" style="text-align: center;"><h4>Location</h4></div>
                        <div class="card-body w-100">(work in progress)</div>
                        <div class="card-footer d-grid w-100" style="text-align: center;"><button class="btn btn-primary btn-lg">Travel</button></div>
                    </div>
                </div>
            </div>
            <hr/>
            
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">
        ';

        $wares = Ware::getVendorWares($vendor);
        foreach ($wares as $ware) {
            echo '
                <div class="col card p-1">
                    <div class="card-header bg-light w-100" style="text-align: center; height: 60px;"><h6>'.$ware->type.'</h6></div>
                    <div class="card-body w-100" style="text-align: center;"><img src="'.$ware->imgSmall.'"></div>
                    <div class="card-footer d-grid w-100" style="text-align: center;">
                        <table class="table table-striped table-sm table-bordered">
                            <tr><td>Price:</td><td style="text-align: right; padding-right: 10px;">'.number_format($ware->price).'</td></tr>
                            <tr><td>Qty:</td><td style="text-align: right; padding-right: 10px;">'.number_format($ware->quantity).'</td></tr>
                        </table>
                    </div>
                </div>
            ';
        }

        echo '
                </div>
            </div>
        ';
    }
}