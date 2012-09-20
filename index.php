<?php

require 'src/facebook.php';
require 'inc/config.php';

$facebook = new Facebook(array(
	'appId'  => $appId,
	'secret' => $secret,
));

$user = $facebook->getUser();
$debug = false;

// GRUPO SELECCIONADO
$grupo = $_GET['grupo'];

if ($user) {
	$logoutUrl = $facebook->getLogoutUrl();
} else {
	$params = array('scope' => 'user_groups,friends_groups');
	$loginUrl = $facebook->getLoginUrl($params);
}

if ($user) {
	try {
		// Proceed knowing you have a logged in user who's authenticated.
		$user_profile = $facebook->api('/me');
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
	
	// Recuperamos listado de grupos, o título de grupo
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
		<ul>
			<li><a href="create.php?tipo=xls&grupo=<?php echo $grupo ?>">Descargar Microsoft Excel, .xls</a></li>
			<li><a href="create.php?tipo=csv&grupo=<?php echo $grupo ?>">Descargar Valores separados por coma, .csv</a></li>
			<li><a href="create.php?tipo=json&grupo=<?php echo $grupo ?>">Descargar JSON</a></li>
		</ul>
<?php
		}
?>
		
<?php 
	if ($debug): ?>
		<h4>Your User Object (/me)</h4>
		<pre><?php print_r($user_profile); ?></pre>
		<h4>PHP Session</h4>
		<pre><?php print_r($_SESSION); ?></pre>
<?php endif; ?>	
<?php 
	endif; ?>
		<div class="disclaimer">
			<small>El archivo devuelto no contendrá información sobre archivos cargados en el grupo. <br />
			En tanto esta aplicación está en desarrollo, el número de ítems máximo a descargar de un grupo está limitado a 750. <br />
			No se guarda ningún tipo de información personal en nuestros servidores. La información es cargada y devuelta al usuario directamente.</small>
			<h3>Problemas conocidos</h3>
			<ul>
				<li>Si el grupo tiene muchos mensajes, Facebook puede devolver "Error 1: An unknown error occurred". <ul><li>Posible solución: En algunos casos, desloguearse de FB y volver a probar lo soluciona.</li></ul></li>
				
			
			</ul>
		</div>
		<small><a href="<?php echo $logoutUrl; ?>" class="logout">Cerrar sesión</a></small>
	</div>
</body>
</html>