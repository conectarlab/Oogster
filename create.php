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

// FACEBOOK SDK PHP <3
require 'src/facebook.php';
require 'inc/config.php';

$facebook = new Facebook(array(
	'appId'  => $appId,
	'secret' => $secret,
));

// Get User ID
$user = $facebook->getUser();

$group	= $_POST['group'];
$type	= $_POST['type'];
$limit	= $_POST['limit'];
$offset	= $_POST['offset'];

function simpleText($s) {
    $rpl = array("À" => 'A', "Á" => 'A', "Â" => 'A', "Ã" => 'A', "Ä" => 'A', "Å" => 'A',"à" => 'a', "á" => 'a', "â" => 'a', "ã" => 'a', "ä" => 'a', "å" => 'a',"Ò" => 'O', "Ó" => 'O', "Ô" => 'O', "Õ" => 'O', "Ö" => 'O', "Ø" => 'O',"ò" => 'o', "ó" => 'o', "ô" => 'o', "õ" => 'o', "ö" => 'o', "ø" => 'o',"È" => 'E', "É" => 'E', "Ê" => 'E', "Ë" => 'E',"è" => 'e', "é" => 'e', "ê" => 'e', "ë" => 'e',"Ç" => 'C',"ç" => 'c',"Ì" => 'I', "Í" => 'I', "Î" => 'I', "Ï" => 'I',"ì" => 'i', "í" => 'i', "î" => 'i', "ï" => 'i',"Ù" => 'U', "Ú" => 'U', "Û" => 'U', "Ü" => 'U',"ù" => 'u', "ú" => 'u', "û" => 'u', "ü" => 'u',"Ÿ" => 'Y',"ÿ" => 'y',"Ñ" => 'N',"ñ" => 'n');
    $s = preg_replace('`\s+`', '_', strtr($s, $rpl));
    $s = strtolower(preg_replace('`_+`', '_', preg_replace('`[^-_A-Za-z0-9]`', '', $s)));
    return trim($s, '_');
}

if ($user) {
	try {
		//$since = '2012-07-02T11:37:46+0000';
		//$since = strtotime($since);
		
		$user_groups = array();
		
		if(empty($limit)) { $limit = 750; }
		if(!empty($offset)) { $offset = "&offset=".$offset; }
		
		$data = $facebook->api("/".$group."/feed/?limit=".$limit.$offset);
		if(!is_array($data['data'])) { 
			define('Direct', TRUE);
			include('error.php');
			die; }
		$user_groups = array_merge($user_groups, $data["data"]);
		$group_name = $facebook->api("/".$group."/");
		$group_name = simpleText($group_name['name']);	
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}

	if($type == 'xls' || $type == 'ods') {
				
		// Cargar PHPExcel
		require_once dirname(__FILE__) . '/PHPExcel/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
	
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
		$celltype_plain_text	= PHPExcel_Cell_DataType::TYPE_STRING2;
		$header					= array('ID','Facebook ID','De','De (ID)','Mensaje','Imagen','Enlace','Nombre','Epígrafe','Descripción','Tipo','Fecha de creación','Fecha de actualización','Comentarios','Me gusta');

		$columnID	= 'A';
		$rowID		= 1;
		foreach($header as $columnValue) {
			$objPHPExcel->getActiveSheet()->setCellValueExplicit($columnID.$rowID, $columnValue, $celltype_plain_text);
			$columnID++;
		}
		
		$rowID	= 2;
		$i		= count($user_groups);
		foreach($user_groups as $item) {
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
						
			$columnID = 'A';
			foreach($fields as $columnValue) {
				$objPHPExcel->getActiveSheet()->setCellValueExplicit($columnID.$rowID, $columnValue, $celltype_plain_text);
				$columnID++;
			}
			$rowID++;
			$i--;
		}
		
		if($type == 'xls') {
			// HEADERS DE EXCEL
			header('Content-type: application/vnd.ms-excel; charset=UTF-8');
			header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.xls');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		/* } elseif($type="ods") {
			// HEADERS DE OPEN OFFICE
			header('Content-type: application/vnd.oasis.opendocument.spreadsheet; charset=UTF-8');
			header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.ods');
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'OOCalc'); */
		} 
		

		$objWriter->save('php://output');
	
	} elseif($type == 'csv') {
		
		// HEADERS DE CSV
		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.csv');
	
		// Creamos un pointer conectado al stream de salida
		$output = fopen('php://output', 'w');

		// Agregamos el título de las columnas
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


	} elseif($type == 'json') {
	
		// HEADERS JSON
		header('Content-Type: application/json; charset=UTF-8');
		header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.json');

		print_r(json_encode($user_groups));
	
	}
	

}

?>