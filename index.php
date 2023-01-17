<html>
<?php

// always logout if visiting this public-facing page
session_start();
session_destroy();

include('autoload.php');
UserInterface::printHead();
if(isset($_SESSION['handle'])) {
    header("Location: app/index.php");
}

?>
<body>
    <!-- Login Container -->
    <div>
        <!-- Login Header -->
        <center><img class="img-responsive" style="max-height:200px; margin-bottom:50px;" src="app/assets/img/logo-large.png" alt="Vendor Holonet"></center>

        <!-- Login Block -->
        <div class="block">
            <!-- Login Form -->
            <div style="text-align: center;">
                <a href="oauth/index.php"><button type="button" class="btn btn-lg btn-secondary"><i class="fas fa-sign-in-alt"></i> Sign In</button></a>
            </div>
        </div>

    </div>
</body>

</html>