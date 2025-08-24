<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };
if ( isset( $_GET['df'] )) { $df = $_GET['df'];  } else { $df = ''; };
if ( isset( $_GET['dt'] )) { $dt = $_GET['dt'];  } else { $dt = ''; };

if ( $t == 'expenses' ) { 

  $sql = "SELECT survId, survDate, prj.projName , usr.userName
          FROM projectsSurveys surv
          INNER JOIN Users usr on usr.userId = surv.createUser
          LEFT JOIN projects prj on prj.projId = survProj
          WHERE isnull(surv.isDeleted,0) = 0 AND survDate between '$df' and '$dt'
          ORDER BY survDate desc   ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "survDate": "'.trim($rows_emps->fields[1]).'", ');
    print(' "createUser": "'.trim($rows_emps->fields[3]).'" ');
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