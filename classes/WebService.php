<?php

class WebService {
    const VENDOR_API = 'http://www.swcombine.com/ws/v2.0/market/vendors';

    /**
     * return data from swc api endpoint
     * @param string $url
     * @return false|SimpleXMLElement
     */
    function fetch_api(string $url) {
        $result = simplexml_load_string(file_get_contents($url));
        $result = json_encode($result);
        return json_decode($result);
    }

    function updateVendorList()
    {
        $resp = $this->fetch_api(self::VENDOR_API);
        $total = $resp->vendors->{'@attributes'}->{'total'};
        $count = $resp->vendors->{'@attributes'}->{'count'};
        $start = $resp->vendors->{'@attributes'}->{'start'};

        $count_vendors = 0;
        while(($start - 1) + $count <= $total) {
            //var_dump($count.' : '.$start.' : '.$total);
            foreach ($resp->vendors->vendor as $vendor) {
                $vendor_id = $vendor->{'@attributes'}->{'id'};
                $vendor_resp = $this->fetch_api(self::VENDOR_API . '/' . $vendor_id);
                $vendor_data = $vendor_resp->vendor;

                if (is_string($vendor_data->{'description'})) {
                    Vendor::parseVendor($vendor_data->{'id'}, $vendor_data->{'name'}, $vendor_data->{'description'}, $vendor_data->{'owner'});
                } else {
                    Vendor::parseVendor($vendor_data->{'id'}, $vendor_data->{'name'}, '', $vendor_data->{'owner'});
                }

                Ware::deleteVendorWares($vendor_id);
                foreach ($vendor_data->wares as $ware) {
                    if ($ware) {
                        if ($vendor_id && isset($ware->{'type'}) && $ware->{'quantity'} && $ware->{'price'} && $ware->{'currency'} && $ware->{'images'}->{'small'} && $ware->{'images'}->{'large'}) {
                            $quantity = str_replace(',', '', $ware->{'quantity'});
                            Ware::parseWare($vendor_id, $ware->{'type'}, $quantity, $ware->{'price'}, $ware->{'currency'}, $ware->{'images'}->{'small'}, $ware->{'images'}->{'large'});
                        }
                    }
                }
            }

            $resp = $this->fetch_api(self::VENDOR_API.'?start_index='.($start + 50));
            $total = $resp->vendors->{'@attributes'}->{'total'};
            $count = $resp->vendors->{'@attributes'}->{'count'};
            $start = $resp->vendors->{'@attributes'}->{'start'};
            $count_vendors++;
        }
        echo $count_vendors;
    }


}