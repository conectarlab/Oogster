Oogster
=======

La aplicación desarrollada permite descargar en un formato estructurado (hoja de cálculo de Microsoft 
Excel) la información que fue cargada por los miembros de un grupo de Facebook al que el usuario de 
la aplicación pertenece. Una vez que la aplicación es autorizada, se ofrece la lista de los grupos a 
los que pertenece para que se elija uno y pueda descargarse el material del grupo en el formato deseado.

Instalación
===========

(Estas instrucciones son más bien esquemáticas, si no entendés alguno de los pasos,
podés preguntar por correo electrónico a desarrollo@conectarlab.com.ar)

1. 	Descargar PHPExcel de http://www.phpexcel.net/ e instalarlo en /PHPExcel.
	Verificar que funcione correctamente.
2. 	Colocar en / los archivos de Oogster.
3. 	Registrar una aplicación nueva en Facebook Developers (https://developers.facebook.com/apps),
	para obtener las keys requeridas para el funcionamiento de la API con la aplicación. Los permisos
	requeridos por la aplicación son "user_groups" y "friends_groups".
4.	Configurar lo requerido en /inc/config.php (renombrado desde config-example.php)


Cómo / Paso a paso
====================

1.	Ingresamos a fb.conectarlab.com.ar. Nos encontraremos con la primer pantalla de la 
	aplicación donde debemos autorizar el uso de nuestra cuenta de Facebook por parte de 
	la aplicación. Para ello haremos clic en el botón de “Conectar usando Facebook”, que 
	nos redireccionará a una pantalla de Facebook para otorgrar los privilegios correspondientes.

2.	Como se ve en esta pantalla, somos presentados ante un cuadro de Facebook donde 
	debemos autorizar la aplicación para que tenga acceso a i) nuestra información básica, 
	ii) nuestros grupos iii) los grupos de nuestros amigos. Si bien sólo utilizamos la 
	información de nuestros grupos, el tercer permiso es necesario para funcionar. La 
	aplicación jamás publicará algo en nuestros perfiles.

3.	Una vez otorgados los permisos somos devueltos a la aplicación donde habrá aparecido 
	un cuadro de diálogo para elegir el grupo que queremos descargar. Lo seleccionamos 
	y hacemos clic en “Seleccionar”.

4.	En esta pantalla podremos seleccionar el formato de salida. Por lo general 
	seleccionaremos “Microsoft Excel” aquí, para tenerlo en formato de hoja de cálculos.
	En las dos opciones que hay por debajo podremos seleccionar en Límite cuántos elementos 
	queremos que se incluyan en nuestro archivo de descarga, y en Margen podremos elegir a 
	partir de qué elemento descargar. De esta manera, podremos descargar de a bloques de 500 
	si en límite indicamos “500” y en margen colocamos “0”, luego “500”, luego “1000” y así. 
	Por defecto, si dejamos los cuadros en blanco, el archivo se descargará en formato de 
	Microsoft Excel con límite 750 y márgen 0.

5.	Esta aplicación está en desarrollo, por lo que hay ciertos [errores conocidos]. 
	En particular, es posible que la primera vez que se intenta descargar algún grupo, 
	surja un error. A veces con intentar con otro grupo se soluciona, o simplemente 
	actualizando la página. De encontrarse con algún error pueden escribirnos 
	a desarrollo@conectarlab.com.ar

Licencia
========

Oogster es Software Libre, distribuido bajo la licencia GNU/GPL.