<?php
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err) {
        error_log("FATAL ERROR: ".print_r($err, true).PHP_EOL, 3, LOGFILE);
    }
});

include(__DIR__.'/../autoload.php');

error_log("[" . date('Y-m-d H:i:s') . "] Starting scheduled vendor update" . PHP_EOL, 3, LOGFILE);
try {
    $ws = new WebService();
    error_log("[" . date('Y-m-d H:i:s') . "] WebService initialized, calling updateVendorList()" . PHP_EOL, 3, LOGFILE);

    $ws->updateVendorList();
    error_log("[" . date('Y-m-d H:i:s') . "] Scheduled vendor update completed successfully" . PHP_EOL, 3, LOGFILE);

} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    error_log("[" . date('Y-m-d H:i:s') . "] ERROR in scheduled update: " . $errorMsg . PHP_EOL, 3, LOGFILE);
    error_log("[" . date('Y-m-d H:i:s') . "] Stack trace: " . $e->getTraceAsString() . PHP_EOL, 3, LOGFILE);
}
error_log("[" . date('Y-m-d H:i:s') . "] Scheduled vendor update finished" . PHP_EOL, 3, LOGFILE);