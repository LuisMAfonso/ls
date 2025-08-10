<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['d'] )) { $d = $_GET['d'];  } else { $d = ''; };
if ( isset( $_GET['w'] )) { $w = $_GET['w'];  } else { $w = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = ''; };

$userID = $_SESSION['user_id'];

$output = '';
$link = 0;
if ( $t == 'm') {

	$output  = "<?xml version='1.0' encoding='UTF-8'?>";
	$output .= "<menu>";
	$sql = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
			FROM (
			 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
  			 	FROM UsersMenus umn
			 	left join UsersGroups ugr on ugr.UserID = '$userID' 
			 	left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
  			 	WHERE LinkId = $link 
  			 	and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1)
			) a
  			ORDER BY Pos ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

	while(!$rows_emps->EOF) {
		$output .= '<item id="'.trim($rows_emps->fields[1]).'" text="'.utf8(trim($rows_emps->fields[2])).'" img="images/menu/'.trim($rows_emps->fields[4]).'">';
		if ( $rows_emps->fields[6] == 'm' ) {
			$sql1 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
			FROM (
			 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
  			 	FROM UsersMenus umn
			 	left join UsersGroups ugr on ugr.UserID = '$userID' 
			 	left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
  			 	WHERE LinkId = ".$rows_emps->fields[0]."  
  			 	and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1)
			) a
  			ORDER BY Pos  ";
			$rows_emps1 = $db->Execute($sql1);	

			while(!$rows_emps1->EOF) {
				$output .= '<item id="'.trim($rows_emps1->fields[1]).'" text="'.utf8(trim($rows_emps1->fields[2])).'" img="images/menu/'.trim($rows_emps1->fields[4]).'">';
				if ( $rows_emps1->fields[6] == 'm' ) {
					$sql2 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
							FROM (
			 					SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
  			 					FROM UsersMenus umn
			 					left join UsersGroups ugr on ugr.UserID = '$userID' 
			 					left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
  			 					WHERE LinkId = ".$rows_emps1->fields[0]."  
  			 					and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1)
							) a
  							ORDER BY Pos  ";
					$rows_emps2 = $db->Execute($sql2);	

					while(!$rows_emps2->EOF) {
						$output .= '<item id="'.trim($rows_emps2->fields[1]).'" text="'.utf8(trim($rows_emps2->fields[2])).'" img="images/menu/'.trim($rows_emps2->fields[4]).'">';
						if ( $rows_emps2->fields[6] == 'p' ) {
							$output .= '<href target="_self"><![CDATA[./'.trim($rows_emps2->fields[5]).']]></href>';
						}
						$output .= '</item>';
						$rows_emps2->MoveNext();
					}	
					$rows_emps2->Close();
				}
				if ( $rows_emps1->fields[6] == 'p' ) {
					$output .= '<href target="_self"><![CDATA[./'.trim($rows_emps1->fields[5]).']]></href>';
				}
				$output .= '</item>';
				$rows_emps1->MoveNext();
			}	
			$rows_emps1->Close();
		}
		if ( $rows_emps->fields[6] == 'p' ) {
			$output .= '<href target="_self"><![CDATA[./'.trim($rows_emps->fields[5]).']]></href>';
		}
		$output .= '</item>';
		$rows_emps->MoveNext();
	}	
	$rows_emps->Close();
	$output .= "</menu>";
	
	header("Content-type: text/xml");
	echo $output;

} if ( $t == 'gm') {

	$output  = "<?xml version='1.0' encoding='UTF-8'?>";
	$output .= "<rows>";
	$sql = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  			 FROM UsersMenus umn
			 left join UsersGroupsMn ugm on ugm.OptId = umn.Id
			 WHERE LinkId = ".$link." 
  			 ORDER BY Pos ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

	while(!$rows_emps->EOF) {
		$output .= '<row id="'.trim($rows_emps->fields[0]).'" open="1">';
		$output .= '<cell image="'.trim($rows_emps->fields[4]).'">'.utf8(trim($rows_emps->fields[2])).'</cell>';
		if ( $rows_emps->fields[6] == 'm' ) {
			$sql1 = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  			 			FROM UsersMenus umn
			 			left join UsersGroupsMn ugm on ugm.OptId = umn.Id
			 			WHERE LinkId = ".$rows_emps->fields[0]." 
  						ORDER BY Pos ";
			$rows_emps1 = $db->Execute($sql1);	

			while(!$rows_emps1->EOF) {
				$output .= '<row id="'.trim($rows_emps1->fields[0]).'" open="1">';
				$output .= '<cell image="'.trim($rows_emps1->fields[4]).'">'.utf8(trim($rows_emps1->fields[2])).'</cell>';
				if ( $rows_emps1->fields[6] == 'm' ) {
					$sql2 = " SELECT ID, OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo
  							FROM UsersMenus
  							WHERE LinkId = ".$rows_emps1->fields[0]." 
  							ORDER BY Pos ";
					$rows_emps2 = $db->Execute($sql2);	

					while(!$rows_emps2->EOF) {
						$output .= '<row id="'.trim($rows_emps2->fields[0]).'" open="1">';
						$output .= '<cell image="'.trim($rows_emps2->fields[4]).'">'.utf8(trim($rows_emps2->fields[2])).'</cell>';
						if ( $rows_emps2->fields[6] == 'p' ) {
							$output .= '<href target="_self"><![CDATA[./'.trim($rows_emps2->fields[5]).']]></href>';
						}
						$output .= '<cell>'.trim($rows_emps2->fields[8]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[9]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[10]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[11]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[7]).'</cell>';
						$output .= '</row>';
						$rows_emps2->MoveNext();
					}	
					$rows_emps2->Close();
				}
				if ( $rows_emps1->fields[6] == 'p' ) {
					$output .= '<href target="_self"><![CDATA[./'.trim($rows_emps1->fields[5]).']]></href>';
				}
				$output .= '<cell>'.trim($rows_emps1->fields[8]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[9]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[10]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[11]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[7]).'</cell>';
				$output .= '</row>';
				$rows_emps1->MoveNext();
			}	
			$rows_emps1->Close();
		}
		if ( $rows_emps->fields[6] == 'p' ) {
			$output .= '<cell>'.trim($rows_emps->fields[8]).'</cell>';
			$output .= '<cell>'.trim($rows_emps->fields[9]).'</cell>';
			$output .= '<cell>'.trim($rows_emps->fields[10]).'</cell>';
			$output .= '<cell>'.trim($rows_emps->fields[11]).'</cell>';
			$output .= '<cell>'.trim($rows_emps->fields[7]).'</cell>';
		}
		$output .= '</row>';
		$rows_emps->MoveNext();
	}	
	$rows_emps->Close();
	$output .= "</rows>";
	
	header("Content-type: text/xml");
	echo $output;

} if ( $t == 'am') {

	$output  = "<?xml version='1.0' encoding='UTF-8'?>";
	$output .= "<rows>";
	$sql = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  			 FROM UsersMenus umn
			 left join UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = $g
			 WHERE LinkId = ".$link." 
  			 ORDER BY Pos ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

	while(!$rows_emps->EOF) {
		$output .= '<row id="'.trim($rows_emps->fields[0]).'">';
		$output .= '<cell><![CDATA[<img src="images/menu/'.trim($rows_emps->fields[4]).'">]]></cell>';
		$output .= '<cell>'.utf8(trim($rows_emps->fields[2])).'</cell>';
		$output .= '<cell>'.trim($rows_emps->fields[8]).'</cell>';
		$output .= '<cell>'.trim($rows_emps->fields[9]).'</cell>';
		$output .= '<cell>'.trim($rows_emps->fields[10]).'</cell>';
		$output .= '<cell>'.trim($rows_emps->fields[11]).'</cell>';
		$output .= '<cell>'.trim($rows_emps->fields[7]).'</cell>';
		$output .= '</row>';
		if ( $rows_emps->fields[6] == 'm' ) {
			$sql1 = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  			 			FROM UsersMenus umn
			 			left join UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = $g
			 			WHERE LinkId = ".$rows_emps->fields[0]." 
  						ORDER BY Pos ";
			$rows_emps1 = $db->Execute($sql1);	

			while(!$rows_emps1->EOF) {
				$output .= '<row id="'.trim($rows_emps1->fields[0]).'">';
				$output .= '<cell><![CDATA[<img src="images/menu/'.trim($rows_emps1->fields[4]).'">]]></cell>';
				$output .= '<cell>--- '.utf8(trim($rows_emps1->fields[2])).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[8]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[9]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[10]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[11]).'</cell>';
				$output .= '<cell>'.trim($rows_emps1->fields[7]).'</cell>';
				$output .= '</row>';
				if ( $rows_emps1->fields[6] == 'm' ) {
					$sql2 = " SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, ugm.id, CanRead, CanCreate, CanModify, CanDelete
  							FROM UsersMenus umn
			 				left join UsersGroupsMn ugm on ugm.OptId = umn.Id and UGroupID = $g
  							WHERE LinkId = ".$rows_emps1->fields[0]." 
  							ORDER BY Pos ";
					$rows_emps2 = $db->Execute($sql2);	

					while(!$rows_emps2->EOF) {
						$output .= '<row id="'.trim($rows_emps2->fields[0]).'" open="1">';
						$output .= '<cell><![CDATA[<img src="images/menu/'.trim($rows_emps2->fields[4]).'">]]></cell>';
						$output .= '<cell>------ '.utf8(trim($rows_emps2->fields[2])).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[8]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[9]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[10]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[11]).'</cell>';
						$output .= '<cell>'.trim($rows_emps2->fields[7]).'</cell>';
						$output .= '</row>';
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
	$output .= "</rows>";
	
	header("Content-type: text/xml");
	echo $output;

} 


?>