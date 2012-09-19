<?php

function simpleText($s) {
    $rpl = array("�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A',"�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a',"�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O',"�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o',"�" => 'E', "�" => 'E', "�" => 'E', "�" => 'E',"�" => 'e', "�" => 'e', "�" => 'e', "�" => 'e',"�" => 'C',"�" => 'c',"�" => 'I', "�" => 'I', "�" => 'I', "�" => 'I',"�" => 'i', "�" => 'i', "�" => 'i', "�" => 'i',"�" => 'U', "�" => 'U', "�" => 'U', "�" => 'U',"�" => 'u', "�" => 'u', "�" => 'u', "�" => 'u',"�" => 'Y',"�" => 'y',"�" => 'N',"�" => 'n');
    $s = preg_replace('`\s+`', '_', strtr($s, $rpl));
    $s = strtolower(preg_replace('`_+`', '_', preg_replace('`[^-_A-Za-z0-9]`', '', $s)));
    return trim($s, '_');
}

require 'src/facebook.php';
require 'inc/config.php';

$facebook = new Facebook(array(
	'appId'  => $appId,
	'secret' => $secret,
));

$user = $facebook->getUser();
$grupo = $_GET['grupo'];

if ($user) {
	$logoutUrl = $facebook->getLogoutUrl(array( 'next' => ( 'http://'.$_SERVER['SERVER_NAME'].'/logout.php') ));
} else {
	$params = array('scope' => 'user_groups,friends_groups');
	$loginUrl = $facebook->getLoginUrl($params);
	die;
}

if ($user) {
	try {
		//$since = '2012-07-02T11:37:46+0000';
		//$since = strtotime($since);
		//$offset = 0;
		$limit = 750;
		$user_groups = array();
		//$data = $facebook->api("/".$grupo."/feed/?limit=$limit&since=$since");
		$data = $facebook->api("/".$grupo."/feed/?limit=$limit");
		$user_groups = array_merge($user_groups, $data["data"]);
		$group_name = $facebook->api("/".$grupo."/");
		$group_name = simpleText($group_name['name']);	
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}

    // Creamos un pointer conectado al stream de salida
    $output = fopen('php://output', 'w');

    // Agregamos el t�tulo de las columnas
    fputcsv($output, array('id','id_fb','from','from_id','message','picture','link','name','caption','description','type','created_time','updated_time','comments','likes'), "\t" );

	$i = 1;
	foreach($user_groups as $item) {
        //$each_note = $item['data'][$i];
        $fields = array (
                        'id_fb' => $item['id'],
                        'from' => $item['from']['name'],
                        'from_id' => $item['from']['id'],
                        'message' => $item['message'],
                        'picture' => $item['picture'],
                        'link' => $item['link'],
                        'name' => $item['name'],
                        'caption' => $item['caption'],
                        'description' => $item['description'],
                        'type' => $item['type'],
                        'created_time' => $item['created_time'],
                        'updated_time' => $item['updated_time'],
                        'comments' => $item['comments']['count'],
                        'likes' => $item['likes']['count']
        );



		foreach($fields as $k => $v) {  $fields[$k] = utf8_encode($v); }

		
		$row = array($i, $fields['id_fb'], $fields['from'], $fields['from_id'], $fields['message'], $fields['picture'], $fields['link'], $fields['name'], $fields['caption'], $fields['description'], $fields['type'], $fields['created_time'], $fields['updated_time'], $fields['comments'], $fields['likes']);

		fputcsv($output, $row, "\t" );

		$i++;
	}

		####################
		## HEADERS DE CSV ##
		####################

		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.xls');

	}

?>	