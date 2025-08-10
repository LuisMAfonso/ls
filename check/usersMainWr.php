<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };


print_r($_POST);
print_r($_GET);
return;

if ( $t == 'a') {
	header("Content-type: application/json");
	$sql = "SELECT UserID, 
		UserName, 
		(CASE IsAdmin WHEN 'Y' THEN 'Sim' ELSE 'Não' END), 
		Employee, 
		email, 
		(CASE Locked WHEN 'Y' THEN 'Inativo' ELSE 'Ativo' END), 
		(CASE homeDeposit WHEN 1 THEN 'Sim' ELSE 'Não' END),
		st.Station,
		u.station,
		u.reset_pass,
		Locked,
		IsAdmin,
		homeDeposit,
		u.homeDepositPIN
        FROM Users AS u
        LEFT JOIN  Stations AS st ON st.StationID = u.Station
    ORDER BY UserName ";
	$rows_emps = $db->Execute($sql);
	echo json_encode($arrayTemp);
} 


?>