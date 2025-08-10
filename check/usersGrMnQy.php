<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = ''; };



if ( $t == 'a') {
	header("Content-type: application/json");
	
	$sql = "SELECT usg.Id, UGroup FROM [BIrent].[dbo].UsersUGroup usg ";

	$rows_emps = $db->Execute($sql);
	
	$arrayTemp = array();
	//$count = 0;
	while(!$rows_emps->EOF) {
		$arrayTemp[] = [
			"id" => utf8(trim($rows_emps->fields[0])),
			"UGroup" => utf8(trim($rows_emps->fields[0])),
			"Nome" => utf8(trim($rows_emps->fields[1]))
		];
		//$count++;
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
	echo json_encode($arrayTemp);
} elseif ( $t == 'b') {
	
	header("Content-type: application/json");
 
	//  get records
	$sql = "SELECT us.UserID, u.UserName, 
		CASE 
			WHEN u.Locked = 'N' THEN 'Ativo'
			WHEN u.Locked = 'Y' THEN 'Inativo'
			ELSE u.Locked
		END
		FROM [BIrent].[dbo].[UsersGroups] AS us
		LEFT JOIN [BIrent].[dbo].[Users] AS u ON u.UserID = us.UserID
	WHERE us.UGroupID = '".$g."'
	ORDER BY u.Locked";
 	// $db->debug=1;

	$arrayTemp = array();

	$rows_emps = $db->Execute($sql);
	while(!$rows_emps->EOF) {
		$arrayTemp[] = [
			"user" => utf8(trim($rows_emps->fields[0])),
 			"name" => trim($rows_emps->fields[1]),
 			"status" => trim($rows_emps->fields[2])
		];
 		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
	echo json_encode($arrayTemp);
	
} elseif ( $t == 'c') {
	header("Content-type: application/json");

	 //$db->debug=1;
 
	$count = 1;
	$sql = "SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
			FROM prod.birent.dbo.UsersMenus umn
			LEFT JOIN prod.birent.dbo.UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = '".$g."'
			WHERE LinkId = '0' 
			ORDER BY Pos";
	$rows_emps = $db->Execute($sql);	

	$arrayTemp = array();
 
	while(!$rows_emps->EOF) {
		$count++;
		$arrayTemp[] = [
			"row-id" => trim($rows_emps->fields[0]),
			"icon-id" => utf8(trim($rows_emps->fields[2])),
			"read" => trim($rows_emps->fields[8]),
			"can-create" => trim($rows_emps->fields[9]),
			"can-modify" => trim($rows_emps->fields[10]),
			"can-delete" => trim($rows_emps->fields[11]),
			"id" => $count
		];
 		if ( $rows_emps->fields[6] == 'm' ) {
			$sql1 = "SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
					FROM prod.birent.dbo.UsersMenus umn
					LEFT JOIN prod.birent.dbo.UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = '".$g."'
					WHERE LinkId = ".$rows_emps->fields[0]." 
					ORDER BY Pos";
			$rows_emps1 = $db->Execute($sql1);	


			while(!$rows_emps1->EOF) {
				$count++;
				$arrayTemp[] = [
					"row-id" => trim($rows_emps1->fields[0]),
					"icon-id" => '--- '.utf8(trim($rows_emps1->fields[2])),
					"read" => trim($rows_emps1->fields[8]),
					"can-create" => trim($rows_emps1->fields[9]),
					"can-modify" => trim($rows_emps1->fields[10]),
					"can-delete" => trim($rows_emps1->fields[11]),
					"id" => $count
					];
				if ( $rows_emps1->fields[6] == 'm' ) {
					$sql2 = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  							FROM prod.birent.dbo.UsersMenus umn
			 				left join prod.birent.dbo.UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = '".$g."'
  							WHERE LinkId = ".$rows_emps1->fields[0]." 
  							ORDER BY Pos ";
					$rows_emps2 = $db->Execute($sql2);	
					

					while(!$rows_emps2->EOF) {
						$count++;
						$arrayTemp[] = [
							"row id" => trim($rows_emps2->fields[0]),
							"icon-id" =>  '------ '.utf8(trim($rows_emps2->fields[2])),
							"read" => trim($rows_emps2->fields[8]),
							"can-create" => trim($rows_emps2->fields[9]),
							"can-modify" => trim($rows_emps2->fields[10]),
							"can-delete" => trim($rows_emps2->fields[11]),
							"id" => $count
							];
						$rows_emps2->MoveNext();
					}	
					$rows_emps2->Close();
				}
				$rows_emps1->MoveNext();
			}	
			$rows_emps1->Close();
		}
		$rows_emps->MoveNext();
	}	
	$rows_emps->Close();

	echo json_encode($arrayTemp);
 }

?>