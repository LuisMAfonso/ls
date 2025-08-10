<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };


//print_r($_POST);
//print_r($_GET);
//return;

if ( $t == 'd') {
	//Dados do Servidor
	$DB_SERVER = "SERVER01";
	$DB_USER   = "sa";
	$DB_PASS   = "drive2011";
	$DB_NAME   = "master";

	include '../adodb5/adodb.inc.php';
	$db = adoNewConnection('mssqlnative');
	$db->setConnectionParameter('CharacterSet','UTF-8');
	$db->connect($DB_SERVER,$DB_USER,$DB_PASS,$DB_NAME);

	$sql = "kill ".$r;
	$rows_emps = $db->Execute($sql);
} 

?>