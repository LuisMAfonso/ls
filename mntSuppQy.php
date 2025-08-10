<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'suppliers' ) {

  $c1 = '"';
  $c2 = '\"';

  $sql = "SELECT suppId, replace(suppName,'$c1','$c2') as suppName, suppCity, tct.sTypeName
          FROM suppliers sup
          INNER JOIN tblSuppType tct on tct.sTypeId = isnull(sup.suppTypeId,1)
          where isnull(sup.isDeleted,0) = 0
          order by suppName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "suppName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "suppCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "suppType": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'supplier' ) {

  $c1 = '"';
  $c2 = '\"';

  if ( $r != 0 ) {    
      $sql = "SELECT suppId, replace(suppName,'$c1','$c2') as suppName, suppAddress, suppZipcode, suppCity, suppCountry, suppEmail, suppPhone, cou.couName, isnull(sup.suppTypeId,1), tct.sTypeName, isnull(sup.suppBusActId,1), tba.bActName, vatNumber
            FROM suppliers sup
            INNER JOIN countries cou on cou.couCode = sup.suppCountry
            INNER JOIN tblsuppType tct on tct.sTypeId = isnull(sup.suppTypeId,1)
            INNER JOIN tblBusActivity tba on tba.bActId = isnull(sup.suppBusActId,1)
            WHERE suppId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "suppId": "'.$r.'", ');
      print(' "suppName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "suppAddress": "'.trim($rows_emps->fields[2]).'", ');
      print(' "suppZipcode": "'.trim($rows_emps->fields[3]).'", ');
      print(' "suppCity": "'.trim($rows_emps->fields[4]).'", ');
      print(' "suppCountry": "'.trim($rows_emps->fields[5]).'", ');
      print(' "suppCode": "'.trim($rows_emps->fields[8]).'", ');
      print(' "suppEmail": "'.trim($rows_emps->fields[6]).'", ');
      print(' "suppPhone": "'.trim($rows_emps->fields[7]).'", ');
      print(' "suppType": "'.trim($rows_emps->fields[10]).'", ');
      print(' "suppTypeId": "'.trim($rows_emps->fields[9]).'", ');
      print(' "suppBusAct": "'.trim($rows_emps->fields[12]).'", ');
      print(' "suppBusActId": "'.trim($rows_emps->fields[11]).'", ');
      print(' "vatNumber": "'.trim($rows_emps->fields[13]).'" ');
      print('}');
  } else {
      print('{');
      print(' "suppId": "0", ');
      print(' "suppName": "", ');
      print(' "suppAddress": "", ');
      print(' "suppZipcode": "", ');
      print(' "suppCity": "", ');
      print(' "suppCountry": "", ');
      print(' "suppCode": "", ');
      print(' "suppEmail": "", ');
      print(' "suppPhone": "", ');
      print(' "suppType": "", ');
      print(' "suppTypeId": "", ');
      print(' "suppBusAct": "", ');
      print(' "suppBusActId": "", ');
      print(' "vatNumber": "" ');
      print('}');
  }

} 
if ( $t == 'contacts' ) {

  $sql = "SELECT cf.cfId, contName, rl.roleName, contEmail, contPhone
          FROM contacts cnt
          inner join contactsFrom cf on cf.contFrom = 'su' and fromId = $r and cf.contId = cnt.contId and isnull(cf.isDeleted,0) = 0
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
if ( $t == 'supply' ) {

  $sql = "SELECT ss.Id, fam.famName, fam.famIcon, sfam.sfamName, sfam.sfamIcon
          FROM suppliersSupply ss
          INNER JOIN tblSubFamily sfam on sfam.sfamId = ss.sfamId
          INNER JOIN tblFamily fam on fam.famId = sfam.famId
          WHERE suppId = $r 
          ORDER BY famName, sfamName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "supFam": "'.trim($rows_emps->fields[1]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[2]).'\"> </i> ';
    print(' "ficon": "'.$icon.'", ');
    print(' "supSFam": "'.trim($rows_emps->fields[3]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[4]).'\"> </i> ';
    print(' "sficon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

}  
?>