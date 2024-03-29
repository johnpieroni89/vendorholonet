<?php

class Vendor {

    /**
     * @property int $id
     * @property string $name
     * @property string $merchant
     * @property string $merchant_img
     * @property string $name
     * @property string $description
     * @property string $owner
     * @property ?Location $location
     */
    public $id;
    public $name;
    public $merchant;
    public $merchant_img;
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
        $location = Location::getVendorLocation($vendor->id);
        if($location){
            $vendor->location = $location;
        }
        return $vendor;
    }

    /**
     * @return Vendor[]
     */
    static function getAll(string $container_uid = '') {
        $db = new Database();
        $db->connect();

        if($container_uid) {
            $container_uid = mysqli_real_escape_string($db->connection, $container_uid);
            $vendorQuery = mysqli_query($db->connection, "SELECT vendors.id FROM vendors LEFT JOIN vendors_locations ON vendors.id = vendors_locations.vendor_id WHERE vendors_locations.container_uid = '$container_uid' ORDER BY name");
        } else {
            $vendorQuery = mysqli_query($db->connection, "SELECT id FROM vendors ORDER BY name");
        }

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
    static function parseVendor(int $id, string $name, string $merchant, string $merchant_img, $description, string $owner) {
        $db = new Database();
        $db->connect();

        $id = mysqli_real_escape_string($db->connection, $id);
        $name = mysqli_real_escape_string($db->connection, $name);
        $merchant = mysqli_real_escape_string($db->connection, $merchant);
        $merchant_img = mysqli_real_escape_string($db->connection, $merchant_img);
        $description = mysqli_real_escape_string($db->connection, $description);
        $owner = mysqli_real_escape_string($db->connection, $owner);
        str_replace('&amp;', '&', $name);

        $result = mysqli_query($db->connection, "INSERT INTO vendors (id, `name`, merchant, merchant_img, description, owner) VALUES ('$id', '$name', '$merchant', '$merchant_img', '$description', '$owner')");
        /*if ($result == false) {
            $message = "Error inputting vendor ".$id." into the database.<br/>";
            echo $message;
        }*/
    }

    static function printVendorTable($vendors, $galacticLocation=true, $groundLocation=false) {
        echo '
            <div class="card mb-4">
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
                                <th>Wares</th>';
                                if ($galacticLocation) {
                                    echo '<th>Space</th>';
                                }
                                if ($groundLocation) {
                                    echo '<th>Ground</th>';
                                }
                                echo '<th>Distance</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Wares</th>';
                                if ($galacticLocation) {
                                    echo '<th>Space</th>';
                                }
                                if ($groundLocation) {
                                    echo '<th>Ground</th>';
                                }
                                echo '<th>Distance</th>
                            </tr>
                        </tfoot>
                        <tbody>';

        foreach ($vendors as $vendor) {
            $wares = Ware::getVendorWares($vendor);
            $distance = '(In Hyperspace)';
            if($_SESSION['location']) {
                if (isset($vendor->location->galaxyCoords)) {
                    $distance = max(abs($vendor->location->galaxyCoords->x - $_SESSION['location']->x), abs($vendor->location->galaxyCoords->y - $_SESSION['location']->y));
                } else {
                    $distance='Unknown';
                }
            }
            if(count($wares)) {
                echo '
                    <tr>
                        <td>'.$vendor->id.'</td>
                        <td>'.$vendor->name.'</td>
                        <td>'.$vendor->owner.'</td>
                        <td>'.count($wares).'</td>';
                        if ($galacticLocation) {
                            echo '<td>'.($vendor->location->galaxyCoords->x ?? "?").', '.($vendor->location->galaxyCoords->y ?? "?").'</td>';
                        }
                        if ($groundLocation) {
                            echo '<td>'.$vendor->location->groundCoords->x.', '.$vendor->location->groundCoords->y.'</td>';
                        }
                        echo '<td>'.$distance.'</td>
                    </tr>
                ';
            }
        }

        echo '                    
                        </tbody>
                    </table>
                </div>
            </div>
        ';
    }

    static function printVendorProfile(Vendor $vendor) {
        $addon = '';
            if (isset ($vendor->location->groundCoords)) {
                $addon = '&surfX='.$vendor->location->surfaceCoords->x.'&surfY='.$vendor->location->surfaceCoords->y.'&groundX='.$vendor->location->groundCoords->x.'&groundY='.$vendor->location->groundCoords->y;
            }
        echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="card col-md-6 col-sm-12 p-0">
                        <div class="card-header bg-light w-100" style="text-align: center;"><h4>Vendor</h4></div>
                        <div class="card-body w-100">
                            <div class="row">
                                <div class="col-sm-12 col-md-4 align-content-center"><center><img src="'.$vendor->merchant_img.'" height="100" width="100" alt="NPC Image"></center></div>
                                <div class="col-sm-12 col-md-8">
                                    <table class="table table-bordered">
                                        <tr><td><b>Shop:</b></td><td style="text-align: left;">'.$vendor->name.'</td></tr>
                                        <tr><td><b>Merchant:</b></td><td style="text-align: left;">'.$vendor->merchant.'</td></tr>
                                        <tr><td><b>Owner:</b></td><td style="text-align: left;">'.$vendor->owner.'</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row flex-fill align-content-center"><b><u>Description</u></b></div>
                                <div class="row flex-fill align-content-start p-3">'.$vendor->description.'</div>
                            </div>
                        </div>
                        <div class="card-footer d-grid w-100" style="text-align: center;"></div>
                    </div>
                    
                    <div class="card col-md-6 col-sm-12 p-0">
                        <div class="card-header bg-light w-100" style="text-align: center;"><h4>Location</h4></div>
                        <div class="card-body w-100">
                            <table class="table table-bordered table-striped">
                                <tr><td><b>Container:</b></td><td>'.$vendor->location->container.' ('.$vendor->location->container_uid.')</td></tr>
                                <tr><td><b>Coordinates:</b></td><td>
                                    '.(($vendor->location->sector) ? 'Sector: '.$vendor->location->sector.'<br/>' : ' ').'
                                    '.((substr($vendor->location->system,0,4) != 'Deep') ? 'System: '.$vendor->location->system.' ('.$vendor->location->galaxyCoords->x.', '.$vendor->location->galaxyCoords->y.')' : 'System: '.$vendor->location->system).' <br/>
                                    '.(($vendor->location->planet) ? 'Planet: '.$vendor->location->planet.' ' : 'Space: ').' ('.$vendor->location->systemCoords->x.', '.$vendor->location->systemCoords->y.')<br/>
                                    '.(($vendor->location->city) ? 'City: '.$vendor->location->city.' ' : (($vendor->location->surfaceCoords) ? 'Surface: ' : '')).'
                                    '.(($vendor->location->surfaceCoords) ? '('.$vendor->location->surfaceCoords->x.', '.$vendor->location->surfaceCoords->y.')<br/>' : '').'
                                    '.(($vendor->location->groundCoords) ? 'Ground: ('.$vendor->location->groundCoords->x.', '.$vendor->location->groundCoords->y.')' : '').'
                                </td></tr>
                            </table>
                        </div>
                        <div class="card-footer d-grid w-100" style="text-align: center;">
                            <a target="_blank" href="https://www.swcombine.com/members/cockpit/travel/directed.php?travelClass=2&supplied=1&galX='.$vendor->location->galaxyCoords->x.'&galY='.$vendor->location->galaxyCoords->y.'&sysX='.$vendor->location->systemCoords->x.'&sysY='.$vendor->location->systemCoords->y.$addon.'">
                            <button class="btn btn-primary" style="width: 100%">Travel</button></a>';
                    echo '<br><form action="download.php"><input type="hidden" name="id" value="'.$_GET['id'].'"><button type="submit" class="btn btn-primary" style="width: 100%" name="download" value="download">Download</button></form></div></div>
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
                    <div class="card-header bg-light w-100" style="text-align: center;"><h6 class="p-0 m-0">'.$ware->type.'</h6>'.$ware->name.'</div>
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