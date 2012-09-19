<?php

require 'src/facebook.php';
require 'inc/config.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
	'appId'  => $appID,
	'secret' => $secret,
));

setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/', $cookie_dominio);
session_destroy();
header('Location: '.$logout_callback);

?>