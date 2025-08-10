<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'grid' ) {

  $sql = "SELECT unitId, unitCode, unitDesig
          FROM tblProdUnit
		      ORDER BY unitDesig";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "code": "'.trim($rows_emps->fields[1]).'", ');
    print(' "descript": "'.trim($rows_emps->fields[2]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'r' ) {
    $sql = "SELECT unitId, unitCode, unitDesig
            FROM tblProdUnit
            WHERE unitId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->code = trim($rows_emps->fields[1]);
      $dataJson->descript = trim($rows_emps->fields[2]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
?>