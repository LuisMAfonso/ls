<?php

include( "../include.php" );

require_once '../vendor/mobiledetect/mobiledetectlib/Mobile_Detect.php';
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer'); 
$scriptVersion = 'other'; 
if ( $detect->version('iPad') ) { $scriptVersion = 'iPad'; }
if ( $detect->version('iPhone') ) { $scriptVersion = 'iPhone'; }
if ( $detect->version('Android') ) { $scriptVersion = 'Android'; }
if ( $detect->version('iPad') ) { $scriptVersion = 'iPad'; }
if ( $detect->version('Windows NT') ) { $scriptVersion = 'Windows NT'; }
if ( $detect->version('Windows Phone') ) { $scriptVersion = 'Windows Phone'; }

$_SESSION['userId'] = '';
$_SESSION['companyId'] = '';
$_SESSION['Id'] = '';
$_SESSION['admin'] = '';
$_SESSION['userName'] = '';

$sql = "SELECT Id, CompanyId, UserID, UserName, isAdmin
		FROM Users usr
		where DateClose is null and UserId=? and Pass=? ";
//$db->debug=1;
$rows = $db->Execute($sql, [$_POST['usrname'], $_POST['pass']]);

while(!$rows->EOF) 
{
	$_SESSION['userId'] = $rows->fields[2];
	$_SESSION['companyId'] = $rows->fields[1];
	$_SESSION['Id'] = $rows->fields[0];
	$_SESSION['admin'] = $rows->fields[4];
	$_SESSION['userName'] = $rows->fields[3];

	$rows->MoveNext();
}
$rows->Close();

if ( $_SESSION['userId'] == '' ) {
	$sql = "INSERT INTO UsersLogs ( LogUser, LogDate, LogDevice, LogVersion, LogSuccess ) VALUES ('".$_POST['usrname']."', getdate(), '".$deviceType."', '".$scriptVersion."', 0 ) ";
//	$rows = $db->Execute($sql);	
	$message = '<p>User or password not right</p><p><a href="index.php">Please try again</a></p>';
	if ( $_POST['usrname'] != '' ) { 
		echo '<script>window.location.href = "index.php?message=error";</script>';
	} else {
		echo '<script>window.location.href = "index.php";</script>';
	}
}else{
	$sql = "INSERT INTO UsersLogs ( LogUser, LogDate, LogDevice, LogVersion, LogSuccess ) VALUES ('".$_POST['usrname']."', getdate(), '".$deviceType."', '".$scriptVersion."', 1 ) ";
	$rows = $db->Execute($sql);

	echo "<b>Redirecting...</b>";
	echo '<script>window.location.href = "main.php";</script>';
}

?> 