<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'contacts' ) {

  $sql = "SELECT a.contId, contName, contCity, descFrom 
          FROM (    
            SELECT ct.contId, contName, contCity, min(isnull(cf.createstamp,'1900-01-01')) as createContact
                FROM contacts ct
            left  join contactsFrom cf on cf.contId = ct.contId and isnull(cf.isDeleted,0) = 0
                where isnull(ct.isDeleted,0) = 0
            GROUP BY ct.contId, contName, contCity
          ) a
          LEFT JOIN  contactsFrom cf on cf.contId = a.contId and isnull(cf.isDeleted,0) = 0 and cf.createStamp = a.createContact
          LEFT JOIN tbFrom tbf on tbf.idfrom = isnull(cf.contFrom,'ct') 
          order by contName";
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
    print(' "contCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "contOrigin": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'contact' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT contId, contName, contAddress, contZipcode, contCity, contCountry, contEmail, contPhone, cou.couName
            FROM contacts con
            INNER JOIN countries cou on cou.couCode = con.contCountry
            WHERE contId = $r ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "contId": "'.$r.'", ');
      print(' "contName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "contAddress": "'.trim($rows_emps->fields[2]).'", ');
      print(' "contZipcode": "'.trim($rows_emps->fields[3]).'", ');
      print(' "contCity": "'.trim($rows_emps->fields[4]).'", ');
      print(' "contCountry": "'.trim($rows_emps->fields[5]).'", ');
      print(' "contCode": "'.trim($rows_emps->fields[8]).'", ');
      print(' "contEmail": "'.trim($rows_emps->fields[6]).'", ');
      print(' "contPhone": "'.trim($rows_emps->fields[7]).'" ');
      print('}');
  } else {
      print('{');
      print(' "contId": "0", ');
      print(' "contName": "", ');
      print(' "contAddress": "", ');
      print(' "contZipcode": "", ');
      print(' "contCity": "", ');
      print(' "contCountry": "", ');
      print(' "contCode": "", ');
      print(' "contEmail": "", ');
      print(' "contPhone": "" ');
      print('}');
  }
} elseif ( $t == 'cnt' ) {

  if ( $id != 0 ) {    
      $sql = "SELECT cfId, contId, fromId, roleId
              FROM contactsFrom
              WHERE cfId = $id ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "Id": "'.$id.'", ');
      print(' "contId": "'.trim($rows_emps->fields[1]).'", ');
      print(' "roleId": "'.trim($rows_emps->fields[3]).'" ');
      print('}');
  } else {
      print('{');
      print(' "Id": "0", ');
      print(' "contId": "", ');
      print(' "roleId": "" ');
      print('}');
  }

}  
?>