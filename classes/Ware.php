<?php

class Ware {
    /**
     * @property int $id
     * @property int $vendor_id
     * @property string $type
     * @property int $quantity
     * @property int $price
     * @property string $currency
     * @property string $imgSmall
     * @property string $imgLarge
     */
    public $id;
    public $vendor_id;
    public $type;
    public $quantity;
    public $price;
    public $currency;
    public $imgSmall;
    public $imgLarge;

    function __construct(stdClass $ware) {
        $this->id = $ware->id;
        $this->vendor_id = $ware->vendor_id;
        $this->type = $ware->type;
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
        $wareQuery = mysqli_query($db->connection, "SELECT id FROM vendors_wares WHERE vendor_id = '$vendor->id'");
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
    static function parseWare(int $vendor_id, string $type, int $quantity, int $price, string $currency, string $imgSmall, string $imgLarge) {
        $db = new Database();
        $db->connect();

        $vendor_id = mysqli_real_escape_string($db->connection, $vendor_id);
        $type = mysqli_real_escape_string($db->connection, $type);
        $quantity = mysqli_real_escape_string($db->connection, $quantity);
        $price = mysqli_real_escape_string($db->connection, $price);
        $currency = mysqli_real_escape_string($db->connection, $currency);
        $imgSmall = mysqli_real_escape_string($db->connection, $imgSmall);
        $imgLarge = mysqli_real_escape_string($db->connection, $imgLarge);

        var_dump("INSERT INTO vendors_wares (vendor_id, type, quantity, price, currency, imgSmall, imgLarge) 
                     VALUES ('$vendor_id', '$type', '$quantity', '$price', '$currency', '$imgSmall', '$imgLarge')");
        mysqli_query($db->connection, "INSERT INTO vendors_wares (vendor_id, type, quantity, price, currency, imgSmall, imgLarge) 
                     VALUES ('$vendor_id', '$type', '$quantity', '$price', '$currency', '$imgSmall', '$imgLarge')");
    }
}