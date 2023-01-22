<?php

//print_r(scandir(session_save_path()));

$arr = scandir(session_save_path());
var_dump(count($arr));

?>