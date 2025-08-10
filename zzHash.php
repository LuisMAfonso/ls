<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'chkSes') {
  checkAccess('admin',$_SERVER[REQUEST_URI]);
}

if ( $t == 'pass' ) {

  $sql = "SELECT [Id],[UserID],[Pass] ,[hashPass]
          FROM [fl].[dbo].[Users]";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  while(!$rows_emps->EOF) {

    $hash = password_hash(trim($rows_emps->fields[2]), PASSWORD_DEFAULT); 
    echo $hash.'<br>';

    $sql2 = "UPDATE users 
              SET hashPass = '$hash' 
              WHERE id = ".$rows_emps->fields[0];
    $rows_emps2 = $db->Execute($sql2);

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();


} 
if ( $t == 'reset' ) {

  $sql = "SELECT [Id],[UserID],[Pass] ,[hashPass]
          FROM [fl].[dbo].[Users]
          WHERE id = 1";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  while(!$rows_emps->EOF) {

    $hash = password_hash('1234', PASSWORD_DEFAULT); 
    echo $hash.'<br>';

    $sql2 = "UPDATE users 
              SET hashPass = '$hash' 
              WHERE id = ".$rows_emps->fields[0];
    $rows_emps2 = $db->Execute($sql2);

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();


} 

?>