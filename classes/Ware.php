<?php

class Ware {
    /**
     * @property int $id
     * @property int $vendor_id
     * @property string $type
     * @property string $name
     * @property int $quantity
     * @property int $price
     * @property string $currency
     * @property string $imgSmall
     * @property string $imgLarge
     */
    public $id;
    public $vendor_id;
    public $type;
    public $name;
    public $quantity;
    public $price;
    public $currency;
    public $imgSmall;
    public $imgLarge;

    function __construct(stdClass $ware) {
        $this->id = $ware->id;
        $this->vendor_id = $ware->vendor_id;
        $this->type = $ware->type;
        $this->name = $ware->name;
        $this->quantity = $ware->quantity;
        $this->price = $ware->price;
        $this->currency = $ware->currency;
        $this->imgSmall = $ware->imgSmall;
        $this->imgLarge = $ware->imgLarge;
    }

    /**
     * get ware object by id
     * @param int $id
     * @return Ware
     */
    static function getWareById(int $id): Ware {
        $db = new Database();
        $db->connect();
        $id = mysqli_real_escape_string($db->connection, $id);

        $ware = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors_wares WHERE id = '$id'"));
        return new Ware($ware);
    }

    /**
     * Get all vendor wares
     * @param Vendor $vendor
     * @return Ware[]
     */
    static function getVendorWares(Vendor $vendor): array {
        $db = new Database();
        $db->connect();
        $vendor_id = mysqli_real_escape_string($db->connection, $vendor->id);
        $wareQuery = mysqli_query($db->connection, "SELECT id FROM vendors_wares WHERE vendor_id = '$vendor_id'");
        $wareArr = array();

        while($row = mysqli_fetch_object($wareQuery)) {
            $wareArr[] = Ware::getWareById($row->id);
        }
        return $wareArr;
    }

    /**
     * Get count of wares for entire mall
     * @param string $container_uid
     * @param bool $unique
     * @return int
     */
    static function getMallWares(string $container_uid, bool $unique = false) {
        $db = new Database();
        $db->connect();
        $container_uid = mysqli_real_escape_string($db->connection, $container_uid);

        if($unique) {
            $mallVendors = mysqli_query($db->connection, "SELECT DISTINCT `vendors_wares`.`type` FROM `vendors_wares` LEFT JOIN `vendors_locations` ON `vendors_wares`.`vendor_id` = `vendors_locations`.`vendor_id` WHERE `vendors_locations`.`container_uid` = '$container_uid'");
            $total = mysqli_num_rows($mallVendors);
        } else {
            $mallVendors = mysqli_fetch_object(mysqli_query($db->connection, "SELECT COUNT(`vendors_wares`.`id`) AS `total` FROM `vendors_wares` LEFT JOIN `vendors_locations` ON `vendors_wares`.`vendor_id` = `vendors_locations`.`vendor_id` WHERE `vendors_locations`.`container_uid` = '$container_uid'"));
            $total = $mallVendors->total;
        }

        return $total;
    }

    static function getWareTypes() {
        $db = new Database();
        $db->connect();
        $wareQuery = mysqli_query($db->connection, "SELECT DISTINCT type FROM vendors_wares ORDER BY type ASC");
        $wareArr = array();

        while($row = mysqli_fetch_object($wareQuery)) {
            $wareArr[] = $row->type;
        }
        return $wareArr;
    }

    /**
     * Get all vendor wares
     * @param string $type
     * @return Ware[]
     */
    static function getWaresByType(string $type): array {
        $db = new Database();
        $db->connect();
        $type = mysqli_real_escape_string($db->connection, $type);
        $wareQuery = mysqli_query($db->connection, "SELECT id, vendor_id, type, quantity, price FROM vendors_wares WHERE type = '$type' ORDER BY price ASC");
        $wareArr = array();

        while($row = mysqli_fetch_object($wareQuery)) {
            $wareArr[] = Ware::getWareById($row->id);
        }
        return $wareArr;
    }

    /**
     * Get all vendor wares for a container
     * @param string $container_uid
     * @return Ware[]
     */
    static function getWaresByContainer(string $container_uid): array {
        $db = new Database();
        $db->connect();
        $container_uid = mysqli_real_escape_string($db->connection, $container_uid);
        $wareQuery = mysqli_query($db->connection, "
                                SELECT vendors_wares.id, vendors_wares.vendor_id, vendors_wares.type, vendors_wares.quantity, vendors_wares.price 
                                FROM vendors_wares LEFT JOIN vendors_locations ON vendors_wares.vendor_id = vendors_locations.vendor_id 
                                WHERE vendors_locations.container_uid = '$container_uid' ORDER BY price ASC");
        $wareArr = array();

        while($row = mysqli_fetch_object($wareQuery)) {
            $wareArr[] = Ware::getWareById($row->id);
        }
        return $wareArr;
    }

    /**
     * delete all vendor wares
     */
    static function deleteAll() {
        $db = new Database();
        $db->connect();
        mysqli_query($db->connection, "TRUNCATE TABLE vendors_wares");
    }

    /**
     * add or update ware from api
     * @param int $vendor_id
     * @param string $type
     * @param int $quantity
     * @param int $price
     * @param string $currency
     * @param string $imgSmall
     * @param string $imgLarge
     * @return void
     */
    static function parseWare(int $vendor_id, string $type, string $name, int $quantity, int $price, string $currency, string $imgSmall, string $imgLarge) {
        $db = new Database();
        $db->connect();

        $vendor_id = mysqli_real_escape_string($db->connection, $vendor_id);
        $type = mysqli_real_escape_string($db->connection, $type);
        $name = mysqli_real_escape_string($db->connection, $name);
        $quantity = mysqli_real_escape_string($db->connection, $quantity);
        $price = mysqli_real_escape_string($db->connection, $price);
        $currency = mysqli_real_escape_string($db->connection, $currency);
        $imgSmall = mysqli_real_escape_string($db->connection, $imgSmall);
        $imgLarge = mysqli_real_escape_string($db->connection, $imgLarge);

        mysqli_query($db->connection, "INSERT INTO vendors_wares (vendor_id, type, name, quantity, price, currency, imgSmall, imgLarge) 
                     VALUES ('$vendor_id', '$type', '$name', '$quantity', '$price', '$currency', '$imgSmall', '$imgLarge')");
    }

    static function printWareTable($wares) {
        echo '
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Ware Registry
                </div>
                <div class="card-body">
                    <table id="wareTable" class="table table-sm table-striped table-responsive table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Img</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Vendor</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Img</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Vendor</th>
                                <th>Distance</th>
                            </tr>
                        </tfoot>
                        <tbody>';

        foreach ($wares as $ware) {
            $distance = '(In Hyperspace)';
            $vendor = Vendor::getVendor($ware->vendor_id);
            if($_SESSION['location']) {
                $distance = max(abs($vendor->location->galaxyCoords->x - $_SESSION['location']->x), abs($vendor->location->galaxyCoords->y - $_SESSION['location']->y));
            }
            echo '
                <tr>
                    <td>'.$ware->vendor_id.'</td>
                    <td>'.$ware->type.'<br/>'.$ware->name.'</td>
                    <td><img height="70" width="70" src="'.$ware->imgSmall.'"/></td>
                    <td>'.$ware->quantity.'</td>
                    <td>'.$ware->price.'</td>
                    <td>'.$vendor->name.'</td>
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
}