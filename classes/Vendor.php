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
    static function getAll(): array {
        $db = new Database();
        $db->connect();
        $vendorIdList = mysqli_fetch_object(mysqli_query($db->connection, "SELECT id FROM vendors ORDER BY id"));
        $vendorArr = array();
        foreach ($vendorIdList as $vendorId) {
            $vendorArr[] = Vendor::getVendor($vendorId);
        }
        return $vendorArr;
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

        if(self::getVendor($id)) {
            mysqli_query($db->connection, "UPDATE vendors SET name = '$name', description = '$description', owner = '$owner' WHERE id = '$id'");
        } else {
            mysqli_query($db->connection, "INSERT INTO vendors (id, name, description, owner) VALUES ('$id', '$name', '$description', '$owner')");
        }
    }
}