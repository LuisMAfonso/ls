<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'projects' ) {

  $sql = "SELECT prj.projId, projName, projCity, ps.prjStatusIcon, sum((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0) as tTime, ps.prjStatusName
          FROM projects prj 
          INNER JOIN tblProjectStatus ps on ps.prjStatusId = isnull(projStatus,1)
          LEFT  JOIN staffTime st on st.projId = prj.projId
          where isnull(prj.isDeleted,0) = 0
          group by  prj.projId, projName, projCity, ps.prjStatusIcon, ps.prjStatusName
          order by projName";
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
    print(' "projCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[4]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[3]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

}  elseif ( $t == 'staff' ) {

  $sql = "SELECT stf.staffId, stf.staffName, cp.posName, sum((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0) as numHr
          FROM staffTime stm
          INNER JOIN staff stf on stf.staffId = stm.StaffId
          INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
          WHERE stm.projId = $p 
          GROUP BY stf.staffId, stf.staffName, cp.posName
          ORDER BY staffName desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "posName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'allStaff' ) {

  $sql = "SELECT stf.staffId, stf.staffName, cp.posName, 0 as numHr
          FROM staff stf 
          INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
          WHERE isnull(stf.isDeleted,0) = 0
          ORDER BY staffName  ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "posName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "selStaff": '.trim($rows_emps->fields[3]).', ');
    print(' "tmFrom": "", ');
    print(' "tmTo": "" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

}  elseif ( $t == 'times' ) {

  $sql = "SELECT stfTmId, stf.staffName, convert(varchar(10),stfTmFrom,120) as tmDay, convert(varchar(5),stfTmFrom,24) as tmFrom,  convert(varchar(5),stfTmTo,24) as TmTo,  convert(varchar(5),timeBreak,24) as TmBreak , (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, stm.projId
          FROM staffTime stm
          INNER JOIN staff stf on stf.staffId = stm.StaffId
          WHERE stm.projId = $p ";
  if ( $r != 0 ) $sql .= " AND stm.staffId = $r ";
  $sql .= " ORDER BY stfTmFrom desc";
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

    $sql = "SELECT projName FROM projects prj where projId = $p ";
    $rows_emps = $db->Execute($sql);
    $projName = trim($rows_emps->fields[0]);

    $sql = "SELECT stfTmId,prj.projName, stm.StaffId, convert(varchar(10),stfTmFrom,120) as tmDay, convert(varchar(5),stfTmFrom,24) as tmFrom,  convert(varchar(5),stfTmTo,24) as tmTo, convert(varchar(5),timeBreak,24) as tmBreak
            FROM staffTime stm
            INNER JOIN projects prj on prj.projId = stm.projId
            WHERE stfTmId ='$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->projName = trim($rows_emps->fields[1]);
      $dataJson->staffId = trim($rows_emps->fields[2]);
      $dataJson->date = trim($rows_emps->fields[3]);
      $dataJson->TmFrom = trim($rows_emps->fields[4]);
      $dataJson->TmTo = trim($rows_emps->fields[5]);
      $dataJson->TmBreak = trim($rows_emps->fields[6]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->projName = $projName;
      $dataJson->staffId = $s;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
} elseif ( $t == 'ut' ) {

  $sql = "SELECT convert(varchar(10),[stfTmFrom],120) as dtTime, sum(datediff(hh, stfTmFrom, stfTmTo)) as qtHours
          FROM staffTime
          where projId = $p ";
  if ( $r != 0 ) $sql .= " AND staffId = $r ";
  $sql .= " GROUP BY convert(varchar(10),[stfTmFrom],120)
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