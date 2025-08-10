<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['f'] )) { $f = $_GET['f'];  } else { $f = ''; };


if ( $t == 'a') {
	header("Content-type: application/json");

	 //$db->debug=1;
 
	$sql = "SELECT ID, OptId, OptDesc, LinkId, Icon, Prog, Tipo, Pos ,IconNew
			FROM birent.dbo.UsersMenus
			WHERE LinkId = '0' 
			ORDER BY Pos";
	$rows_emps = $db->Execute($sql);	

	$arrayTemp = array();
 
	while(!$rows_emps->EOF) {
        $iconMenu = '<i class="fa '.utf8(trim($rows_emps->fields[8])).'"> </i> ';
		$arrayTemp[] = [
			"id" => trim($rows_emps->fields[0]),
			"name" => $iconMenu.'&nbsp;&nbsp; '.utf8(trim($rows_emps->fields[2])),
			"OptId" => utf8(trim($rows_emps->fields[1])),
			"Descricao" => utf8(trim($rows_emps->fields[2])),
			"DescricaoPai" => utf8(trim($rows_emps->fields[3])),
			"NomeFicheiro" => utf8(trim($rows_emps->fields[5])),
			"Tipo" => utf8(trim($rows_emps->fields[6])),
			"Pos" => utf8(trim($rows_emps->fields[7])),
			"Icon" => utf8(trim($rows_emps->fields[8]))
		];
 		if ( $rows_emps->fields[6] == 'm' ) {
			$sql1 = "SELECT ID, OptId, OptDesc, LinkId, Icon, Prog, Tipo, Pos --,IconNew
					FROM prod.birent.dbo.UsersMenus 
					WHERE LinkId = ".$rows_emps->fields[0]." 
					ORDER BY Pos";
			$rows_emps1 = $db->Execute($sql1);	


			while(!$rows_emps1->EOF) {
				$arrayTemp[] = [
					"id" => trim($rows_emps1->fields[0]),
					"name" => trim($rows_emps1->fields[2]),
					"parent" => utf8(trim($rows_emps1->fields[3])),
					"OptId" => utf8(trim($rows_emps1->fields[1])),
					"Descricao" => utf8(trim($rows_emps1->fields[2])),
					"DescricaoPai" => utf8(trim($rows_emps1->fields[3])),
					"NomeFicheiro" => utf8(trim($rows_emps1->fields[5])),
					"Tipo" => utf8(trim($rows_emps1->fields[6])),
					"Pos" => utf8(trim($rows_emps1->fields[7])),
					//"Icon" => utf8(trim($rows_emps1->fields[8]))
					];
				if ( $rows_emps1->fields[6] == 'm' ) {
					$sql2 = " SELECT ID, OptId, OptDesc, LinkId, Icon, Prog, Tipo, Pos --,IconNew
  							FROM prod.birent.dbo.UsersMenus 
  							WHERE LinkId = ".$rows_emps1->fields[0]." 
  							ORDER BY Pos ";
					$rows_emps2 = $db->Execute($sql2);	
					

					while(!$rows_emps2->EOF) {
						$arrayTemp[] = [
							"id" => trim($rows_emps2->fields[0]),
							"name" => trim($rows_emps2->fields[2]),
							"parent" =>  utf8(trim($rows_emps2->fields[3])),
							"OptId" => utf8(trim($rows_emps2->fields[1])),
							"Descricao" => utf8(trim($rows_emps2->fields[2])),
							"DescricaoPai" => utf8(trim($rows_emps2->fields[3])),
							"NomeFicheiro" => utf8(trim($rows_emps2->fields[5])),
							"Tipo" => utf8(trim($rows_emps2->fields[6])),
							"Pos" => utf8(trim($rows_emps2->fields[7])),
							//"Icon" => utf8(trim($rows_emps2->fields[8]))
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
else if ( $t == 'b') {
	header("Content-type: application/json");
	$sql = "SELECT groupMn.UGroupID, uGroup.UGroup , groupMn.CanRead, groupMn.CanCreate, groupMn.CanModify, groupMn.CanDelete 
	FROM prod.birent.dbo.UsersGroupsMn AS groupMn
		LEFT JOIN prod.birent.dbo.UsersUGroup as uGroup ON groupMn.UGroupID = uGroup.ID
		WHERE groupMn.OptId = '$c'  
		ORDER BY groupMn.UGroupID ASC";
	$rows_emps = $db->Execute($sql);
	
	$arrayTemp = array();
	$count = 0;
	while(!$rows_emps->EOF) {
		$arrayTemp[] = [
			"id" => $count,
			"GroupID" => utf8(trim($rows_emps->fields[0])),
			"GrupoDesc" => utf8(trim($rows_emps->fields[1]))
		];
		$count++;
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
	echo json_encode($arrayTemp);
} 
else if($t == 'getDescricaoPai'){
	$arrayTemp = array();


	//GET DADOS
	$sql = "SELECT * FROM prod.[BIrent].[dbo].UsersMenus WHERE LinkId = 0 AND Tipo = 'm' ORDER BY Pos ASC";
	//print($sql);
	//$db->debug=1;
	$rows_emps = $db->Execute($sql);
	while(!$rows_emps->EOF) {
		//print("<option value='".utf8(trim($rows_emps->fields[0]))."'><![CDATA[".utf8(trim($rows_emps->fields[2]))."]]></option>");
		$arrayTemp[] = [
			"id" => utf8(trim($rows_emps->fields[0])),
			"value" => utf8(trim($rows_emps->fields[2]))
		];
		$sql1 = "SELECT * FROM prod.[BIrent].[dbo].UsersMenus WHERE LinkId = ".utf8(trim($rows_emps->fields[0]))." AND Tipo = 'm' ORDER BY Pos ASC";
		$rows_emps1 = $db->Execute($sql1);
		while(!$rows_emps1->EOF) {
			//print("<option value='".utf8(trim($rows_emps1->fields[0]))."'><![CDATA[-- ".utf8(trim($rows_emps1->fields[2]))."]]></option>");
			$arrayTemp[] = [
				"id" => utf8(trim($rows_emps1->fields[0])),
				"value" => "-- ".utf8(trim($rows_emps1->fields[2]))
			];
			$sql2 = "SELECT * FROM prod.[BIrent].[dbo].UsersMenus WHERE LinkId = ".utf8(trim($rows_emps1->fields[0]))." AND Tipo = 'm' ORDER BY Pos ASC";
			$rows_emps2 = $db->Execute($sql2);
			while(!$rows_emps2->EOF) {
				//print("<option value='".utf8(trim($rows_emps2->fields[0]))."'><![CDATA[---- ".utf8(trim($rows_emps2->fields[2]))."]]></option>");
				$arrayTemp[] = [
					"id" => utf8(trim($rows_emps2->fields[0])),
					"value" => "---- ".utf8(trim($rows_emps2->fields[2]))
				];
				$rows_emps2->MoveNext();
			}
			$rows_emps2->Close();
			$rows_emps1->MoveNext();
		}
		$rows_emps1->Close();
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();

	echo json_encode($arrayTemp);
}

?>