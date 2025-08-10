<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'stf' ) {
    $sql = "SELECT stfTmId, convert(varchar(16),stfTmFrom,120) as stfFrom , convert(varchar(16),stfTmTo,120) as stfTmTo, stf.staffName, pj.projName, isnull(pj.calColor,'#9575CD')
           FROM staffTime stm
           INNER JOIN staff stf on stf.staffId = stm.StaffId
           INNER JOIN projects pj on pj.projId = stm.projId ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '{';
    $dataJson .= '   "data": [ ';

    $firstReg = 1;
    while(!$rows_emps->EOF) {

      if ( $firstReg == 0 ) $dataJson .= ',';
      if ( $firstReg == 1 ) $firstReg = 0;
      $dataJson .= '{';
      $dataJson .= ' "id": "'.trim($rows_emps->fields[0]).'", ';
      $dataJson .= ' "start_date": "'.trim($rows_emps->fields[1]).'", ';
      $dataJson .= ' "end_date": "'.trim($rows_emps->fields[2]).'", ';
      $dataJson .= ' "text": "'.trim($rows_emps->fields[3]).'", ';
      $dataJson .= ' "details": "'.trim($rows_emps->fields[4]).'", ';
      $dataJson .= ' "color": "'.trim($rows_emps->fields[5]).'" ';

      $dataJson .= '}';

      $rows_emps->MoveNext();
    }
    $rows_emps->Close();

    $dataJson .= '   ] ';
    $dataJson .= '}';
    echo $dataJson;
} 
?>