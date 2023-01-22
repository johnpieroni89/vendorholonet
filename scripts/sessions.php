<?php

//print_r(scandir(session_save_path()));

$arr = scandir(session_save_path());
var_dump(file_get_contents('/var/lib/php/sessions/'.$arr[58]));

//phpinfo();

?>