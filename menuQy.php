<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = ''; };

$userID = $_SESSION['userId'];
$userName = $_SESSION['userName'];

$output = '';
$link = 0;
if ( $t == 'main') {

	$output .= "[";
	$sql = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
			FROM (
			 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
  			 	FROM UsersMenus umn
			 	left join UsersGroups ugr on ugr.UserID = '$userID' 
			 	left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
  			 	WHERE LinkId = $link 
  			 	and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1  OR tipo = 's' OR tipo = 'e')
			) a
  			ORDER BY Pos ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

	$first = 1;
	while(!$rows_emps->EOF) {
		if ( $first == 0 ) $output .= ', ';
		if ( $first == 1 ) $first = 0;
		if ( $rows_emps->fields[6] == 's' ) {
			$output .= '{ ';
			$output .= '"type": "separator" ';
			$output .= '} ';
		} else if ( $rows_emps->fields[6] == 'e' ) {
			$output .= '{ ';
			$output .= '"type": "spacer" ';
			$output .= '} ';
		} else{
			$output .= '{ ';
			$output .= '"id": "'.trim($rows_emps->fields[1]).'", ';
			$output .= '"value": "'.trim($rows_emps->fields[2]).'", ';
			$output .= '"icon": "'.trim($rows_emps->fields[4]).'", ';
			if ( $rows_emps->fields[6] == 'm' ) {
				$output .= '  "call": "menu", ';
				$output .= '  "items": [ ';
				$sql1 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
				FROM (
				 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
	  			 	FROM UsersMenus umn
				 	left join UsersGroups ugr on ugr.UserID = '$userID' 
				 	left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
	  			 	WHERE LinkId = ".$rows_emps->fields[0]."  
	  			 	and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1)
				) a
	  			ORDER BY Pos  ";
				$rows_emps1 = $db->Execute($sql1);	

				$firstL1 = 1;
				while(!$rows_emps1->EOF) {
					if ( $firstL1 == 0 ) $output .= ', ';
					if ( $firstL1 == 1 ) $firstL1 = 0;
					$output .= '{ ';
					$output .= '"id": "'.trim($rows_emps1->fields[1]).'", ';
					$output .= '"value": "'.trim($rows_emps1->fields[2]).'", ';
					$output .= '"icon": "'.trim($rows_emps1->fields[4]).'", ';
					if ( $rows_emps1->fields[6] == 'm' ) {
						$output .= '  "call": "menu", ';
						$output .= '  "items": [ ';
						$sql2 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos
								FROM (
				 					SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
	  			 					FROM UsersMenus umn
				 					left join UsersGroups ugr on ugr.UserID = '$userID' 
				 					left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
	  			 					WHERE LinkId = ".$rows_emps1->fields[0]."  
	  			 					and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1)
								) a
	  							ORDER BY Pos  ";
						$rows_emps2 = $db->Execute($sql2);	

						$firstL2 = 1;
						while(!$rows_emps2->EOF) {
							if ( $firstL2 == 0 ) $output .= ', ';
							if ( $firstL2 == 1 ) $firstL2 = 0;
							$output .= '{ ';
							$output .= '"id": "'.trim($rows_emps2->fields[1]).'", ';
							$output .= '"value": "'.trim($rows_emps2->fields[2]).'", ';
							$output .= '"icon": "'.trim($rows_emps2->fields[4]).'", ';
							if ( $rows_emps2->fields[6] == 'p' ) {
								$output .= '"call": "'.trim($rows_emps2->fields[5]).'" ';
							}
							$output .= '} ';
							$rows_emps2->MoveNext();
						}	
						$rows_emps2->Close();
						$output .= '] ';
					}
					if ( $rows_emps1->fields[6] == 'p' ) {
						$output .= '"call": "'.trim($rows_emps1->fields[5]).'" ';
					}
					$output .= '} ';
					$rows_emps1->MoveNext();
				}	
				$rows_emps1->Close();
				$output .= '] ';
			}
			if ( $rows_emps->fields[6] == 'p' ) {
				$output .= '"call": "'.trim($rows_emps->fields[5]).'" ';
			}
			$output .= '} ';
		}
		$rows_emps->MoveNext();
	}	
	$rows_emps->Close();
	$output .= "]";
	
	header("Content-type: application/json");
	echo $output;

} 
if ( $t == 'per') {

	$sql = "SELECT umn.OptId, max(isnull(CanRead,0)) as canRead, max(isnull(CanCreate,0)) as canCreate, max(isnull(CanModify,0)) as canModify, max(isnull(CanDelete,0)) as canDelete
  			FROM UsersMenus umn
  			inner join UsersGroupsMenu ugm on ugm.optId = umn.Id 
  			inner join UsersGroups usg on usg.UGroupID = ugm.UGroupID and usg.UserID = '$userID'
  			where umn.optId = '$p'
  			group by umn.OptId";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	


   	$dataJson = new stdClass();
    $dataJson->canRead = trim($rows_emps->fields[1]);
    $dataJson->canCreate = trim($rows_emps->fields[2]);
    $dataJson->canModify = trim($rows_emps->fields[3]);
    $dataJson->canDelete = trim($rows_emps->fields[4]);

    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'perm') {

	$sql = "SELECT umn.OptId, max(isnull(CanRead,0)) as canRead, max(isnull(CanCreate,0)) as canCreate, max(isnull(CanModify,0)) as canModify, max(isnull(CanDelete,0)) as canDelete
  			FROM UsersMenusMobile umn
  			inner join UsersGroupsMenuMobile ugm on ugm.optId = umn.Id 
  			inner join UsersGroups usg on usg.UGroupID = ugm.UGroupID and usg.UserID = '$userID'
  			where umn.optId = '$p'
  			group by umn.OptId";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	


   	$dataJson = new stdClass();
    $dataJson->canRead = trim($rows_emps->fields[1]);
    $dataJson->canCreate = trim($rows_emps->fields[2]);
    $dataJson->canModify = trim($rows_emps->fields[3]);
    $dataJson->canDelete = trim($rows_emps->fields[4]);

    $myJSON = json_encode($dataJson);
    echo $myJSON;
}

if ( $t == 'data') {

	$sql = "SELECT OptDesc, Icon
			  FROM UsersMenus
			  where prog = '$p' ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

   	$dataJson = new stdClass();
    $dataJson->name = trim($rows_emps->fields[0]);
    $dataJson->icon = trim($rows_emps->fields[1]);

    $sql = "SELECT isnull(avatar,'noImage.png')
			  FROM Users
			  WHERE UserID = '$userID'";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	
    $dataJson->avatar = trim($rows_emps->fields[0]);
    
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'man') {

	$output .= "[";
	$sql = "SELECT Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, ugmID, CanRead, CanCreate, CanModify, CanDelete
			FROM (
			 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, ugm.id as ugmID,  (CASE isnull(CanRead,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanRead, (CASE isnull(CanCreate,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanCreate, (CASE isnull(CanModify,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanModify, (CASE isnull(CanDelete,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanDelete
  			 	FROM UsersMenus umn
			 	left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = $g
  			 	WHERE LinkId = $link and tipo not in ('e','s')
			) a
  			ORDER BY Pos ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

	$first = 1;
	while(!$rows_emps->EOF) {
		if ( $first == 0 ) $output .= ', ';
		if ( $first == 1 ) $first = 0;
		if ( $rows_emps->fields[6] == 's' ) {
			$output .= '{ ';
			$output .= '"type": "separator" ';
			$output .= '} ';
		} else if ( $rows_emps->fields[6] == 'e' ) {
			$output .= '{ ';
			$output .= '"type": "spacer" ';
			$output .= '} ';
		} else{
			$output .= '{ ';
			$output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    		$iconMenu = '<i class=\"'.trim($rows_emps->fields[4]).'\"> </i> ';
   			$output .= '"value": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps->fields[2]).'", ';
			$output .= '"icon": "'.trim($rows_emps->fields[4]).'", ';
			$output .= '"CanRead": '.trim($rows_emps->fields[9]).', ';
			$output .= '"CanCreate": '.trim($rows_emps->fields[10]).', ';
			$output .= '"CanModify": '.trim($rows_emps->fields[11]).', ';
			$output .= '"CanDelete": '.trim($rows_emps->fields[12]).', ';
			if ( $rows_emps->fields[6] == 'm' ) {
				$output .= '  "call": "menu", ';
				$output .= '  "items": [ ';
				$sql1 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, ugmID, CanRead, CanCreate, CanModify, CanDelete
				FROM (
				 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, ugm.id as ugmID, (CASE isnull(CanRead,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanRead, (CASE isnull(CanCreate,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanCreate, (CASE isnull(CanModify,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanModify, (CASE isnull(CanDelete,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanDelete
	  			 	FROM UsersMenus umn
			 		left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = $g
	  			 	WHERE LinkId = ".$rows_emps->fields[0]."  and tipo not in ('e','s')
				) a
	  			ORDER BY Pos  ";
				$rows_emps1 = $db->Execute($sql1);	

				$firstL1 = 1;
				while(!$rows_emps1->EOF) {
					if ( $firstL1 == 0 ) $output .= ', ';
					if ( $firstL1 == 1 ) $firstL1 = 0;
					$output .= '{ ';
					$output .= '"id": "'.trim($rows_emps1->fields[0]).'", ';
		    		$iconMenu = '<i class=\"'.trim($rows_emps1->fields[4]).'\"> </i> ';
		   			$output .= '"value": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps1->fields[2]).'", ';
					$output .= '"icon": "'.trim($rows_emps1->fields[4]).'", ';
					$output .= '"CanRead": '.trim($rows_emps1->fields[9]).', ';
					$output .= '"CanCreate": '.trim($rows_emps1->fields[10]).', ';
					$output .= '"CanModify": '.trim($rows_emps1->fields[11]).', ';
					$output .= '"CanDelete": '.trim($rows_emps1->fields[12]).', ';
					if ( $rows_emps1->fields[6] == 'm' ) {
						$output .= '  "call": "menu", ';
						$output .= '  "items": [ ';
						$sql2 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, ugmID, CanRead, CanCreate, CanModify, CanDelete
								FROM (
								 	SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, ugm.id as ugmID, (CASE isnull(CanRead,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanRead, (CASE isnull(CanCreate,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanCreate, (CASE isnull(CanModify,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanModify, (CASE isnull(CanDelete,0) WHEN 0 THEN 'false' ELSE 'true' END) as CanDelete
					  			 	FROM UsersMenus umn
							 		left join UsersGroupsMenu ugm on ugm.OptId = umn.Id and ugm.UGroupID = $g
					  			 	WHERE LinkId = ".$rows_emps1->fields[0]." and tipo not in ('e','s')  
								) a
					  			ORDER BY Pos  ";
						$rows_emps2 = $db->Execute($sql2);	

						$firstL2 = 1;
						while(!$rows_emps2->EOF) {
							if ( $firstL2 == 0 ) $output .= ', ';
							if ( $firstL2 == 1 ) $firstL2 = 0;
							$output .= '{ ';
							$output .= '"id": "'.trim($rows_emps2->fields[0]).'", ';
				    		$iconMenu = '<i class=\"'.trim($rows_emps2->fields[4]).'\"> </i> ';
				   			$output .= '"value": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps2->fields[2]).'", ';
							$output .= '"icon": "'.trim($rows_emps2->fields[4]).'", ';
							$icon = '<i class=\"mdi mdi-18px mdi-checkbox-blank-outline\"> </i> ';
							$output .= '"CanRead": '.trim($rows_emps2->fields[9]).', ';
							$output .= '"CanCreate": '.trim($rows_emps2->fields[10]).', ';
							$output .= '"CanModify": '.trim($rows_emps2->fields[11]).', ';
							$output .= '"CanDelete": '.trim($rows_emps2->fields[12]).', ';
							if ( $rows_emps2->fields[6] == 'p' ) {
								$output .= '"call": "'.trim($rows_emps2->fields[5]).'" ';
							}
							$output .= '} ';
							$rows_emps2->MoveNext();
						}	
						$rows_emps2->Close();
						$output .= '] ';
					}
					if ( $rows_emps1->fields[6] == 'p' ) {
						$output .= '"call": "'.trim($rows_emps1->fields[5]).'" ';
					}
					$output .= '} ';
					$rows_emps1->MoveNext();
				}	
				$rows_emps1->Close();
				$output .= '] ';
			}
			if ( $rows_emps->fields[6] == 'p' ) {
				$output .= '"call": "'.trim($rows_emps->fields[5]).'" ';
			}
			$output .= '} ';
		}
		$rows_emps->MoveNext();
	}	
	$rows_emps->Close();
	$output .= "]";
	
	header("Content-type: application/json");
	echo $output;

} 
?>