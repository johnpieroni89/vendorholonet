<?php

print_r(scandir(session_save_path()));

$arr = scandir(session_save_path());

phpinfo();

?>