<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'staff' ) {

  $sql = "SELECT staffId, staffNumber, staffName, cp.posName
          FROM staff stf 
          INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
          where isnull(isDeleted,0) = 0
          order by staffName";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "staffNumber": "'.trim($rows_emps->fields[1]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "staffPosition": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'employee' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT stf.staffId, stf.staffNumber, stf.staffName, stf.staffAddress, stf.staffZipcode, stf.staffCity, stf.staffCountry, stf.staffEmail, stf.staffPhone, stf.staffVatNumber, stf.staffIDnumber, stf.staffDriveLicense, stf.staffPosition, cp.posName, cou.couName, isnull(stf.staffAvatar,'noImage.png'), stfr.staffName, stf.staffReportTo
              FROM staff stf
              INNER JOIN tblCompPosition cp on cp.posId = isnull(stf.staffPosition,1)
              INNER JOIN countries cou on cou.couCode = stf.staffCountry
              LEFT  JOIN staff stfr on stfr.staffId = isnull(stf.staffReportTo,0)
              WHERE stf.staffId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->staffNumber = trim($rows_emps->fields[1]);
      $dataJson->staffName = trim($rows_emps->fields[2]);
      $dataJson->staffAddress = trim($rows_emps->fields[3]);
      $dataJson->staffZipcode = trim($rows_emps->fields[4]);
      $dataJson->staffCity = trim($rows_emps->fields[5]);
      $dataJson->staffCountry = trim($rows_emps->fields[14]);
      $dataJson->staffEmail = trim($rows_emps->fields[7]);
      $dataJson->staffPhone = trim($rows_emps->fields[8]);
      $dataJson->staffVatNumber = trim($rows_emps->fields[9]);
      $dataJson->staffIDnumber = trim($rows_emps->fields[10]);
      $dataJson->staffDriveLicense = trim($rows_emps->fields[11]);
      $dataJson->staffPositionId = trim($rows_emps->fields[12]);
      $dataJson->staffPosition = trim($rows_emps->fields[13]);
      $dataJson->staffCountryId = trim($rows_emps->fields[6]);
      $dataJson->staffAvatar->src = "https://agente.bimby.pt/ls/images/imgUsers/employee/".trim($rows_emps->fields[15]);
      $dataJson->staffReportTo = trim($rows_emps->fields[16]);
      $dataJson->staffReportToId = trim($rows_emps->fields[17]);
    } else {
      $dataJson->Id = 0;
      $dataJson->staffNumber = "";
      $dataJson->staffName = "";
      $dataJson->staffAddress = "";
      $dataJson->staffZipcode = "";
      $dataJson->staffCity = "";
      $dataJson->staffCountry = "";
      $dataJson->staffEmail = "";
      $dataJson->staffPhone = "";
      $dataJson->staffVatNumber = "";
      $dataJson->staffIDnumber = "";
      $dataJson->staffDriveLicense = "";
      $dataJson->staffPositionId = "";
      $dataJson->staffPosition = "";
      $dataJson->staffCountryId = "";
      $dataJson->staffAvatar = "";
      $dataJson->staffReportTo = "";
      $dataJson->staffReportToId = "";
    }
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} elseif ( $t == 'notes' ) {

  $sql = "SELECT noteId, notesTypeIcon, noteText, convert(varchar(16),dateNote,120) as dtNote
          FROM staffNotes sn
          INNER JOIN tblNotesTypes nt on nt.notesTypeId = sn.noteType
          WHERE isnull(isDeleted,0) = 0 and staffId = $r
          ORDER BY dateNote desc";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "noteId": "'.trim($rows_emps->fields[0]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[1]).'\"> </i> ';
    print(' "noteIcon": "'.$icon.'", ');
    print(' "noteText": "'.trim($rows_emps->fields[2]).'", ');
    print(' "notedate": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'rates' ) {

  $sql = "SELECT rateId, dtFrom, dtTo, hourCost, hourRate
          FROM staffPrices
          WHERE staffId = $r
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
    print(' "hourCost": "'.trim($rows_emps->fields[3]).'", ');
    print(' "hourRate": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'rate' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT rateId, dtFrom, dtTo, hourCost, hourRate
              FROM staffPrices
              where rateId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->dtFrom = trim($rows_emps->fields[1]);
      $dataJson->dtTo = trim($rows_emps->fields[2]);
      $dataJson->hourCost = trim($rows_emps->fields[3]);
      $dataJson->hourRate = trim($rows_emps->fields[4]);
    } else {
      $dataJson->Id = 0;
      $dataJson->dtFrom = "";
      $dataJson->dtTo = "";
      $dataJson->hourCost = "";
      $dataJson->hourRate = "";
    }
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} 

?>