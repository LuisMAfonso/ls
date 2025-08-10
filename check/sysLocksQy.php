<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/
if ( $t == 'a') {
	header("Content-type: application/json");
	$sql = "SELECT p.spid, p.status, p.hostname, p.loginame, p.cpu, r.blocking_session_id, r.wait_time, CONVERT(VARCHAR,r.start_time,120), r.command, p.program_name, text 
		FROM   sys.dm_exec_requests AS r,
		master.dbo.sysprocesses  AS p 
		CROSS APPLY sys.dm_exec_sql_text(p.sql_handle)
		WHERE  p.status NOT IN ('sleeping', 'background') 
	AND r.session_id = p.spid";	
	$rows_emps = $db->Execute($sql);
	
	$arrayTemp = array();
	$count = 1;
	while(!$rows_emps->EOF) {
		if(trim($rows_emps->fields[4]) > 1000){
			$customVar = true;
		}
		else{
			$customVar = false;
		}
		$arrayTemp[] = [
			"id" => $count,
			"Spid" => trim($rows_emps->fields[0]),
			"Status" => trim($rows_emps->fields[1]),
			"Hostname" => trim($rows_emps->fields[2]),
			"login" => trim($rows_emps->fields[3]),
			"CPU" => trim($rows_emps->fields[4]),
			"Bloking" => trim($rows_emps->fields[5]),
			"Wait_ms" => trim($rows_emps->fields[6]),
			"Start_time" => trim($rows_emps->fields[7]),
			"Command" => trim($rows_emps->fields[8]),
			"ProgName" => trim($rows_emps->fields[9]),
			"Text" => trim($rows_emps->fields[10]),
			"kill" => "<img src='../images/icons/delete.png' width='24' height='24'>",
            "custom" => $customVar
		];
		$count++;
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
	echo json_encode($arrayTemp);
} 

?>