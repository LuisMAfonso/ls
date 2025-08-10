<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'machines' ) {

  $sql = "SELECT machineId, macName, macPlate, tsub.sfamName, tsub.sfamIcon, (CASE macOwner WHEN 1 THEN 'Internal' ELSE 'External' END)
          FROM rsMachines rm
          LEFT JOIN  tblSubFamily tsub on tsub.sfamId = rm.subfamilyId
          where isnull(rm.isDeleted,0) = 0
          order by macName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "macName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "macPlate": "'.trim($rows_emps->fields[2]).'", ');
    print(' "sfamName": "'.trim($rows_emps->fields[3]).'", ');
    print(' "macOwner": "'.trim($rows_emps->fields[5]).'", ');
    $icon = '<i class=\"mdi mdi-24px '.trim($rows_emps->fields[4]).'\"> </i> ';
    print(' "Icon": "'.$icon.'"' );
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'machine' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT machineId, macName, macPlate, macOwner, macSerialNumber, macLabelCode, familyId, subFamilyId, fam.famName, sfam.sfamName, (CASE macOwner WHEN 1 THEN 'Internal' ELSE 'External' END) as macOwnerDesc, macSupplierId, sup.suppName, macMaintenance, macMaintUnit, macMaintFirst, macMaintNext, (CASE macMaintenance WHEN 1 THEN 'Yes' ELSE 'No' END) as macMaintenanceDesc
              FROM rsMachines mac
              left join tblFamily fam on fam.famId = mac.familyId
              left join tblSubFamily sfam on sfam.famId = mac.familyId and sfam.sfamId = subFamilyId 
              left join suppliers sup on sup.suppId = mac.macSupplierId
              WHERE machineId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "machineId": "'.$r.'", ');
      print(' "macName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "macPlate": "'.trim($rows_emps->fields[2]).'", ');
      print(' "macOwnerId": "'.trim($rows_emps->fields[3]).'", ');
      print(' "macOwner": "'.trim($rows_emps->fields[10]).'", ');
      print(' "macSupplierId": "'.trim($rows_emps->fields[11]).'", ');
      print(' "macSupplier": "'.trim($rows_emps->fields[12]).'", ');
      print(' "macMaintenance": "'.trim($rows_emps->fields[13]).'", ');
      print(' "macMaintUnit": "'.trim($rows_emps->fields[14]).'", ');
      print(' "macMaintFirst": "'.trim($rows_emps->fields[15]).'", ');
      print(' "macMaintNext": "'.trim($rows_emps->fields[16]).'", ');
      print(' "macMaintenanceDesc": "'.trim($rows_emps->fields[17]).'", ');
      print(' "macSerialNumber": "'.trim($rows_emps->fields[4]).'", ');
      print(' "macLabelCode": "'.trim($rows_emps->fields[5]).'", ');
      print(' "familyId": "'.trim($rows_emps->fields[6]).'", ');
      print(' "subFamilyId": "'.trim($rows_emps->fields[7]).'", ');
      print(' "famName": "'.trim($rows_emps->fields[8]).'", ');
      print(' "sfamName": "'.trim($rows_emps->fields[9]).'" ');
      print('}');
  } else {
      print('{');
      print(' "machineId": "0", ');
      print(' "macName": "", ');
      print(' "macPlate": "", ');
      print(' "macEquipmentTypeId": "", ');
      print(' "macSerialNumber": "", ');
      print(' "macLabelCode": "", ');
      print(' "familyId": "", ');
      print(' "subFamilyId": "", ');
      print(' "famName": "", ');
      print(' "sfamName": "" ');
      print('}');
  }

} if ( $t == 'requests' ) {

  $sql = "SELECT macReqId, prj.projName, convert(varchar(10),fromDate,120) as fDate, convert(varchar(10),toDate,120) as tDate,usr.UserName
          FROM rsMachinesRequests rmr
          INNER JOIN projects prj on prj.projId = rmr.projId
          INNER JOIN Users usr on usr.UserID = rmr.requestedBy 
          WHERE machineId = $r and isnull(rmr.isDeleted,0) = 0 
          ORDER BY fromDate desc";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "fromDate": "'.trim($rows_emps->fields[2]).'", ');
    print(' "toDate": "'.trim($rows_emps->fields[3]).'", ');
    print(' "userName": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
?>