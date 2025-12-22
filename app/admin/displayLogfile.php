<?php

include('../../autoload.php');

if(!isset($_SESSION['handle'])) {
    header("Location: ../../index.php");
}
if(!$_SESSION['handle'] == CONFIG_GLOBAL_ADMIN) {
    header("Location: ../index.php");
}

// Read log file
$logContent = '';
$lines = [];
if(file_exists(LOGFILE)) {
    $logContent = file_get_contents(LOGFILE);
    $lines = array_reverse(file(LOGFILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}

// Handle clear log action
if(isset($_POST['clear_log'])) {
    file_put_contents(LOGFILE, '');
    header("Location: view_logs.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<?php UserInterface::printHead(); ?>
<style>
    .log-container {
        background: #1e1e1e;
        color: #d4d4d4;
        padding: 20px;
        border-radius: 5px;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        max-height: 600px;
        overflow-y: auto;
    }
    .log-line {
        padding: 5px;
        border-bottom: 1px solid #333;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    .log-line:hover {
        background: #2d2d2d;
    }
    .log-error {
        color: #f48771;
        font-weight: bold;
    }
    .log-success {
        color: #89d185;
    }
    .log-timestamp {
        color: #569cd6;
    }
</style>
<body class="sb-nav-fixed">
<?php UserInterface::printNav(); ?>
<div id="layoutSidenav">
    <?php UserInterface::printSideNav(); ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Update Logs</h1>
                <hr/>
                
                <div class="mb-3">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Admin Panel
                    </a>
                    <button onclick="location.reload()" class="btn btn-info">
                        <i class="fa-solid fa-refresh"></i> Refresh
                    </button>
                    <a href="?download=1" class="btn btn-secondary">
                        <i class="fa-solid fa-download"></i> Download Log
                    </a>
                    <form method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to clear the log?');">
                        <button type="submit" name="clear_log" class="btn btn-danger">
                            <i class="fa-solid fa-trash"></i> Clear Log
                        </button>
                    </form>
                </div>

                <?php if(empty($lines)): ?>
                    <div class="alert alert-info">No log entries found.</div>
                <?php else: ?>
                    <div class="log-container">
                        <?php foreach($lines as $line): 
                            $class = '';
                            if(stripos($line, 'error') !== false || stripos($line, 'fail') !== false) {
                                $class = 'log-error';
                            } elseif(stripos($line, 'success') !== false || stripos($line, 'complete') !== false || stripos($line, 'finish') !== false) {
                                $class = 'log-success';
                            }
                            
                            // Highlight timestamps
                            $line = preg_replace('/(\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/', '<span class="log-timestamp">$1</span>', htmlspecialchars($line));
                        ?>
                            <div class="log-line <?php echo $class; ?>"><?php echo $line; ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-2 text-muted">
                        Showing <?php echo count($lines); ?> log entries (most recent first)
                    </div>
                <?php endif; ?>
            </div>
        </main>
        <?php UserInterface::printFooter(); ?>
    </div>
</div>
<?php UserInterface::printScripts(); ?>
</body>
</html>

<?php
// Handle download
if(isset($_GET['download']) && file_exists(LOGFILE)) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="vendor_update_log_' . date('Y-m-d_His') . '.txt"');
    readfile(LOGFILE);
    exit;
}
?>