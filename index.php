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
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600' rel='stylesheet' type='text/css'>
</head>
<body>
	<h1>Archivador de grupos de Facebook</h1>
	<div id="main">
		<header>
			<h1><a class="sprites oogster" href="/">Oogster</a></h1>
			<h2 class="sprites tagline">Archivador de grupos de Facebook</h2>
		</header>
		<section>
			<div>
<?php 
	if (!$user): ?>
		<h3>Por favor conecte usando Facebook.</h3>
		<a href="<?php echo $loginUrl; ?>" class="sprites connect">conecte con Facebook</a>
<?php 	else: ?>
<?php
		if(!isset($grupo)) {
			if(!empty($user_groups['data'])) {
				foreach($user_groups['data'] as $item) {
					$grupos .= '<option value="'.$item['id'].'">'.$item['name'].'</option>'."\n";
				}
?>			<h3>¡Hola <?php echo $user_profile['first_name'] ?>!</h3>
			<p>Por favor elija el grupo del cual desee descargar el contenido*.</p>
			<form action="" method="get">
				<label for="grupo">
					<select name="grupo">
<?php				echo $grupos ?>
					</select>
				</label>
				<input type="submit" value="Seleccionar" />
			</form>
<?php 		} else { ?>
			<div><em>¡No perteneces a ningún grupo! :(</em></div>
<?php 		}
		} else {
?>
		<h3>Descargando contenido de <strong>"<?php echo $group_title ?>"</strong></h3>
		<form action="create.php" method="post" id="download">
			<input type="radio" name="type" value="xls" checked="checked" />Microsoft Excel, .xls<br />
			<input type="radio" name="type" value="csv" />Valores separados por coma, .csv<br />
			<input type="radio" name="type" value="json" />JSON <br />
			
			<label for="limit"><abbr title="El Límite indica cuántos elementos queremos descargar de nuestro grupo">Límite</abbr></label>
			<input type="text" name="limit" /><br />
			<label for="offset"><abbr title="El Márgen indica a partir  elementos queremos descargar de nuestro grupo">Márgen</abbr></label>
			<input type="text" name="offset" />
			
			<input type="hidden" name="group" value="<?php echo $grupo ?>" />
			
			<input type="submit" value="Descargar" />
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
					
					<h4>Problemas conocidos</h4>
					<ul>
						<li>Si el grupo tiene muchos mensajes, Facebook puede devolver "Error 1: An unknown error occurred". 
							<ul>
								<li>Posible solución: En algunos casos, desloguearse de FB y volver a probar lo soluciona.</li>
								<li>Se puede probar con simplemente recargar la aplicación y probar de nuevo.</li>
							</ul>
						</li>
						<li>El funcionamiento, por asuntos de validación de credenciales de Facebook, a veces es errático. Se puede intentar varias veces hasta que devuelva un archivo.</li>
						<li>Para grupos muy grandes en algunos casos es mejor seguir el siguiente procedimiento:
							<ol>
								<li>Descargar con un límite de por ejemplo 500.</li>
								<li>Luego, descargar con el mismo límite pero con un márgen de 500, para descargar a partir del elemento 500.</li>
								<li>Repetir cuantas veces se desee.</li>
							</ol></li>
						
						<li>Si encontrás un error o querés consultar cualquier cosa, podés escribirnos a <a href="mailto:vmuro@conectarlab.com.ar?subject=Oogster">vmuro@conectarlab.com.ar</a></li>
					</ul>
				</div>
				<footer>
				<p>El archivo devuelto no contendrá información sobre archivos cargados en el grupo. <br />
					En tanto la aplicación está en desarrollo, se recomienda limitar el número de ítems a descargar (por vez) a 750.<br />
					<strong>No se guarda ningún tipo de información personal en nuestros servidores.</strong> La información es cargada y devuelta al usuario directamente.</p>
				<a href="http://conectarlab.com.ar">conectar Lab.</a> | <a href="mailto:vmuro@conectarlab.com.ar?subject=Oogster">Contacto técnico</a> | Última actualización: 21.09.2012
				<?php if ($user) { ?><div class="logout"><a href="<?php echo $logoutUrl; ?>" class="logout">Cerrar sesión</a><br />(cerrarás sesión en Facebook también)</div><?php } ?>
				</footer>
			</div>
		</section>
	</div>
</body>
</html>