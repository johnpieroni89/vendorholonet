<?php

class Ware {
    /**
     * @property int $id
     * @property int $vendor_id
     * @property string $type
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
        $wareIdList = mysqli_fetch_object(mysqli_query($db->connection, "SELECT id FROM vendors_wares WHERE vendor_id = '$vendor->id'"));
        $wareArr = [];
        foreach ($wareIdList as $wareId) {
            $wareArr[] = Ware::getWareById($wareId);
        }
        return $wareArr;
    }
}