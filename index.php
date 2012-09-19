<?php

require 'src/facebook.php';
require 'inc/config.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
	'appId'  => $appId,
	'secret' => $secret,
));

// Get User ID
$user = $facebook->getUser();

$debug = false;

// GRUPO SELECCIONADO
$grupo = $_GET['grupo'];

if ($user) {
	//$logoutUrl = $facebook->getLogoutUrl(array( 'next' => ( 'http://'.$_SERVER['SERVER_NAME'].'/logout.php') ));
	$logoutUrl = $facebook->getLogoutUrl();
} else {
	$params = array('scope' => 'user_groups,friends_groups');
	$loginUrl = $facebook->getLoginUrl($params);
}

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
		// $user_groups = $facebook->api( '/219652654807851/feed/?limit=320');
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
	
	if(!isset($grupo)) { 
		$user_groups = $facebook->api('/me/groups'); 
	} else {
		$group_title = $facebook->api('/'.$grupo); 
		$group_title = $group_title['name']; 
	}
}
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>Archivador de grupos de Facebook</title>
	<link rel="stylesheet" type="text/css" href="style.css" media="all" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
</head>
<body>
	<h1>Archivador de grupos de Facebook</h1>
	<div id="main">
<?php 
	if (!$user): ?>
		<div>Por favor <a href="<?php echo $loginUrl; ?>">conecte con Facebook</a></div>
<?php 	else: ?>
		<h2>¡Hola <?php echo $user_profile['first_name'] ?>!</h2>
<?php
		if(!isset($grupo)) {
			if(!empty($user_groups['data'])) {
				foreach($user_groups['data'] as $item) {
					$grupos .= '<option value="'.$item['id'].'">'.$item['name'].'</option>'."\n";
				}
?>			<h3>Por favor elija el grupo del cual desee descargar el contenido*.</h3>
			<form action="" method="get">
				<select name="grupo">
<?php			 echo $grupos ?>
				</select>
				<input type="submit" value="Seleccionar" />
			</form>
<?php 		} else { ?>
			<div><em>¡No perteneces a ningún grupo! :(</em></div>
<?php 		}
		} else {
?>
		<h3>Descargando contenido de "<?php echo $group_title ?>"</h3>
		<div><em>Descargar <b><a href="create-csv.php?grupo=<?php echo $grupo ?>">Valores separados por coma, .csv</a></b></em></div>
	<?php	/* <div><em>Descargar <b><a href="create-xls.php?grupo=<?php echo $grupo  ?>">Microsoft Excel, .xls</a></b></em></div> */ ?>
		<div><em>Descargar <b><a href="create-xls2.php?grupo=<?php echo $grupo ?>">Microsoft Excel, .xls</a></b></em></div>
		<div><em>Descargar <b><a href="create-json.php?grupo=<?php echo $grupo ?>">JSON</a></b></em></div>
<?php
		}
?>
		<a href="<?php echo $logoutUrl; ?>" class="logout">Cerrar sesión</a>
<?php 
	if ($debug): ?>
		<h3>Your User Object (/me)</h3>
		<pre><?php print_r($user_profile); ?></pre>
		<h3>PHP Session</h3>
		<pre><?php print_r($_SESSION); ?></pre>
<?php endif; ?>	
<?php 
	endif; ?>
		<div class="disclaimer">
			<small>El archivo devuelto no contendrá información sobre archivos cargados en el grupo. <br />En tanto esta aplicación está en desarrollo, el número de ítems máximo a descargar de un grupo está limitado a 1500.</small>
		</div>
	</div>
</body>
</html>