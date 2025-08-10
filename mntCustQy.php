<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'companies' ) {

  $sql = "SELECT custId, custName, custCity, tct.cTypeName
          FROM customers cus
          INNER JOIN tblCustType tct on tct.cTypeId = isnull(cus.custTypeId,1)
          where isnull(cus.isDeleted,0) = 0
          order by custName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "custName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "custCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "custType": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'customer' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT custId, custName, custAddress, custZipcode, custCity, custCountry, custEmail, custPhone, cou.couName, isnull(cus.custTypeId,1), tct.cTypeName, isnull(cus.custBusActId,1), tba.bActName, vatNumber
            FROM customers cus
            INNER JOIN countries cou on cou.couCode = cus.custCountry
            INNER JOIN tblCustType tct on tct.cTypeId = isnull(cus.custTypeId,1)
            INNER JOIN tblBusActivity tba on tba.bActId = isnull(cus.custBusActId,1)
            WHERE custId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "custId": "'.$r.'", ');
      print(' "custName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "custAddress": "'.trim($rows_emps->fields[2]).'", ');
      print(' "custZipcode": "'.trim($rows_emps->fields[3]).'", ');
      print(' "custCity": "'.trim($rows_emps->fields[4]).'", ');
      print(' "custCountry": "'.trim($rows_emps->fields[5]).'", ');
      print(' "custCode": "'.trim($rows_emps->fields[8]).'", ');
      print(' "custEmail": "'.trim($rows_emps->fields[6]).'", ');
      print(' "custPhone": "'.trim($rows_emps->fields[7]).'", ');
      print(' "custType": "'.trim($rows_emps->fields[10]).'", ');
      print(' "custTypeId": "'.trim($rows_emps->fields[9]).'", ');
      print(' "custBusAct": "'.trim($rows_emps->fields[12]).'", ');
      print(' "custBusActId": "'.trim($rows_emps->fields[11]).'", ');
      print(' "vatNumber": "'.trim($rows_emps->fields[13]).'" ');
      print('}');
  } else {
      print('{');
      print(' "custId": "0", ');
      print(' "custName": "", ');
      print(' "custAddress": "", ');
      print(' "custZipcode": "", ');
      print(' "custCity": "", ');
      print(' "custCountry": "", ');
      print(' "custCode": "", ');
      print(' "custEmail": "", ');
      print(' "custPhone": "", ');
      print(' "custType": "", ');
      print(' "custTypeId": "", ');
      print(' "custBusAct": "", ');
      print(' "custBusActId": "", ');
      print(' "vatNumber": "" ');
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

} 
?>