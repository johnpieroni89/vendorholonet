<?php

//print_r(scandir(session_save_path()));

$arr = scandir(session_save_path());

foreach($arr as $data){
    $session = file_get_contents('/var/lib/php/sessions/'.$data);
    if($data) {
        var_dump($data);
    }
}

?>