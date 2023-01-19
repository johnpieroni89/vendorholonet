<?php

class Mall
{
    /**
     * Get all malls if they meet certain thresholds
     * @param int $vendorDensity
     * @param int $uniqueOwners
     * @return array
     */
    static function getMalls(int $vendorDensity = 5, int $uniqueOwners = 5): ?array {
        $malls = Mall::findMallContainers($vendorDensity);
        $malls = Mall::filterMallsByUniqueOwners($malls, $uniqueOwners);
        usort($malls, function($a, $b) { return $a->owners < $b->owners;});
        $malls = Mall::getMallLocations($malls);
        return $malls;
    }

    static function getMallLocations(array $containers): ?array {
        $db = new Database();
        $db->connect();
        $containerArr = array();
        foreach ($containers as $container) {
            $container->location = Location::getMallLocation($container->container_uid);
            $containerArr[] = $container;
        }
        return $containerArr;
    }

    /**
     * Find all locations with at least # of vendors
     * @param int $vendor_density
     * @return array
     */
    static function findMallContainers(int $vendor_density): ?array {
        $db = new Database();
        $db->connect();
        $containers = mysqli_query($db->connection, "SELECT COUNT(`vendor_id`) as `vendors`, `container_uid`, `container` FROM `vendors_locations` GROUP BY `container_uid`, `container` HAVING `vendors` >= '$vendor_density' ORDER BY `vendors` DESC");
        $containersArr = array();
        while($obj = mysqli_fetch_object($containers)) {
            if($obj->container_uid[0] == 5 || $obj->container_uid[0] == 7 || $obj->container_uid[0] == 4) {
                $containersArr[] = $obj;
            }
        }
        return $containersArr;
    }

    /**
     * Filter malls out if they don't meet target number of unique vendor owners
     * @param array $containers
     * @param int $uniqueTarget
     * @return array
     */
    static function filterMallsByUniqueOwners(array $containers, int $uniqueTarget = 5): ?array {
        $db = new Database();
        $db->connect();

        $remainingContainers = array();
        foreach ($containers as $container) {
            $uniqueCount = mysqli_num_rows(mysqli_query($db->connection, "SELECT DISTINCT `vendors`.`owner` FROM `vendors` LEFT JOIN `vendors_locations` ON `vendors`.`id` = `vendors_locations`.`vendor_id` WHERE `vendors_locations`.`container_uid` = '".$container->container_uid."'"));
            if($uniqueCount >= $uniqueTarget) {
                $container->owners = $uniqueCount;
                $remainingContainers[] = $container;
            }
        }
        return $remainingContainers;
    }

    static function sortMalls(array $containers) {
        usort($containers, Mall::sortByOwners());
    }

    static function sortByOwners($a, $b): int {
        if($a->owners==$b->owners) return 0;
        return ($a->owners<$b->owners)?-1:1;
    }

    static function printMalls(int $vendorDensity = 5, int $uniqueOwners = 5): void {
        $malls = Mall::getMalls($vendorDensity, $uniqueOwners);

        foreach ($malls as $mall) {
            $distance = '(In Hyperspace)';
            if($_SESSION['location']) {
                $distance = max(abs($mall->location->galaxyCoords->x - $_SESSION['location']->x), abs($mall->location->galaxyCoords->y - $_SESSION['location']->y));
            }

            echo '
                <div class="col-sm-12 col-md-6 col-lg-4 card p-2 m-0">
                    <div class="card-header bg-light w-100" style="text-align: center;"><h4>'.$mall->container.'</h4></div>
                    <div class="card-body d-grid w-100" style="text-align: center;">
                        <table class="table table-striped table-sm table-bordered">
                            <tr><td>Total Vendors:</td><td style="text-align: right; padding-right: 10px;">'.number_format($mall->vendors).'</td></tr>
                            <tr><td>Unique Owners:</td><td style="text-align: right; padding-right: 10px;">'.number_format($mall->owners).'</td></tr>
                            <tr><td>Distance:</td><td style="text-align: right; padding-right: 10px;">'.$distance.'</td></tr>
                        </table>
                    </div>
                    <div class="card-footer d-grid w-100" style="text-align: center;">
                        (Travel Button soon!)
                    </div>
                </div>
            ';
        }
    }
}