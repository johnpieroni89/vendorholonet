<?php

class Mall
{
    /**
     * Get all malls if they meet certain thresholds
     * @param int $vendorDensity
     * @param int $uniqueOwners
     * @return void
     */
    static function getMalls(int $vendorDensity = 5, int $uniqueOwners = 5) {
        $malls = Mall::findMallContainers($vendorDensity);
        $malls = Mall::filterMallsByUniqueOwners($malls, $uniqueOwners);
        var_dump($malls);
        return $malls;
    }

    /**
     * Find all locations
     * @param int $vendor_density
     * @return array
     */
    static function findMallContainers(int $vendor_density) {
        $db = new Database();
        $db->connect();
        $containers = mysqli_query($db->connection, "SELECT COUNT(`vendor_id`) as `vendors`, `container_uid`, `container` FROM `vendors_locations` GROUP BY `container_uid`, `container` HAVING `vendors` >= '$vendor_density' ORDER BY `vendors` DESC");
        $containersArr = array();
        while($obj = mysqli_fetch_object($containers)) {
            $containersArr[] = $obj;
        }
        return $containersArr;
    }

    /**
     * Filter malls out if they don't meet target number of unique vendor owners
     * @param array $containers
     * @param int $uniqueTarget
     * @return array
     */
    static function filterMallsByUniqueOwners(array $containers, int $uniqueTarget = 5) {
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
}