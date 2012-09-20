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
		<form action="create.php" method="post">
			<input type="radio" name="type" value="xls" />Microsoft Excel, .xls<br />
			<input type="radio" name="type" value="csv" />Valores separados por coma, .csv<br />
			<input type="radio" name="type" value="json" />JSON <br />
			
			<label for="limit">Límite</label>
			<input type="text" name="limit" /><br />
			<label for="offset">Márgen</label>
			<input type="text" name="offset" />
			
			<input type="hidden" name="group" value="<?php echo $grupo ?>" />
			
			<input type="submit" value="Seleccionar" />
		</form>
		
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
				<li>Si el grupo tiene muchos mensajes, Facebook puede devolver "Error 1: An unknown error occurred". 
					<ul>
						<li>Posible solución: En algunos casos, desloguearse de FB y volver a probar lo soluciona.</li>
					</ul>
				</li>
				<li>El funcionamiento, por asuntos de validación de credenciales de Facebook, a veces es errático. Se puede intentar varias veces hasta que devuelva un archivo.</li>
				<li>Para grupos muy grandes en algunos casos es mejor seguir el siguiente procedimiento:
					<ol>
						<li>Descargar con un límite de por ejemplo 500.</li>
						<li>Luego, descargar con el mismo límite pero con un márgen de 500, para descargar a partir del elemento 500.</li>
						<li>Repetir cuantas veces se desee.</li>
					</ol></li>
				
			
			</ul>
		</div>
		<small><a href="<?php echo $logoutUrl; ?>" class="logout">Cerrar sesión</a></small>
	</div>
</body>
</html>