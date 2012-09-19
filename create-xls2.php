<?php

// Cargar PHPExcel
require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';
$objPHPExcel = new PHPExcel();
//$sheet = $objPHPExcel->getActiveSheet();

// FACEBOOK SDK PHP <3
require 'src/facebook.php';
require 'inc/config.php';

// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
	'appId'  => $appID,
	'secret' => $secret,
));

// Get User ID
$user = $facebook->getUser();

$grupo = $_GET['grupo'];

function simpleText($s) {
    $rpl = array("À" => 'A', "Á" => 'A', "Â" => 'A', "Ã" => 'A', "Ä" => 'A', "Å" => 'A',"à" => 'a', "á" => 'a', "â" => 'a', "ã" => 'a', "ä" => 'a', "å" => 'a',"Ò" => 'O', "Ó" => 'O', "Ô" => 'O', "Õ" => 'O', "Ö" => 'O', "Ø" => 'O',"ò" => 'o', "ó" => 'o', "ô" => 'o', "õ" => 'o', "ö" => 'o', "ø" => 'o',"È" => 'E', "É" => 'E', "Ê" => 'E', "Ë" => 'E',"è" => 'e', "é" => 'e', "ê" => 'e', "ë" => 'e',"Ç" => 'C',"ç" => 'c',"Ì" => 'I', "Í" => 'I', "Î" => 'I', "Ï" => 'I',"ì" => 'i', "í" => 'i', "î" => 'i', "ï" => 'i',"Ù" => 'U', "Ú" => 'U', "Û" => 'U', "Ü" => 'U',"ù" => 'u', "ú" => 'u', "û" => 'u', "ü" => 'u',"Ÿ" => 'Y',"ÿ" => 'y',"Ñ" => 'N',"ñ" => 'n');
    $s = preg_replace('`\s+`', '_', strtr($s, $rpl));
    $s = strtolower(preg_replace('`_+`', '_', preg_replace('`[^-_A-Za-z0-9]`', '', $s)));
    return trim($s, '_');
}

// Login or logout url will be needed depending on current user state.
if ($user) {
//  $logoutUrl = $facebook->getLogoutUrl();
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

		$limit = 1500;
		$user_groups = array();
		//$data = $facebook->api("/".$grupo."/feed/?limit=$limit&since=$since");
		$data = $facebook->api("/".$grupo."/feed/?limit=$limit");
		if(!is_array($data['data'])) { print_r($data); die; }
		$user_groups = array_merge($user_groups, $data["data"]);

		$group_name = $facebook->api("/".$grupo."/");
		$group_name = simpleText($group_name['name']);	

	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
	
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Conectar Lab.")
								->setLastModifiedBy("Conectar Lab.")
								->setTitle('Archivo del grupo "'.$group_name['name'].'"')
								->setSubject('Archivo del grupo "'.$group_name['name'].'"')
								->setDescription('Archivo del grupo de Facebook "'.$group_name['name'].'", generado por la aplicación creada por Conectar Lab')
								->setKeywords("facebook conectarlab grupo");

	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('01');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	$sheet = array();
	$sheet[] =  array('id','id_fb','from','from_id','message','picture','link','name','caption','description','type','created_time','updated_time','comments','likes');

	$rowID = 1;
	
	$i = count($user_groups);
	foreach($user_groups as $item) {
        //$each_note = $item['data'][$i];
        $fields = array (
						'id' 			=> $i,
                        'id_fb' 		=> $item['id'],
                        'from' 			=> $item['from']['name'],
                        'from_id' 		=> $item['from']['id'],
                        'message' 		=> $item['message'],
                        'picture' 		=> $item['picture'],
                        'link' 			=> $item['link'],
                        'name' 			=> $item['name'],
                        'caption' 		=> $item['caption'],
                        'description'	=> $item['description'],
                        'type' 			=> $item['type'],
                        'created_time' 	=> $item['created_time'],
                        'updated_time' 	=> $item['updated_time'],
                        'comments' 		=> $item['comments']['count'],
                        'likes' 		=> $item['likes']['count']
        );
		
	//	$fields = array_unshift($fields, $i);
		$sheet[] = $fields;
		
		$celltype_plain_text = PHPExcel_Cell_DataType::TYPE_STRING2;
		
		$columnID = 'A';
		foreach($fields as $columnValue) {
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($columnID.$rowID, $columnValue, $celltype_plain_text);
			$columnID++;
		}
		$rowID++;
		$i--;
	}
	
	//$objPHPExcel->getActiveSheet()->fromArray($sheet);

	####################
	## HEADERS DE EXCEL ##
	####################

	// We'll be outputting an excel file
	header('Content-type: application/vnd.ms-excel');

	// It will be called file.xls
	header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.xls');
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	//$objWriter->save(str_replace('.php', '.xls', __FILE__));
	$objWriter->save('php://output');
	
	

}
?>