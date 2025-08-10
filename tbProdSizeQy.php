<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'grid' ) {

  $sql = "SELECT sizeId, sizeCode, sizeDesig
          FROM tblProdSize
		      ORDER BY sizeDesig";
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
if ( $t == 'prices' ) {

  $sql = "SELECT pspId, dtFrom, dtTo, pspAmount, pspQuant, pu.unitCode
          FROM tblProdSizePrices psp
          LEFT JOIN tblProdUnit pu on pu.unitId = pspUnit
          where sizeId = '$id'
          ORDER BY dtFrom desc";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "dtFrom": "'.trim($rows_emps->fields[1]).'", ');
    print(' "dtTo": "'.trim($rows_emps->fields[2]).'", ');
    print(' "pspAmount": "'.trim($rows_emps->fields[3]).'", ');
    print(' "pspQuant": "'.trim($rows_emps->fields[4]).'", ');
    print(' "pspUnit": "'.trim($rows_emps->fields[5]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'r' ) {
    $sql = "SELECT sizeId, sizeCode, sizeDesig
            FROM tblProdSize
            WHERE sizeId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = new stdClass();
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
elseif ( $t == 'price') {

  $sql = "SELECT pspId, dtFrom, dtTo, pspAmount, pspQuant, pspUnit
          FROM tblProdSizePrices
          WHERE pspId = '$id' ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "pspId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "dtFrom": "'.trim($rows_emps->fields[1]).'", ');
    print(' "dtTo": "'.trim($rows_emps->fields[2]).'", ');
    print(' "pspAmount": "'.trim($rows_emps->fields[3]).'", ');
    print(' "pspQuant": "'.trim($rows_emps->fields[4]).'", ');
    print(' "pspUnit": "'.trim($rows_emps->fields[5]).'" ');
    print('}');
    $rows_emps->MoveNext();
  } else {
    print('{');
    print(' "pspId": "0", ');
    print(' "dtFrom": "'.date("Y-m-d").'", ');
    print(' "dtTo": "'.date("Y").'-12-31'.'", ');
    print(' "pspAmount": "0", ');
    print(' "pspQuant": "0", ');
    print(' "pspUnit": "" ');
    print('}');
  }
  $rows_emps->Close();
} 
?>