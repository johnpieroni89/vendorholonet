<?php

class Location {
    /**
     * @property int $vendor_id
     * @property string $container
     * @property string $container_uid
     * @property string $sector
     * @property string $system
     * @property string $planet
     * @property string $city
     * @property string $city_uid
     * @property Point $galaxyCoords
     * @property Point $systemCoords
     * @property Point $surfaceCoords
     * @property Point $groundCoords
     */
    public $id;
    public $vendor_id;
    public $container;
    public $container_uid;
    public $sector;
    public $system;
    public $planet;
    public $city;
    public $city_uid;
    public $galaxyCoords;
    public $systemCoords;
    public $surfaceCoords;
    public $groundCoords;

    function __construct(stdClass $location) {
        $this->id = $location->id;
        $this->vendor_id = $location->vendor_id;
        $this->container = $location->container;
        $this->container_uid = $location->container_uid;
        $this->sector = $location->sector;
        $this->system = $location->system;
        $this->planet = $location->planet;
        $this->city = $location->city;
        $this->city_uid = $location->city_uid;

        $this->galaxyCoords = null;
        if($location->galx && $location->galy) {
            $this->galaxyCoords = new Point($location->galx, $location->galy);
        }

        $this->systemCoords = null;
        if($location->sysx && $location->sysy) {
            $this->systemCoords = new Point($location->sysx, $location->sysy);
        }

        $this->surfaceCoords = null;
        if($location->surfx && $location->surfy) {
            $this->surfaceCoords = new Point($location->surfx, $location->surfy);
        }

        $this->groundCoords = null;
        if($location->groundx && $location->groundy) {
            $this->groundCoords = new Point($location->groundx, $location->groundy);
        }
    }

    /**
     * Return a vendor's location
     * @param $vendor_id
     * @return Location|null
     */
    static function getVendorLocation($vendor_id): ?Location {
        if($vendor_id) {
            $db = new Database();
            $db->connect();
            $vendor_id = mysqli_real_escape_string($db->connection, $vendor_id);
            $location = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors_locations WHERE vendor_id = '$vendor_id'"));
            if($location) {
                return new Location($location);
            }
        }
        return null;
    }

    /**
     * Return a vendor's location
     * @param $container_uid
     * @return Location|null
     */
    static function getMallLocation($container_uid): ?Location {
        if($container_uid) {
            $db = new Database();
            $db->connect();
            $container_uid = mysqli_real_escape_string($db->connection, $container_uid);
            $location = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors_locations WHERE container_uid = '$container_uid'"));
            if($location) {
                return new Location($location);
            }
        }
        return null;
    }

    static function deleteAll() {
        $db = new Database();
        $db->connect();
        mysqli_query($db->connection, "TRUNCATE TABLE vendors_locations");
    }

    /**
     * add or update vendor location from api
     * @param int $vendor_id
     * @param $container
     * @param $container_uid
     * @param $sector
     * @param $system
     * @param $planet
     * @param $city
     * @param $city_uid
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
    static function parseLocation(
        int $vendor_id,
        $container,
        $container_uid,
        $sector = '',
        $system = '',
        $planet = '',
        $city = '',
        $city_uid = '',
        $galx = '',
        $galy = '',
        $sysx = '',
        $sysy = '',
        $surfx = '',
        $surfy = '',
        $groundx = '',
        $groundy = ''
    ) {
        $db = new Database();
        $db->connect();

        $vendor_id = mysqli_real_escape_string($db->connection, $vendor_id);
        $container = mysqli_real_escape_string($db->connection, $container);
        $container_uid = mysqli_real_escape_string($db->connection, $container_uid);
        $sector = mysqli_real_escape_string($db->connection, $sector);
        $system = mysqli_real_escape_string($db->connection, $system);
        $planet = mysqli_real_escape_string($db->connection, $planet);
        $city = mysqli_real_escape_string($db->connection, $city);
        $city_uid = mysqli_real_escape_string($db->connection, $city_uid);
        $galx = mysqli_real_escape_string($db->connection, $galx);
        $galy = mysqli_real_escape_string($db->connection, $galy);
        $sysx = mysqli_real_escape_string($db->connection, $sysx);
        $sysy = mysqli_real_escape_string($db->connection, $sysy);
        $surfx = mysqli_real_escape_string($db->connection, $surfx);
        $surfy = mysqli_real_escape_string($db->connection, $surfy);
        $groundx = mysqli_real_escape_string($db->connection, $groundx);
        $groundy = mysqli_real_escape_string($db->connection, $groundy);

        mysqli_query($db->connection, "INSERT INTO vendors_locations (vendor_id, container, container_uid, sector, `system`, planet, city, city_uid, galx, galy, sysx, sysy, surfx, surfy, groundx, groundy) 
        VALUES ('$vendor_id', '$container', '$container_uid', '$sector', '$system', '$planet', '$city', '$city_uid', 
                ".(($galx) ? $galx : 'NULL').", 
                ".(($galy) ? $galy : 'NULL').", 
                ".(($sysx) ? $sysx : 'NULL').", 
                ".(($sysy) ? $sysy : 'NULL').",  
                ".(($surfx) ? $surfx : 'NULL').", 
                ".(($surfy) ? $surfy : 'NULL').", 
                ".(($groundx) ? $groundx : 'NULL').", 
                ".(($groundy) ? $groundy : 'NULL')."
        )");
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