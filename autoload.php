<?php 

session_start();
include('config.php');
include('classes/Database.php');

include('classes/User.php');
if(isset($_SESSION['handle'])) {
    $user = User::getUser($_SESSION['handle']);
} else {
    $user = null;
}

include('classes/Vendor.php');
include('classes/Location.php');
include('classes/Ware.php');
include('classes/WebService.php');

include('oauth/AuthorizationResult.php');
include('oauth/ContentTypes.php');
include('oauth/GrantTypes.php');
include('oauth/OAuthToken.php');
include('oauth/RequestMethods.php');
include('oauth/SWC.php');
include('oauth/SWCombineWSException.php');

?>