<?php 
include( '../include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

$user = $_SESSION['userId'];

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

} elseif ( $t == 'times' ) {

  $sql = "SELECT stfTmId, prj.projName, convert(varchar(16),stfTmFrom,120) as tmDay, convert(varchar(5),stfTmFrom,24) as tmFrom,  convert(varchar(5),stfTmTo,24) as TmTo,  convert(varchar(5),timeBreak,24) as TmBreak , (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, stm.projId
          FROM staffTime stm
          LEFT JOIN projects prj on prj.projId = stm.projId
          WHERE StaffId = $r
          ORDER BY stfTmFrom desc" ;
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

} elseif ( $t == 'expenses' ) {

  $sql = "SELECT expId, expDate, et.sFamName, expValue
          FROM expenses ex
          INNER JOIN tblSubFamily et on et.sFamId = expType 
          where isnull(ex.isDeleted,0) = 0 and createUser = '$user'
          ORDER by expDate desc";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "expDate": "'.trim($rows_emps->fields[1]).'", ');
    print(' "expTypeName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "expValue": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'exp' ) {
    $sql = "SELECT expId, expDate, expType, expProj, expValue, sFamName, prj.projName, expImage, expNotes
            FROM expenses ex
            INNER JOIN tblSubFamily et on et.sFamId = expType 
            LEFT  JOIN projects prj on prj.projId = expProj 
            where isnull(ex.isDeleted,0) = 0 and expId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->expDate = trim($rows_emps->fields[1]);
      $dataJson->expId = trim($rows_emps->fields[2]);
      $dataJson->expName = trim($rows_emps->fields[5]);
      $dataJson->projId = trim($rows_emps->fields[3]);
      $dataJson->projName = trim($rows_emps->fields[6]);
      $dataJson->expValue = trim($rows_emps->fields[4]);
      $dataJson->workDone = trim($rows_emps->fields[8]);
      $dataJson->avatar->src = "../images/imgUsers/expense/".trim($rows_emps->fields[7]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
} elseif ( $t == 'projs' ) {

  $sql = "SELECT prj.projId, projName, projCity, ps.prjStatusIcon, ps.prjStatusName
          FROM projects prj 
          INNER JOIN tblProjectStatus ps on ps.prjStatusId = isnull(projStatus,1)
          where isnull(prj.isDeleted,0) = 0
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
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[3]).'\"> </i> ';
    print(' "icon": "'.$icon.'", ');
    print(' "projStatus": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'prjtimes' ) {

  $sql = "SELECT stfTmId, stf.staffName, convert(varchar(10),stfTmFrom,120) as tmDay, convert(varchar(5),stfTmFrom,24) as tmFrom,  convert(varchar(5),stfTmTo,24) as TmTo,  convert(varchar(5),timeBreak,24) as TmBreak , (datediff(mi,stftmFrom, stfTMTo)-datediff(mi,0,timeBreak))/60.0 as numHr, stm.projId
          FROM staffTime stm
          INNER JOIN staff stf on stf.staffId = stm.StaffId
          WHERE stm.projId = $r ";
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

} elseif ( $t == 'workDone' ) {

  $sql = "SELECT worksId, workDone
          FROM staffWorks
          ORDER BY workDone";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "workDone": "'.trim($rows_emps->fields[1]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'surveys' ) {

  $sql = "SELECT survId, survDate, prj.projName 
          FROM projectsSurveys surv
          LEFT JOIN projects prj on prj.projId = survProj
          WHERE isnull(surv.isDeleted,0) = 0 AND surv.createUser = '$user'
          ORDER BY survDate desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "survDate": "'.trim($rows_emps->fields[1]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[2]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'survey' ) {
    $sql = "SELECT survId, survDate, prj.projName, sizeArea, locAddress, projType, street, territory, [site], stairs, access, equipment, existingSoil, newSoil, rubbishRemove, fiskar, toilets, duration, irrigation, waterConn, transpOld, pruning, moveTerritory, hourRestr, parkPaid, robot, lightCables, barrier, fence, survProj, notes
            FROM projectsSurveys surv
            LEFT JOIN projects prj on prj.projId = survProj
            WHERE isnull(surv.isDeleted,0) = 0 AND survId = '$id' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = new StdClass();
    if(!$rows_emps->EOF) {
      $dataJson->Id = trim($rows_emps->fields[0]);
      $dataJson->survDate = trim($rows_emps->fields[1]);
      $dataJson->projName = trim($rows_emps->fields[2]);
      $dataJson->sizeArea = trim($rows_emps->fields[3]);
      $dataJson->locAddress = trim($rows_emps->fields[4]);
      $dataJson->projType = trim($rows_emps->fields[5]);
      $dataJson->street = trim($rows_emps->fields[6]);
      $dataJson->territory = trim($rows_emps->fields[7]);
      $dataJson->site = trim($rows_emps->fields[8]);
      $dataJson->stairs = trim($rows_emps->fields[9]);
      $dataJson->access = trim($rows_emps->fields[10]);
      $dataJson->equipment = trim($rows_emps->fields[11]);
      $dataJson->existingSoil = trim($rows_emps->fields[12]);
      $dataJson->newSoil = trim($rows_emps->fields[13]);
      $dataJson->rubbishRemove = trim($rows_emps->fields[14]);
      $dataJson->fiskar = trim($rows_emps->fields[15]);
      $dataJson->toilets = trim($rows_emps->fields[16]);
      $dataJson->duration = trim($rows_emps->fields[17]);
      $dataJson->irrigation = trim($rows_emps->fields[18]);
      $dataJson->waterConn = trim($rows_emps->fields[19]);
      $dataJson->transpOld = trim($rows_emps->fields[20]);
      $dataJson->pruning = trim($rows_emps->fields[21]);
      $dataJson->moveTerritory = trim($rows_emps->fields[22]);
      $dataJson->hourRestr = trim($rows_emps->fields[23]);
      $dataJson->parkPaid = trim($rows_emps->fields[24]);
      $dataJson->robot = trim($rows_emps->fields[25]);
      $dataJson->lightCables = trim($rows_emps->fields[26]);
      $dataJson->barrier = trim($rows_emps->fields[27]);
      $dataJson->fence = trim($rows_emps->fields[28]);
      $dataJson->survProj = trim($rows_emps->fields[29]);
      $dataJson->notes = trim($rows_emps->fields[30]);
      $dataJson->exist = true;
    } else {
      $dataJson->Id = 0;
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
} 

?>