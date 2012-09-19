<?php

####################
## HEADERS DE CSV ##
####################

// output headers so that the file is downloaded rather than displayed
header('Content-Type: application/json; charset=UTF-8');
header('Content-Disposition: attachment; filename='.$group_name.'-'.date('dmY', mktime()).'.xls');

function simpleText($s) {
    $rpl = array("�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A', "�" => 'A',"�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a', "�" => 'a',"�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O', "�" => 'O',"�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o', "�" => 'o',"�" => 'E', "�" => 'E', "�" => 'E', "�" => 'E',"�" => 'e', "�" => 'e', "�" => 'e', "�" => 'e',"�" => 'C',"�" => 'c',"�" => 'I', "�" => 'I', "�" => 'I', "�" => 'I',"�" => 'i', "�" => 'i', "�" => 'i', "�" => 'i',"�" => 'U', "�" => 'U', "�" => 'U', "�" => 'U',"�" => 'u', "�" => 'u', "�" => 'u', "�" => 'u',"�" => 'Y',"�" => 'y',"�" => 'N',"�" => 'n');
    $s = preg_replace('`\s+`', '_', strtr($s, $rpl));
    $s = strtolower(preg_replace('`_+`', '_', preg_replace('`[^-_A-Za-z0-9]`', '', $s)));
    return trim($s, '_');
}

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

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

// Login or logout url will be needed depending on current user state.

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
	
	print_r(json_encode($user_groups));

}

?>	