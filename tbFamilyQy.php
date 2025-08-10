<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'families' ) {

  $sql = "SELECT famId, famName, famIcon
          FROM tblFamily
		      ORDER BY famId";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "family": "'.trim($rows_emps->fields[1]).'", ');
    $icon = '<i class=\"mdi mdi-24px '.trim($rows_emps->fields[2]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'subFam' ) {

  $sql = "SELECT sfamId, sfamName, sfamIcon
          FROM tblSubFamily
          WHERE famId = $u AND isnull(isDeleted,0) = 0 ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "subfam": "'.trim($rows_emps->fields[1]).'", ');
    $icon = '<i class=\"mdi mdi-24px '.trim($rows_emps->fields[2]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'r' ) {
    $sql = "SELECT sfamId, sfamName, sfamIcon, famId
          FROM tblSubFamily
          WHERE sfamId = $u '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->sfamName = trim($rows_emps->fields[1]);
      $dataJson->sfamIcon = trim($rows_emps->fields[2]);
      $dataJson->famId = trim($rows_emps->fields[3]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->sfamName = '';
      $dataJson->sfamIcon = '';
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
?>