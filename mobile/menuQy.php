<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };

$userID = $_SESSION['userId'];
$userName = $_SESSION['userName'];

$output = '';
if ( $t == 'per') {

	$sql = "SELECT umn.OptId, max(isnull(CanRead,0)) as canRead, max(isnull(CanCreate,0)) as canCreate, max(isnull(CanModify,0)) as canModify, max(isnull(CanDelete,0)) as canDelete
  			FROM UsersMenus umn
  			inner join UsersGroupsMenu ugm on ugm.optId = umn.Id 
  			inner join UsersGroups usg on usg.UGroupID = ugm.UGroupID and usg.UserID = '$userID'
  			where umn.optId = '$p'
  			group by umn.OptId";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	


   	$dataJson = '';
    $dataJson->canRead = trim($rows_emps->fields[1]);
    $dataJson->canCreate = trim($rows_emps->fields[2]);
    $dataJson->canModify = trim($rows_emps->fields[3]);
    $dataJson->canDelete = trim($rows_emps->fields[4]);

    $myJSON = json_encode($dataJson);
    echo $myJSON;
}

if ( $t == 'data') {

	$sql = "SELECT OptDesc, Icon
			  FROM UsersMenusMobile
			  where prog = '$p' ";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

   	$dataJson = '';
    $dataJson->name = trim($rows_emps->fields[0]);
    $dataJson->icon = 'mdi mdi-24px '.trim($rows_emps->fields[1]);

    $sql = "SELECT isnull(avatar,'noImage.png')
			  FROM Users
			  WHERE UserID = '$userID'";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	
    $dataJson->avatar = trim($rows_emps->fields[0]);
    
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'menu' ) {


	$sql = "SELECT ID, OptDesc, Icon, Prog
			  FROM UsersMenusMobile
			  WHERE linkId = 0 AND Pos > 0
			  ORDER BY pos";
//  	$db->debug=1;
	$rows_emps = $db->Execute($sql);	

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "id": "'.(trim($rows_emps->fields[0])).'", ');
  	print(' "value": "'.(trim($rows_emps->fields[1])).'", ');
  	print(' "prog": "'.(trim($rows_emps->fields[3])).'", ');
  	$icon = '<i class=\"mdi mdi-48px '.(trim($rows_emps->fields[2])).'\"> </i> ';
  	print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
?>