<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };


if ( $t == 'i') {
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
	
	$arrayTemp = array();
	//$count = 0;
	while(!$rows_emps->EOF) {
		//isadmin
		if(utf8(trim($rows_emps->fields[11])) == 'Y'){$isadminTemp = 1;}else{$isadminTemp = 0;}
		//resetPass
		if(utf8(trim($rows_emps->fields[9])) == '1'){$resetPassTemp = 1;}else{$resetPassTemp = 0;}
		//homeDeposit
		if(utf8(trim($rows_emps->fields[12])) == '1'){$homeDepTemp = 1;}else{$homeDepTemp = 0;}
		//locked
		if(utf8(trim($rows_emps->fields[10])) == 'Y'){$lockedTemp = 1;}else{$lockedTemp = 0;}

	    if(trim($rows_emps->fields[5]) == 'Ativo' && trim($rows_emps->fields[3]) !== ''){
            $infoSend = "<img src='../images/icons/m_send.png' width='24' height='24'>";
        }
        else{
        	$infoSend = "";
        }
        if(utf8(trim($rows_emps->fields[5])) == 'Ativo'){
            $estadotemp = "<span style='display: inline-block; width: 12px; height: 12px; background-color: #0fcc45; opacity: 0.9; border-radius: 50%;' class='on'></span>";
        }
        else{
            $estadotemp = "<span style='display: inline-block; width: 12px; height: 12px; background-color: #FF0000; opacity: 0.9; border-radius: 50%;' class='on'></span>";
        }
		$arrayTemp[] = [
			"id" => utf8(trim($rows_emps->fields[0])),
			"UserID" => utf8(trim($rows_emps->fields[0])),
			"UserName" => utf8(trim($rows_emps->fields[1])),
			"IsAdminG" => utf8(trim($rows_emps->fields[2])),
			"Employee" => utf8(trim($rows_emps->fields[3])),
			"email" => utf8(trim($rows_emps->fields[4])),
			"homeDepositG" => utf8(trim($rows_emps->fields[6])),
			"LockedG" => $estadotemp.' '.utf8(trim($rows_emps->fields[5])),
			"send" => $infoSend,
			"stationG" => utf8(trim($rows_emps->fields[7])),
			"station" => utf8(trim($rows_emps->fields[8])),
			"reset_pass" => $resetPassTemp,
			"Locked" => $lockedTemp,
			"IsAdmin" => $isadminTemp,
			"homeDeposit" => $homeDepTemp,
			"homeDepositPIN" => utf8(trim($rows_emps->fields[13]))
		];
		//$count++;
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
	echo json_encode($arrayTemp);
} 
elseif ( $t == 'checkUserExist') {
	$arrayTemp = array();
    $sql = "SELECT * FROM Users WHERE UserID = '".$r."'";
    $rows_emps = $db->Execute($sql);
    if($rows_emps->rowCount() > 0){
		$arrayTemp = array("result" => "true");
        $rows_emps->MoveNext();
    }
    else {
		$arrayTemp = array("result" => "false");
        $rows_emps->MoveNext();
    }
    $rows_emps->Close();
	echo json_encode($arrayTemp);
}  
elseif ( $t == 'getStations') {
	$arrayTemp = array();
    $sql = "SELECT StationID, Station FROM Stations ORDER BY StationID";
    //$db->debug=1;
    $rows_emps = $db->Execute($sql);
    while(!$rows_emps->EOF) {
		$arrayTemp[] = [
			"id" => utf8(trim($rows_emps->fields[0])),
			"value" => utf8(trim($rows_emps->fields[1]))
		];
		$rows_emps->MoveNext();
    }
    $rows_emps->Close();
	echo json_encode($arrayTemp);
}   

?>