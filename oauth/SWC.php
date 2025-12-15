<?php

define('OAUTH_ENDPOINT_AUTH', 'https://www.swcombine.com/ws/oauth2/auth/');
define('OAUTH_ENDPOINT_TOKEN', 'https://www.swcombine.com/ws/oauth2/token/');

class SWC {

    /**
     * @var string Id of client used in requests.
     */
    public $client_id;

    /**
     * @var string Client secret that is used in requests.
     */
    private $client_secret;

    /**
     * @var string Uri that oauth authenticator will redirect to.
     */
    public $redirect_uri;

    /**
     * @var string Indicates if your application needs to access a Resource when the user is not present at the browser.
     */
    private $access_type;

    /**
     * @var OAuthToken OAuth Token.
     */
    public $token;

    function __construct($isOffline = true) {
        $this->client_id = SWC_API_CLIENT_ID;
        $this->client_secret = SWC_API_CLIENT_SECRET;
        $this->redirect_uri = SWC_API_REDIRECT_URI;
        $this->access_type = $isOffline ? "offline" : "online";
    }
        
    public function set_user($user) {
        $this->user = $user;
    }

    public function get_token() {
        return $this->token;
    }
        
    public function set_token(OAuthToken $token) {
        return $this->token = $token;
    }

    /**
     * Attempts authorize process by redirecting the user in the browser.
     * @param array $scopes List of scopes required by the app for user to authorize.
     * @param string $state Any state information to be pass back to the app on completion of authorisation.
     */
    public function AttemptAuthorize(array $scopes, $state = null, $renew = true) {
        $url = OAUTH_ENDPOINT_AUTH.'?response_type=code'.
                        '&client_id='.urlencode($this->client_id).
                        '&scope='.urlencode(implode(' ', $scopes)).
                        '&redirect_uri='.urlencode($this->redirect_uri).
                        '&state='.urlencode($state ?? '').
                        '&access_type='.urlencode($this->access_type);
        if($renew) {
            $url .= '&renew_previously_granted=yes';
        }
            // Clear any previous output to avoid header issues
        if (ob_get_length()) {
            ob_end_clean();
        }
        //header('Accept: '.ContentTypes::JSON);
        header('location: '.$url);
    }

    /**
     * Parses the query tokens returned during authorisation process
     *
     * @param array $queryTokens Query tokens to parse for authorisation.
     */
    public function ParseUrl(array $queryTokens) {
        $result = AuthorizationResult::Error;

        if (!isset($queryTokens['code'])) {
            // app was not authorized for some reason
            if ($queryTokens['error'] == 'access_denied') {
                $result = AuthorizationResult::Denied;
            } else {
                // random error so return description value
                $result = $queryTokens['description'];
            }
        } else {
            // exchange code for a token
            $this->token = $this->GetToken($queryTokens['code']);
            $result = AuthorizationResult::Authorized;
        }
        return $result;
    }

    /**
     * Exchanges access_code for tokens.
     *
     * @param string $code Access code to exchange for tokens.
     * @return OAuthToken
     * @throws SWCombineWSException
     */
    private function GetToken($code, $method=RequestMethods::Post) {
        $values =   array
                        (
                            "code" => $code
                            ,"client_id" => $this->client_id
                            ,"client_secret" => $this->client_secret
                            ,"redirect_uri" => $this->redirect_uri
                            ,"grant_type" => GrantTypes::AuthorizationCode
                            ,"access_type" => $this->access_type
                        );

        $response = self::MakeRequest(OAUTH_ENDPOINT_TOKEN, $method, $values);

        if (isset($response->error)) {
            throw new SWCombineWSException('Failed to get token. Reason: '.$response->error, $response->error);
        }

        return new OAuthToken($response->expires_in, $response->access_token, isset($response->refresh_token) ? $response->refresh_token : null);
    }

    public function RefreshToken(OAuthToken $token, $method=RequestMethods::Post) {
        $values = array
                        (
                        "refresh_token" => $token->get_refresh_token()
                        ,"client_id" => $this->client_id
                        ,"client_secret" => $this->client_secret
                        ,"grant_type" => GrantTypes::RefreshToken
                        );

        $response = self::MakeRequest(OAUTH_ENDPOINT_TOKEN, $method, $values);

        if (isset($response->error)) {
            throw new SWCombineWSException('Failed to get token. Reason: '.$response->error, $response->error);
        }

      return new OAuthToken($response->expires_in, $response->access_token, isset($response->refresh_token) ? $response->refresh_token : $token->get_refresh_token());
    }

    public function CallApi($uri, array $values = null, $method=RequestMethods::Get) {
        if (is_null($values)) {
            $values = [];
        }
        $token = $this->get_token();
        if (is_object($token)) {
            $values = array_merge(['access_token' => $token->get_access_token()], $values);
            return self::MakeRequest($uri, $method, $values);
        }
        else {
            return false;
        }
    }

    /**
     * Makes a request to the specified uri.
     *
     * @param string $uri Uri to make the request to.
     * @param int $method Http Method to use for the request. @see HTTP_METH_XXXX
     * @param array $values Any parameters to include in the request, where the Key is the parameter name and Value is the parameter value.
     * If successful returns a instance of TValue
     */
    public static function MakeRequest($uri, $method, array $values) {
        $body = http_build_query($values);
        $headers = array('accept: '.ContentTypes::JSON);
        $headers[] = 'User-Agent: SWC API Client';
        // open connection
        $ch = curl_init();

        if ($method == RequestMethods::Get) {
                // values should be query parameters so update uri
                $uri .= '?'.$body;
                $headers[] = 'Content-type: '.ContentTypes::UTF8;
        } else {
                $headers[] = 'Content-type: '.ContentTypes::FormData;
                $headers[] = 'Content-length: '.strlen($body);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        // set url and headers
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // set the method
        switch ($method) {
        case RequestMethods::Post:
                curl_setopt($ch, CURLOPT_POST, 1);
                break;

        case RequestMethods::Put:
                curl_setopt($ch, CURLOPT_PUT, 1);
                break;

        case RequestMethods::Delete:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        // execute
        $response = curl_exec($ch);
        // close connection
        curl_close($ch);

        return (object)json_decode($response);
    }
}