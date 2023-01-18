<?php

class WebService {
    const VENDOR_API = 'http://www.swcombine.com/ws/v2.0/market/vendors.json';

    /**
     * return data from swc api endpoint
     * @param string $url
     */
    function fetch_api(string $url) {
        $result = json_decode(file_get_contents($url), true);
        return $result;
    }

    function updateVendorList()
    {
        Vendor::deleteAll();
        $resp = $this->fetch_api(self::VENDOR_API);
        $resp = $resp['swcapi']['vendors'];
        $total = $resp['attributes']['total'];
        $count = $resp['attributes']['count'];
        $start = $resp['attributes']['start'];

        while(($start - 1) + $count <= $total) {
            foreach ($resp['vendor'] as $vendor) {
                $vendor_id = $vendor['attributes']['id'];
                $vendor_resp = $this->fetch_api(self::VENDOR_API . '/' . $vendor_id .'.json');
                $vendor_data = $vendor_resp['swcapi']['vendor'];

                if (is_string($vendor_data['description'])) {
                    Vendor::parseVendor($vendor_id, $vendor_data['name'], $vendor_data['description'], $vendor_data['owner']['value']);
                } else {
                    Vendor::parseVendor($vendor_id, $vendor_data['name'], '', $vendor_data['owner']['value']);
                }

                Location::deleteAll();
                $location = $vendor_data['location'];
                $coords = $location['coordinates'];
                (isset($location['container']['value'])) ? $container = $location['container']['value'] : $container = '';
                (isset($location['container']['attributes']['uid'])) ? $container_uid = $location['container']['attributes']['uid'] : $container_uid = '';
                (isset($location['sector']['value'])) ? $sector = $location['sector']['value'] : $sector = '';
                (isset($location['system']['value'])) ? $system = $location['system']['value'] : $system = '';
                (isset($location['planet']['value'])) ? $planet = $location['planet']['value'] : $planet = '';
                (isset($location['city']['value'])) ? $city = $location['city']['value'] : $city = '';
                (isset($location['city']['attributes']['uid'])) ? $city_uid = $location['city']['attributes']['uid'] : $city_uid = '';
                Location::parseLocation(
                    $vendor_id, $container, $container_uid, $sector, $system, $planet, $city,
                    $city_uid,
                    $coords['galaxy']['attributes']['x'],
                    $coords['galaxy']['attributes']['y'],
                    $coords['system']['attributes']['x'],
                    $coords['system']['attributes']['y'],
                    $coords['surface']['attributes']['x'],
                    $coords['surface']['attributes']['y'],
                    $coords['ground']['attributes']['x'],
                    $coords['ground']['attributes']['y']
                );

                Ware::deleteVendorWares($vendor_id);
                foreach ($vendor_data['wares'] as $ware) {
                    if ($ware) {
                        if ($vendor_id && isset($ware['type']) && $ware['quantity'] && $ware['price'] && $ware['currency'] && $ware['images']['small'] && $ware['images']['large']) {
                            $quantity = str_replace(',', '', $ware['quantity']);
                            Ware::parseWare($vendor_id, $ware['type'], $quantity, $ware['price'], $ware['currency'], $ware['images']['small'], $ware['images']['large']);
                        }
                    }
                }
            }

            $resp = $this->fetch_api(self::VENDOR_API.'.json?start_index='.($start + 50));
            $resp = $resp['swcapi']['vendors'];
            $total = $resp['attributes']['total'];
            $count = $resp['attributes']['count'];
            $start = $resp['attributes']['start'];
        }
    }


}