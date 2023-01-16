<?php

class Location {
    /**
     * @property int $vendor_id
     * @property string $container
     * @property string $sector
     * @property string $system
     * @property string $planet
     * @property string $city
     * @property Point $galaxyCoords
     * @property Point $systemCoords
     * @property Point $surfaceCoords
     * @property Point $groundCoords
     */
    public $id;
    public $vendor_id;
    public $container;
    public $sector;
    public $system;
    public $planet;
    public $city;
    public $galaxyCoords;
    public $systemCoords;
    public $surfaceCoords;
    public $groundCoords;

    function __construct(stdClass $location) {
        $this->id = $location->id;
        $this->vendor_id = $location->vendor_id;
        $this->container = $location->container;
        $this->sector = $location->sector;
        $this->system = $location->system;
        $this->planet = $location->planet;
        $this->city = $location->city;
        $this->galaxyCoords = new Point($location->galx, $location->galy);
        $this->systemCoords = new Point($location->sysx, $location->sysy);
        $this->surfaceCoords = new Point($location->surfx, $location->surfy);
        $this->groundCoords = new Point($location->groundx, $location->groundy);
    }

    /**
     * Return a vendor's location
     * @param Vendor $vendor
     * @return Location|void
     */
    static function getVendorLocation(Vendor $vendor): ?Location {
        $db = new Database();
        $db->connect();
        $location = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors_locations WHERE id = '$vendor->id'"));
        if($location) {
            return new Location($location);
        }
    }

    /**
     * add or update vendor location from api
     * @param int $vendor_id
     * @param $container
     * @param $sector
     * @param $system
     * @param $planet
     * @param $city
     * @param $galx
     * @param $galy
     * @param $sysx
     * @param $sysy
     * @param $surfx
     * @param $surfy
     * @param $groundx
     * @param $groundy
     * @return void
     */
    static function parseLocation(int $vendor_id, $container, $sector, $system, $planet, $city, $galx, $galy, $sysx, $sysy, $surfx, $surfy, $groundx, $groundy) {
        $db = new Database();
        $db->connect();
        $vendor = Vendor::getVendor($vendor_id);

        // escape string

        if(! self::getVendorLocation($vendor)) {
            mysqli_query($db->connection, "UPDATE vendors_locations SET container = '$container', sector = '$sector', `system` = '$system', 
                         planet = '$planet', city = '$city', galx = '$galx', galy = '$galy', sysx = '$sysx', sysy = '$sysy', 
                         surfx = '$surfx', surfy = '$surfy', groundx = '$groundx', groundy = '$groundy' WHERE vendor_id = '$vendor->id'");
        } else {
            mysqli_query($db->connection, "INSERT INTO vendors_locations (vendor_id, container, sector, `system`, planet, city, galx, galy, sysx, sysy, surfx, surfy, groundx, groundy) 
                         VALUES ('$vendor_id', '$container', '$sector', '$system', '$planet', '$city', '$galx', '$galy', '$sysx', '$sysy', '$surfx', '$surfy', '$groundx', '$groundy')");
        }
    }
}

class Point {
    public $x;
    public $y;

    /**
     * @param int $x
     * @param int $y
     */
    function __construct(int $x, int $y) {
        $this->x = $x;
        $this->y = $y;
    }
}