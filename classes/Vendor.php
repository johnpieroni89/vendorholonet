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
    //public $location; // Location obj

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
        //$vendor->location = Location::getVendorLocation($vendor->id);
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
            echo '
                <tr>
                    <td>'.$vendor->id.'</td>
                    <td>'.$vendor->name.'</td>
                    <td>Name of vendor owner soon!</td>
                    <td>'.count($wares).'</td>
                    <td>0</td>
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
                    <div class="card col-sm-4">
                        <div class="card-header bg-primary flex-fill">Vendor</div>
                        <div class="card-body">blah</div>
                        <div class="card-footer bg-secondary"><button>Message</button></div>
                    </div>
                    
                    <div class="card col-sm-2 offset-1">
                        <div class="card-header bg-primary">Metrics</div>
                        <div class="card-body">blah</div>
                        <div class="card-footer bg-secondary"></div>
                    </div>
                    
                    <div class="card col-sm-4 offset-1">
                        <div class="card-header bg-primary">Location</div>
                        <div class="card-body">blah</div>
                        <div class="card-footer bg-secondary"><button>Travel</button></div>
                    </div>
                </div>
            </div>
        ';
    }
}