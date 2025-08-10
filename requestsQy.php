<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['rq'] )) { $rq = $_GET['rq'];  } else { $rq = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'requests' ) {

  $sql = "SELECT reqId, reqDate, rs.reqStatusName, sp.suppName, pj.projName, sf.sfamName, sf.sfamIcon, (Case rq.reqType WHEN 's' THEN 'Services' WHEN 'p' THEN 'Products' ELSE 'ND' END), rq.reqType
          FROM Request rq
          LEFT  JOIN projects pj on pj.projId = rq.projId
          INNER JOIN suppliers sp on sp.suppId = rq.suppId
          INNER JOIN tblSubFamily sf on sf.sfamId = rq.subfamId
          INNER JOIN requestsStatus rs on rs.reqStatusId = rq.reqStatus
          WHERE isnull(rq.isdeleted,0) = 0
          ORDER BY reqDate";
        //    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "reqDate": "'.trim($rows_emps->fields[1]).'", ');
    print(' "reqStatusName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "suppName": "'.trim($rows_emps->fields[3]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[4]).'", ');
    print(' "reqType": "'.trim($rows_emps->fields[7]).'", ');
    print(' "reqTypeId": "'.trim($rows_emps->fields[8]).'", ');
    print(' "sfamName": "'.trim($rows_emps->fields[5]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[6]).'\"> </i> ';
    print(' "sfamIcon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'request' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT reqId, reqDate, rs.reqStatusName, rq.suppId, rq.projId, rq.subfamId, rq.reqType, rq.reqNotes
              FROM Request rq
              INNER JOIN projects pj on pj.projId = rq.projId
              INNER JOIN suppliers sp on sp.suppId = rq.suppId
              INNER JOIN tblSubFamily sf on sf.sfamId = rq.subfamId
              INNER JOIN requestsStatus rs on rs.reqStatusId = rq.reqStatus
              WHERE isnull(rq.isdeleted,0) = 0 AND reqId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      $dataJson->reqId = trim($rows_emps->fields[0]);
      $dataJson->reqDate = trim($rows_emps->fields[1]);
      $dataJson->reqStatusName = trim($rows_emps->fields[2]);
      $dataJson->suppId = trim($rows_emps->fields[3]);
      $dataJson->projId = trim($rows_emps->fields[4]);
      $dataJson->subfamId = trim($rows_emps->fields[5]);
      $dataJson->reqType = trim($rows_emps->fields[6]);
      $dataJson->reqNote = trim($rows_emps->fields[7]);
  } else {
      $dataJson->reqId = 0;
      $dataJson->reqDate = date("Y-m-d");
      $dataJson->reqStatusName = "New";
      $dataJson->suppId = "";
      $dataJson->projId = "";
      $dataJson->reqType = "";
      $dataJson->reqNote = "";
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} elseif ( $t == 'statusLogs' ) {

  $sql = "SELECT rslId, rslDate, rs.reqStatusName, rsl.rslNotes
          FROM RequestStatusLog rsl
          INNER JOIN RequestsStatus rs on rs.reqStatusId = rsl.rslStatusId
          WHERE reqId = $r AND isnull(isdeleted,0) = 0 
          ORDER BY rslDate desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "rslId": "'.trim($rows_emps->fields[0]).'", ');
    $icon = '<i class=\"mdi mdi-18px mdi-message-bulleted\"> </i> ';
    if ( trim($rows_emps->fields[3]) == '' ) $icon = '';
    print(' "rslNote": "'.$icon.'", ');
    print(' "rslDate": "'.trim($rows_emps->fields[1]).'", ');
    print(' "rslStatusName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "rslNotes": "'.trim($rows_emps->fields[3]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'reqHeader' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT reqId, reqDate, rs.reqStatusName, sp.suppName, pj.projName, (Case rq.reqType WHEN 's' THEN 'Services' WHEN 'p' THEN 'Products' ELSE 'ND' END)
              FROM Request rq
              INNER JOIN projects pj on pj.projId = rq.projId
              INNER JOIN suppliers sp on sp.suppId = rq.suppId
              INNER JOIN tblSubFamily sf on sf.sfamId = rq.subfamId
              INNER JOIN requestsStatus rs on rs.reqStatusId = rq.reqStatus
              WHERE isnull(rq.isdeleted,0) = 0 AND reqId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      $dataJson->reqId = trim($rows_emps->fields[0]);
      $dataJson->reqDate = trim($rows_emps->fields[1]);
      $dataJson->reqStatus = trim($rows_emps->fields[2]);
      $dataJson->suppName = trim($rows_emps->fields[3]);
      $dataJson->projName = trim($rows_emps->fields[4]);
      $dataJson->reqType = trim($rows_emps->fields[5]);
  } else {
      $dataJson->reqId = 0;
      $dataJson->reqDate = "";
      $dataJson->reqStatus = "";
      $dataJson->suppName = "";
      $dataJson->projName = "";
      $dataJson->reqType = "";
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} elseif ( $t == 'reqDetail' ) {


//  $db->debug=1;
  if ( $rq == 's' ) {
    $sql = "SELECT reqsId, reqsCode, reqsName, reqsQuant, reqsPrice, reqsNotes, reqsSfamId, sf.sfamIcon
          FROM RequestServices rs
          LEFT JOIN tblSubFamily sf on sf.sfamId = rs.reqsSfamId
          WHERE rs.reqId = $r 
          ORDER BY reqsId ";
        //    $db->debug=1;
    $rows_emps = $db->Execute($sql);
  } else {
    $sql = "SELECT reqpId, reqpArticle, reqpName, reqpQuant, reqpPrice, reqpNotes, reqpSfamId, sf.sfamIcon
            FROM RequestProducts rp
            LEFT JOIN tblSubFamily sf on sf.sfamId = rp.reqpSfamId
            WHERE rp.reqId = $r 
            ORDER BY reqpId ";
          //    $db->debug=1;
    $rows_emps = $db->Execute($sql);
  }

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "reqCode": "'.trim($rows_emps->fields[1]).'", ');
    print(' "reqName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "reqQuant": "'.trim($rows_emps->fields[3]).'", ');
    print(' "reqPrice": "'.trim($rows_emps->fields[4]).'", ');
    print(' "reqNotes": "'.trim($rows_emps->fields[5]).'", ');
    print(' "reqSfamId": "'.trim($rows_emps->fields[6]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    print(' "sfamIcon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'reqDetailsS' ) {

  $sql = "SELECT reqsId, reqsCode, reqsName, reqsQuant, reqsPrice, reqsNotes, reqsSfamId, sf.sfamIcon
          FROM RequestServices rs
          LEFT JOIN tblSubFamily sf on sf.sfamId = rs.reqsSfamId
          WHERE rs.reqId = $r 
          ORDER BY reqsId ";
        //    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "reqsCode": "'.trim($rows_emps->fields[1]).'", ');
    print(' "reqsName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "reqsQuant": "'.trim($rows_emps->fields[3]).'", ');
    print(' "reqsPrice": "'.trim($rows_emps->fields[4]).'", ');
    print(' "reqsNotes": "'.trim($rows_emps->fields[5]).'", ');
    print(' "reqsSfamId": "'.trim($rows_emps->fields[6]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    print(' "sfamIcon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'reqDetailS' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT reqsId, reqsCode, reqsName, reqsQuant, reqsPrice, reqsNotes, reqsSfamId, sf.sfamIcon
              FROM RequestServices rs
              LEFT JOIN tblSubFamily sf on sf.sfamId = rs.reqsSfamId
              WHERE rs.reqId = $r ";
  //    $db->debug=1;
      $rows_emps = $db->Execute($sql);

      $dataJson->reqsId = trim($rows_emps->fields[0]);
      $dataJson->reqsCode = trim($rows_emps->fields[1]);
      $dataJson->reqsName = trim($rows_emps->fields[2]);
      $dataJson->reqsQuant = trim($rows_emps->fields[3]);
      $dataJson->reqsPrice = trim($rows_emps->fields[4]);
      $dataJson->reqsNotes = trim($rows_emps->fields[5]);
      $dataJson->reqsSfamId = trim($rows_emps->fields[6]);
  } else {
      $dataJson->reqId = 0;
      $dataJson->reqsCode = "";
      $dataJson->reqsName = "";
      $dataJson->reqsQuant = "";
      $dataJson->reqsPrice = "";
      $dataJson->reqsNotes = "";
      $dataJson->reqsSfamId = "";
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} elseif ( $t == 'reqDetailsP' ) {

  $sql = "SELECT reqpId, reqpArticle, reqpName, reqpQuant, reqpPrice, reqpNotes, reqpSfamId, sf.sfamIcon
          FROM RequestProducts rp
          LEFT JOIN tblSubFamily sf on sf.sfamId = rp.reqpSfamId
          WHERE rp.reqId = $r 
          ORDER BY reqpId ";
        //    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "reqpArticle": "'.trim($rows_emps->fields[1]).'", ');
    print(' "reqpName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "reqpQuant": "'.trim($rows_emps->fields[3]).'", ');
    print(' "reqpPrice": "'.trim($rows_emps->fields[4]).'", ');
    print(' "reqpNotes": "'.trim($rows_emps->fields[5]).'", ');
    print(' "reqpSfamId": "'.trim($rows_emps->fields[6]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    print(' "sfamIcon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'reqDetailP' ) {

  $dataJson = '';      

  if ( $r != 0 ) {    
      $sql = "SELECT reqpId, prodId, reqpArticle, reqpName, reqpQuant, reqpPrice, reqpNotes, reqpSfamId, sf.sfamIcon
              FROM RequestProducts rp
              LEFT JOIN tblSubFamily sf on sf.sfamId = rp.reqpSfamId
              WHERE rp.reqpId = $r ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      $dataJson->reqpId = trim($rows_emps->fields[0]);
      $dataJson->reqpArticle = trim($rows_emps->fields[2]);
      $dataJson->reqpName = trim($rows_emps->fields[3]);
      $dataJson->reqpQuant = trim($rows_emps->fields[4]);
      $dataJson->reqpPrice = trim($rows_emps->fields[5]);
      $dataJson->reqpNotes = trim($rows_emps->fields[6]);
      $dataJson->reqpSfamId = trim($rows_emps->fields[7]);
  } else {
      $dataJson->reqId = 0;
      $dataJson->reqpArticle = "";
      $dataJson->reqpName = "";
      $dataJson->reqpQuant = "";
      $dataJson->reqpPrice = "";
      $dataJson->reqpNotes = "";
      $dataJson->reqpSfamId = "";
  }
    $myJSON = json_encode($dataJson);
    echo $myJSON;

} 
?>