<?php

class OAuthToken {

	/**
	 * @var int timestamp when access token will expire.
	 */
	private $expires_at;

	/**
	 * @var string Access token used when making a request.
	 */
	private $access_token;

	/**
	 * @var string Refresh token used to get renewed Access token.
	 */
	private $refresh_token;
	
	public function __construct($expiresIn, $accessToken, $refreshToken = null) {
		$this->expires_at = time() + $expiresIn;
		$this->access_token = $accessToken;
		$this->refresh_token = $refreshToken;
	}

	/**
	 * Gets the access token.
	 *
	 * @return string Access token used when making a request.
	 */
	public function get_access_token() {
		return $this->access_token;
	}

	/**
	 * Gets the refresh token.
	 *
	 * @return string Refresh token used to get renewed Access token.
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * Get remaining life time of token.
	 *
	 * @return int Total remaining life of access token in seconds.
	 */
	public function get_expires_in() {
		$expires_at = $this->get_expires_at();
		$now = time();
		return $expires_at - $now;
	}

	/**
	 * Get date and time when token expires.
	 *
	 * @return int timestamp when access token will expire.
	 */
	public function get_expires_at() {
		return $this->expires_at;
	}
}