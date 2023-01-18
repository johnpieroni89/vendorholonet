<?php

class User {

    public $id;
    public $handle;
    public $access_token;
    public $refresh_token;
    public $date_registered;

    /**
     * conduct SWC Web Services login
     * @param string $handle
     * @param string $access_token
     * @param string $refresh_token
     * @return void
     * @throws Exception
     */
    static function oAuthLogin(string $handle, string $access_token, string $refresh_token): void
    {
        $db = new Database();
        $db->connect();

        $handle = mysqli_real_escape_string($db->connection, $handle);
        $access_token = mysqli_real_escape_string($db->connection, $access_token);
        $refresh_token = mysqli_real_escape_string($db->connection, $refresh_token);

        if(User::isAccount($handle)){
            mysqli_query($db->connection, "UPDATE users SET access_token = '$access_token', refresh_token = '$refresh_token' WHERE handle = '$handle'");
        } else {
            mysqli_query($db->connection, "INSERT INTO users (handle, access_token, refresh_token) VALUES ('$handle', '$access_token', '$refresh_token')");
        }

        $user = User::getUser($handle);
        $_SESSION['id'] = $user->id;
        $_SESSION['handle'] = $user->handle;
        $_SESSION['access_token'] = $user->access_token;
        $_SESSION['uid'] = User::getUID($handle);
        $_SESSION['location'] = User::getLocation($_SESSION['uid'], $user->access_token);
        $_SESSION['location_str'] = 'Location: Hyperspace';
        if($_SESSION['location']) {
            $_SESSION['location_str'] = 'Location: ('.$_SESSION['location']->x.', '.$_SESSION['location']->y.')';
        }
        $_SESSION['client_ip'] = $_SERVER['REMOTE_ADDR'];
        $_SESSION['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        header("Location: ../app/index.php");
    }

    /**
     * Get User object from handle
     * @param string $handle
     * @return User
     */
    static function getUser(string $handle): User {
        $db = new Database();
        $db->connect();
        $handle = mysqli_real_escape_string($db->connection, $handle);
        return mysqli_fetch_object(mysqli_query($db->connection, "SELECT * FROM users WHERE handle = '$handle'"), 'User');
    }

    /**
     * Verify if $username exists
     * @param string $handle
     * @return bool
     */
    static function isAccount(string $handle): bool
    {
        $db = new Database();
        $db->connect();
        $handle = mysqli_real_escape_string($db->connection, $handle);

        if (mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM users WHERE handle = '".$handle."'"))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return Character's UID from SWC API
     * @param string $handle
     * @return string
     */
    static function getUID(string $handle): string {
        $handle = str_replace(' ', '%20', $handle);
        $resp = json_decode(file_get_contents('http://www.swcombine.com/ws/v2.0/character/handlecheck/'.$handle.'.json'), true);
        return $resp['swcapi']['character']['uid'];
    }

    /**
     * Get Character object from SWC API
     * @param string $uid
     * @param string $access_token
     * @return mixed
     */
    static function getCharacter(string $uid, string $access_token) {
        $resp = json_decode(file_get_contents('http://www.swcombine.com/ws/v2.0/character/'.$uid.'.json?access_token='.$access_token), true);
        return $resp;
    }

    /**
     * Get Location from Character's UID from SWC API
     * @param string $uid
     * @param string $access_token
     * @return Point|null
     */
    static function getLocation(string $uid, string $access_token): ?Point {
        $resp = json_decode(file_get_contents('https://www.swcombine.com/ws/v2.0/location/characters/'.$uid.'.json?access_token='.$access_token), true);
        if(isset($resp['swcapi']['location']['coordinates']['galaxy']['attributes'])) {
            $location = $resp['swcapi']['location']['coordinates']['galaxy']['attributes'];
            return new Point($location['x'], $location['y']);
        }
        return null;
    }
}