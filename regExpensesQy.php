<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };
if ( isset( $_GET['df'] )) { $df = $_GET['df'];  } else { $df = ''; };
if ( isset( $_GET['dt'] )) { $dt = $_GET['dt'];  } else { $dt = ''; };

if ( $t == 'expenses' ) { 

  $sql = "SELECT ep.expId ,sFamName, expDate, stf.staffName, expValue, isnull(expImage,'noImage.png'), right(isnull(expImage,'noImage.png'),3), ep.createUser
          FROM expenses ep 
          INNER JOIN tblSubFamily et on et.sFamId = expType 
          LEFT JOIN staff stf on stf.staffId = ep.staffId
          WHERE isnull(ep.isDeleted,0) = 0 and expDate between '$df' and '$dt' ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "expTypeName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "expDate": "'.trim($rows_emps->fields[2]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[3]).'", ');
    print(' "expValue": "'.trim($rows_emps->fields[4]).'", ');
    print(' "image": "'.trim($rows_emps->fields[5]).'", ');
    print(' "user": "'.trim($rows_emps->fields[7]).'", ');
    print(' "ext": "'.trim($rows_emps->fields[6]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'expense' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT expId, expDate, et.sFamName, prj.projName, stf.staffName, expValue, convert(varchar(16),ex.createStamp,120), usr.userName, expNotes
            FROM expenses ex
            INNER JOIN tblSubFamily et on et.sFamId = expType 
            INNER JOIN users usr on usr.userId = ex.createUser
            LEFT  JOIN projects prj on prj.projId = expProj 
            LEFT  JOIN staff stf on stf.staffId = ex.staffId
            where isnull(ex.isDeleted,0) = 0 and expId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "Id": "'.$r.'", ');
      print(' "expDate": "'.trim($rows_emps->fields[1]).'", ');
      print(' "expTypeName": "'.trim($rows_emps->fields[2]).'", ');
      print(' "projName": "'.trim($rows_emps->fields[3]).'", ');
      print(' "staffName": "'.trim($rows_emps->fields[4]).'", ');
      print(' "expValue": "'.trim($rows_emps->fields[5]).'", ');
      print(' "workDone": "'.trim($rows_emps->fields[8]).'", ');
      print(' "createStamp": "'.trim($rows_emps->fields[6]).'", ');
      print(' "userName": "'.trim($rows_emps->fields[7]).'" ');
      print('}');
  } else {
      print('{');
      print(' "Id": "0", ');
      print(' "expDate": "", ');
      print(' "expTypeName": "", ');
      print(' "expDate": "", ');
      print(' "projName": "", ');
      print(' "staffName": "", ');
      print(' "expValue": "", ');
      print(' "workDone": "", ');
      print(' "createStamp": "", ');
      print(' "userName": "" ');
      print('}');
  }

} if ( $t == 'contacts' ) {

  $sql = "SELECT cnt.contId, contName, rl.roleName, contEmail, contPhone
          FROM contacts cnt
          inner join contactsFrom cf on cf.contFrom = 'cu' and fromId = $r and cf.contId = cnt.contId and isnull(cf.isDeleted,0) = 0
          inner join tblRoles rl on rl.roleId = cf.roleId
          where isnull(cnt.isDeleted,0) = 0 ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "contName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "contRole": "'.trim($rows_emps->fields[2]).'", ');
    print(' "contEmail": "'.trim($rows_emps->fields[3]).'", ');
    print(' "contPhone": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'exp' ) {
    $sql = "SELECT expId, expDate, expType, staffId, expProj, expValue, expNotes, sFamName
            FROM expenses ex
            INNER JOIN tblSubFamily et on et.sFamId = expType 
            where isnull(ex.isDeleted,0) = 0 and expId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->expDate = trim($rows_emps->fields[1]);
      $dataJson->expId = trim($rows_emps->fields[2]);
      $dataJson->staffId = trim($rows_emps->fields[3]);
      $dataJson->projId = trim($rows_emps->fields[4]);
      $dataJson->expValue = trim($rows_emps->fields[5]);
      $dataJson->workDone = trim($rows_emps->fields[6]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
} 
?>