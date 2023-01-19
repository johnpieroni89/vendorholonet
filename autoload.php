<?php 

session_start();
include('config.php');
include('classes/Database.php');

include('classes/User.php');
include('classes/Vendor.php');
include('classes/Location.php');
include('classes/Mall.php');
include('classes/Ware.php');
include('classes/UserInterface.php');
include('classes/WebService.php');

if(isset($_SESSION['handle'])) {
    $user = User::getUser($_SESSION['handle']);

    // refresh location data
    $_SESSION['location'] = User::getLocation($_SESSION['uid'], $_SESSION['access_token']);
    $_SESSION['location_str'] = 'Location: Hyperspace';
    if($_SESSION['location']) {
        $_SESSION['location_str'] = 'Location: ('.$_SESSION['location']->x.', '.$_SESSION['location']->y.')';
    }

} else {
    $user = null;
}

include('oauth/AuthorizationResult.php');
include('oauth/ContentTypes.php');
include('oauth/GrantTypes.php');
include('oauth/OAuthToken.php');
include('oauth/RequestMethods.php');
include('oauth/SWC.php');
include('oauth/SWCombineWSException.php');

?>