<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = ''; };
if ( isset( $_GET['ip'] )) { $ip = $_GET['ip'];  } else { $ip = ''; };

if ( $t == 'packs') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT ipId, ipCode, ipDesig, ipSFam, sfam.sfamName, sfam.sfamIcon, pk.ipUnitId, pu.unitCode
          FROM impPacks pk
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pk.ipSFam
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(pk.ipUnitId,1)
          WHERE isnull(pk.isDeleted,0) = 0 and ipType = '$ip' 
          ORDER BY ipDesig";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"ipCode": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"ipDesig": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"ipSFam": "'.trim($rows_emps->fields[3]).'", ';
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[5]).'\"> </i> ';
    $output .= '"Icon": "'.$icon.'", ';
    $output .= '"sfamName": "'.trim($rows_emps->fields[4]).'", ';
    $output .= '"ipUnitId": "'.trim($rows_emps->fields[6]).'", ';
    $output .= '"ipUnit": "'.trim($rows_emps->fields[7]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
}  elseif ( $t == 'detail') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT  ipdId, ipdDesig, ipdQuant, sfam.sfamIcon, ipdFam, ipdCodeId, fam.famType, fam.famIcon
          FROM impPacksDetail pd
          INNER JOIN tblFamily fam on fam.famId = ipdFam 
          LEFT  JOIN rsProducts prod on prod.prodId = pd.ipdCodeId
          LEFT  JOIN tblSubFamily sfam on sfam.sfamId = (CASE ipdFam WHEN 2 THEN prod.prodTypeId ELSE pd.ipdCodeId END)
          WHERE ipId = $c
          ORDER BY ipdId";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"bkdId": "'.trim($rows_emps->fields[0]).'", ';
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[3]).'\"> </i> ';
    $output .= '"Icon": "'.$icon.'", ';
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    $output .= '"typeIcon": "'.$icon.'", ';
    $output .= '"famType": "'.trim($rows_emps->fields[6]).'", ';
    $output .= '"bkDesig": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"bkdQuant": "'.trim($rows_emps->fields[2]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
}  elseif ( $t == 'pack') {

  $sql = "SELECT bkId, bkCode, bkDesig, BomKit, isnull(bkType,1), sfam.sfamName, sfam.sfamIcon, (CASE BomKit WHEN 'b' THEN 'BOM' WHEN 'k' THEN 'KIT' ELSE '' END), pk.bkUnitId, pu.unitCode
          FROM packs pk
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pk.bkType
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(pk.bkUnitId,1)
          WHERE bkId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "bkId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "bkCode": "'.trim($rows_emps->fields[1]).'", ');
    print(' "bkDesig": "'.trim($rows_emps->fields[2]).'", ');
    print(' "BomKit": "'.trim($rows_emps->fields[3]).'", ');
    print(' "BomKitDesig": "'.trim($rows_emps->fields[7]).'", ');
    print(' "bkType": "'.trim($rows_emps->fields[4]).'", ');
    print(' "bkUnit": "'.trim($rows_emps->fields[9]).'", ');
    print(' "bkUnitId": "'.trim($rows_emps->fields[8]).'", ');
    print(' "sfamName": "'.trim($rows_emps->fields[5]).'" ');
    print('}');
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();
} 
if ( $t == 'r' ) {
    $sql = "SELECT bkdId, bkdCodeId, bkdDesig, bkdQuant
          FROM packsDetail pkd
          WHERE pkd.bkdId = $id ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->estReference = trim($rows_emps->fields[1]);
      $dataJson->estDesign = trim($rows_emps->fields[2]);
      $dataJson->estQuant = trim($rows_emps->fields[3]);
     $dataJson->exist = true;
    } 
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'p' ) {
    $sql = "SELECT bkdId, pd.prodTypeId, bkdCodeId, bkdDesig, bkdQuant, tpu.unitCode
          FROM packsDetail pkd
          INNER JOIN rsProducts pd on pd.prodId = bkdCodeId
          LEFT  JOIN tblProdUnit tpu on tpu.unitId = pd.prodUnitId
          WHERE pkd.bkdId = $id ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->estReference = trim($rows_emps->fields[1]);
      $dataJson->estCode = trim($rows_emps->fields[2]);
      $dataJson->estDesign = trim($rows_emps->fields[3]);
      $dataJson->estQuant = trim($rows_emps->fields[4]);
      $dataJson->unitLine = trim($rows_emps->fields[5]);
      $dataJson->exist = true;
    } 
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
?>