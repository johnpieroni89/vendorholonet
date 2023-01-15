<?php

class AuthorizationResult
{
	const Error = 0;
	const Authorized = 1;
	const Denied = 2;

	function get_description($result) {
		$description = null;

		switch ($result) {
			case 1:
				$description = 'Authorized';
				break;

			case 2:
				$description = 'The end-user or authorization server denied the request';
				break;

			default:
				$description = 'Unspecified Error';
				break;
		}

		return $description;
	}
}