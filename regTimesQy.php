<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };
if ( isset( $_GET['df'] )) { $df = $_GET['df'];  } else { $df = ''; };
if ( isset( $_GET['dt'] )) { $dt = $_GET['dt'];  } else { $dt = ''; };

if ( $t == 'proj' ) { 

  $sql = "SELECT projId, projName, projCity, cast(numHr as decimal(18,2)) as numHr, cast(hrValue as decimal(18,2)) as hrValue
          FROM (
            SELECT stm.projId, prj.projName, prj.projCity, sum((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0) as numHr, sum(((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0)*stp.hourRate) as hrValue
            FROM staffTime stm
            INNER JOIN projects prj on prj.projId = stm.projId
            INNER JOIN staff stf on stf.staffId = stm.StaffId
            LEFT  JOIN staffPrices stp on stp.staffId = stm.StaffId and stfTmFrom between stp.dtFrom and stp.dtTo
            INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
            WHERE convert(varchar(10),stm.stfTmFrom ,120) between '$df' and '$dt' and isnull(stm.isDeleted,0) = 0
            GROUP BY stm.projId, prj.projName, prj.projCity
          ) a     
          ORDER BY projName desc   ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "name": "'.trim($rows_emps->fields[1]).'", ');
    print(' "posCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'", ');
    print(' "tValue": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'staff' ) { 

  $sql = "SELECT staffId, staffName, posName, cast(numHr as decimal(18,2)) as numHr, cast(hrValue as decimal(18,2)) as hrValue
          FROM (
            SELECT stf.staffId, stf.staffName, cp.posName, sum((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0) as numHr, sum(((datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0)*stp.hourRate) as hrValue
            FROM staffTime stm
            INNER JOIN staff stf on stf.staffId = stm.StaffId
            LEFT  JOIN staffPrices stp on stp.staffId = stm.StaffId and stfTmFrom between stp.dtFrom and stp.dtTo
            INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
            WHERE convert(varchar(10),stm.stfTmFrom ,120) between '$df' and '$dt' and isnull(stm.isDeleted,0) = 0
            GROUP BY stf.staffId, stf.staffName, cp.posName
          ) a     
          ORDER BY staffName desc";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "name": "'.trim($rows_emps->fields[1]).'", ');
    print(' "posCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'", ');
    print(' "tValue": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'detproj' ) { 

  $sql = "SELECT stfTmId, stf.staffName, convert(varchar(10),stfTmFrom,120) as tmDay, (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, replace(workDone,char(10),'<br>')
          FROM staffTime stm
          INNER JOIN staff stf on stf.staffId = stm.StaffId
          INNER JOIN projects prj on prj.projId = stm.projId
          WHERE stm.projId = $r and isnull(stm.isDeleted,0) = 0 and convert(varchar(10),stm.stfTmFrom ,120) between '$df' and '$dt' and isnull(stm.isDeleted,0) = 0
          ORDER BY stfTmFrom desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "name": "'.trim($rows_emps->fields[1]).'", ');
    print(' "date": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'", ');
    print(' "workDone": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'detstaff' ) { 

  $sql = "SELECT stfTmId, prj.projName, convert(varchar(10),stfTmFrom,120) as tmDay, (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, replace(workDone,char(10),'<br>')
          FROM staffTime stm
          INNER JOIN staff stf on stf.staffId = stm.StaffId
          INNER JOIN projects prj on prj.projId = stm.projId
          WHERE stm.StaffId = $r and isnull(stm.isDeleted,0) = 0 and convert(varchar(10),stm.stfTmFrom ,120) between '$df' and '$dt' and isnull(stm.isDeleted,0) = 0
          ORDER BY stfTmFrom desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "name": "'.trim($rows_emps->fields[1]).'", ');
    print(' "date": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'", ');
    print(' "workDone": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
?>