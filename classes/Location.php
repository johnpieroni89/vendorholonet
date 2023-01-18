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
        if($location->galx) {
            $this->galaxyCoords = new Point($location->galx, $location->galy);
        }

        $this->systemCoords = null;
        if($location->sysx) {
            $this->systemCoords = new Point($location->sysx, $location->sysy);
        }

        $this->surfaceCoords = null;
        if($location->surfx) {
            $this->surfaceCoords = new Point($location->surfx, $location->surfy);
        }

        $this->groundCoords = null;
        if($location->groundx) {
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
            $location = mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM vendors_locations WHERE id = '$vendor_id'"));
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

        $container = mysqli_real_escape_string($db->connection, $container);
        $sector = mysqli_real_escape_string($db->connection, $sector);
        $system = mysqli_real_escape_string($db->connection, $system);
        $planet = mysqli_real_escape_string($db->connection, $planet);
        $city = mysqli_real_escape_string($db->connection, $city);

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