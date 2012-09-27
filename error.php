<?php

#########################################################################
## Oogster: Archivador de grupos de Facebook
## Copyright (C) 2012  Conectar Lab. (hola@conectarlab.com.ar)
## 
## This program is free software: you can redistribute it and/or modify
## it under the terms of the GNU General Public License as published by
## the Free Software Foundation, either version 3 of the License, or
## (at your option) any later version.
## 
## This program is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.
## 
## You should have received a copy of the GNU General Public License
## along with this program.  If not, see <http://www.gnu.org/licenses/>.
#########################################################################

if(!defined('Direct')){die('No est&aacute; permitido el acceso directo a este archivo.');}
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
	<title>Archivador de grupos de Facebook</title>
	<link rel="stylesheet" type="text/css" href="style.css" media="all" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link href='http://fonts.googleapis.com/css?family=Dosis:400,600' rel='stylesheet' type='text/css'>
	<meta http-equiv="Refresh" content="25;url=http://fb.conectarlab.com.ar/?grupo=<?php echo $group ?>"></head>
	<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-35016502-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>
	<div id="main">
		<header>
			<h1><a class="sprites oogster" href="/">Oogster</a></h1>
			<h2 class="sprites tagline">Archivador de grupos de Facebook</h2>
		</header>
		<section>
			<div>
				<h1>Ha habido un error, usted será redireccionado.</h1>
				<h2>Si la redirección automática no funciona, <a href="http://fb.conectarlab.com.ar/?grupo=<?php echo $group ?>">haga clic aquí</a>.</h2>
				<pre><?php print_r($data); ?></pre>

				<footer>
					<a href="http://conectarlab.com.ar">conectar Lab.</a> | <a href="mailto:vmuro@conectarlab.com.ar?subject=Oogster">Contacto técnico</a> | Última actualización: 21.09.2012
				<?php if ($user) { ?><div class="logout"><a href="<?php echo $logoutUrl; ?>" class="logout">Cerrar sesión</a><br />(cerrarás sesión en Facebook también)</div><?php } ?>
				</footer>
			</div>
		</section>
	</div>
</body>
</html>