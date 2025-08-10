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

}  elseif ( $t == 'times' ) {

  $sql = "SELECT stfTmId, prj.projName, convert(varchar(10),stfTmFrom,120) as tmDay, convert(varchar(5),stfTmFrom,24) as tmFrom,  convert(varchar(5),stfTmTo,24) as TmTo,  convert(varchar(5),timeBreak,24) as TmBreak  , (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, stm.projId
          FROM staffTime stm
          LEFT JOIN projects prj on prj.projId = stm.projId
          WHERE StaffId = $r and isnull(stm.isDeleted,0) = 0
          ORDER BY stfTmFrom desc";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "projId": "'.trim($rows_emps->fields[1]).'", ');
    print(' "date": "'.trim($rows_emps->fields[2]).'", ');
    print(' "TmFrom": "'.trim($rows_emps->fields[3]).'", ');
    print(' "TmTo": "'.trim($rows_emps->fields[4]).'", ');
    print(' "TmBreak": "'.trim($rows_emps->fields[5]).'", ');
    print(' "numHR": "'.trim($rows_emps->fields[6]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'r' ) { 
    $sql2 = "SELECT replace(convert(varchar(5),BreakTime,24),':','') FROM tblDefaults ";
    $rows_emps2 = $db->Execute($sql2);
    $defBreak = trim($rows_emps2->fields[0]);

    $sql = "SELECT stfTmId, projId,convert(varchar(10),stfTmFrom,120) as tmDay, replace(convert(varchar(5),stfTmFrom,24),':','') as tmFrom,  replace(convert(varchar(5),stfTmTo,24),':','') as tmTo,  replace(convert(varchar(5),timeBreak,24),':','') as tmBreak, workDone
            FROM staffTime stm
            WHERE stfTmId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->projId = trim($rows_emps->fields[1]);
      $dataJson->date = trim($rows_emps->fields[2]);
      $dataJson->TmFrom = trim($rows_emps->fields[3]);
      $dataJson->TmTo = trim($rows_emps->fields[4]);
      $dataJson->TmBreak = trim($rows_emps->fields[5]);
      $dataJson->workDone = trim($rows_emps->fields[6]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->TmBreak = $defBreak;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
} elseif ( $t == 'ut' ) {

  $sql = "SELECT convert(varchar(10),[stfTmFrom],120) as dtTime, sum((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0) as qtHours
          FROM staffTime
          where staffid = $r and isnull(isDeleted,0) = 0
          GROUP BY convert(varchar(10),[stfTmFrom],120)
          ORDER BY convert(varchar(10),[stfTmFrom],120) ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('{');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    $return = '';
    if ( trim($rows_emps->fields[1]) < 3 ) $return = 'day_mark2';
    elseif  ( trim($rows_emps->fields[1]) < 5 ) $return = 'day_mark4';
    elseif  ( trim($rows_emps->fields[1]) < 7 ) $return = 'day_mark6';
    else $return = 'day_mark8';
    print(' "'.trim($rows_emps->fields[0]).'": "'.$return.'" ');

    $rows_emps->MoveNext();
  }
  print('}');
  $rows_emps->Close();

} 

?>