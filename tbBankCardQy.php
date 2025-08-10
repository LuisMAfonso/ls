<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'grid' ) {

  $sql = "SELECT cardId, cardNumber, bank, bankAccount, cardLimit, stf.staffName
          FROM tbBankCards tbc
          LEFT JOIN staff stf on stf.staffId = assignto 
		      ORDER BY cardNumber";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "cardNumber": "'.trim($rows_emps->fields[1]).'", ');
    print(' "bank": "'.trim($rows_emps->fields[2]).'", ');
    print(' "bankAccount": "'.trim($rows_emps->fields[3]).'", ');
    print(' "cardLimit": "'.trim($rows_emps->fields[4]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[5]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'r' ) {
    $sql = "SELECT cardId, cardNumber, bank, bankAccount, cardLimit, assignTo
            FROM tbBankCards tbc
            WHERE cardId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->cardNumber = trim($rows_emps->fields[1]);
      $dataJson->bank = trim($rows_emps->fields[2]);
      $dataJson->bankAccount = trim($rows_emps->fields[3]);
      $dataJson->cardLimit = trim($rows_emps->fields[4]);
      $dataJson->staffId = trim($rows_emps->fields[5]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
?>