<?php
/* initalize cookie settings */
$l_maxlifetime = 2 * 24 * 60 * 60; /* life duration of cookie: 2 days */
$s_host = ( (strpos($_SERVER['HTTP_HOST'], ':') === false) ? $_SERVER['HTTP_HOST'] : explode(':', $_SERVER['HTTP_HOST'])[0] ); /* get domain from server http host */
$b_secure = true; /* if you only want to receive the cookie over HTTPS */
$b_httponly = true; /* prevent JavaScript access to session cookie */

/**
 * browsers will not enforce SameSite rules at all. Even if browsers
 * start to treat cookies without this flag present as Lax (which is
 * the case for Chrome 80 and later), setting None will disable this protection.
 */
//$s_samesite = 'None';

/**
 * that cookie will not be passed for any cross-domain requests
 * unless it's a regular link that navigates user to the target site.
 * Other requests methods (such as POST and PUT) and XHR requests
 * will not contain this cookie.
 */
//$s_samesite = 'Lax';

/**
 * that cookie will not be sent for any cross-domain requests whatsoever.
 * Even if the user simply navigates to the target site with a regular link,
 * the cookie will not be sent. This might lead to some confusing or downright
 * impractical user experiences, so be careful if you use Strict cookies.
 */
$s_samesite = 'Strict';

$foo = session_name('forestPHPSession'); /* unqiue name per web application */

if (PHP_VERSION_ID < 70300) {
	session_set_cookie_params($l_maxlifetime, '/; samesite=' . $s_samesite, $s_host, $b_secure, $b_httponly);
} else {
	session_set_cookie_params([
		'lifetime' => $l_maxlifetime,
		'path' => '/',
		'domain' => $s_host,
		'secure' => $b_secure,
		'httponly' => $b_httponly,
		'samesite' => $s_samesite
	]);
}

session_start();
session_regenerate_id(false);
unset($_SESSION);
session_destroy();
?>
Logout erfolgreich<br />
<a href="./">Weiter...</a>