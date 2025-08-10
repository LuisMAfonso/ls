<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'users' ) {

  $sql = "SELECT Id, UserID, UserName, EMail, DateOpen, DateClose,  isnull(IsAdmin,0), (CASE isnull(avatar,'') WHEN '' THEN 'noImage.png' ELSE avatar END), staffId
		  FROM Users
		  ORDER BY UserName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "UserId": "'.trim($rows_emps->fields[1]).'", ');
    print(' "UserName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "Email": "'.trim($rows_emps->fields[3]).'", ');
    print(' "DateOpen": "'.trim($rows_emps->fields[4]).'", ');
    print(' "DateClose": "'.trim($rows_emps->fields[5]).'", ');
    print(' "avatar": "'.trim($rows_emps->fields[7]).'", ');
    print(' "staffId": "'.trim($rows_emps->fields[8]).'", ');

    $won = trim($rows_emps->fields[6]);
    if ( $won == '1' ) print(' "IsAdmin": 1 ');
    if ( $won == '0' ) print(' "IsAdmin": 0 ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'groups' ) {

  $sql = "SELECT ug.Id, gr.UGroup
		  FROM UsersGroups ug 
		  INNER JOIN UsersUGroup gr on gr.Id = UGroupID
		  WHERE ug.UserId = '$u'
		  ORDER BY gr.UGroup";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "Group": "'.trim($rows_emps->fields[1]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'r' ) {
    $sql = "SELECT Id, UserID, UserName, EMail, birthday, (CASE isnull(avatar,'') WHEN '' THEN 'noImage.png' ELSE avatar END), staffId
            FROM Users
            WHERE Id = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->userId = trim($rows_emps->fields[1]);
      $dataJson->password = '';
      $dataJson->name = trim($rows_emps->fields[2]);
      $dataJson->email = trim($rows_emps->fields[3]);
      $dataJson->birthday = trim($rows_emps->fields[4]);
      $dataJson->avatar->src = "https://agente.bimby.pt/ls/images/imgUsers/".trim($rows_emps->fields[5]);
      $dataJson->staffId = trim($rows_emps->fields[6]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'profile' ) {
    $sql = "SELECT Id, UserID, UserName, EMail, birthday, (CASE isnull(avatar,'') WHEN '' THEN 'noImage.png' ELSE avatar END), staffId
            FROM Users
            WHERE UserID = '".$_SESSION['userId']."' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->userId = trim($rows_emps->fields[1]);
      $dataJson->password = '';
      $dataJson->name = trim($rows_emps->fields[2]);
      $dataJson->email = trim($rows_emps->fields[3]);
      $dataJson->birthday = trim($rows_emps->fields[4]);
      $dataJson->staffId = trim($rows_emps->fields[4]);
      $dataJson->avatar->src = "https://agente.bimby.pt/ls/images/imgUsers/".trim($rows_emps->fields[5]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}

?>