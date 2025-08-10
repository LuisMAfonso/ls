<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

if ( $t == 'consumables') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT csmbId, csmbArticle, csmbName, sfam.sfamName, pu.unitcode, sfam.sfamIcon
          FROM rsConsumables pd
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(csmbUnitId,1)
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pd.csmbTypeId
          WHERE isnull(pd.isDeleted,0) = 0
          ORDER BY csmbArticle";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"csmbArticle": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"csmbName": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"sfamName": "'.trim($rows_emps->fields[3]).'", ';
    $output .= '"unitcode": "'.trim($rows_emps->fields[4]).'", ';
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[5]).'\"> </i> ';
    $output .= '"Icon": "'.$icon.'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
}  elseif ( $t == 'prices') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT csmbPrcId, dtFrom, dtTo, amount, (CASE isnull(priceBS,0) WHEN 0 THEN 'Price for Buy Qt.' ELSE 'Price for Sell Qt.' END)
          FROM rsConsumablesPrices
          WHERE csmbId = $c
          order by dtfrom desc";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"csmbPrcId": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"dtFrom": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"dtTo": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"amount": "'.trim($rows_emps->fields[3]).'", ';
    $output .= '"priceBS": "'.trim($rows_emps->fields[4]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
}  elseif ( $t == 'consumable') {

  $sql = "SELECT csmbId, csmbArticle, csmbName, csmbTypeId, isnull(csmbUnitId,1), csmbWeight, sfam.sfamName, pu.unitcode, isnull(qtBuy,1), isnull(qtSell,1), sfam.sfamIcon
          FROM rsConsumables pd
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(csmbUnitId,1)
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pd.csmbTypeId
          WHERE csmbId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "csmbId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "csmbArticle": "'.trim($rows_emps->fields[1]).'", ');
    print(' "csmbName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "csmbTypeId": "'.trim($rows_emps->fields[3]).'", ');
    print(' "csmbUnitId": "'.trim($rows_emps->fields[4]).'", ');
    print(' "csmbWeight": "'.trim($rows_emps->fields[5]).'", ');
    print(' "csmbType": "'.trim($rows_emps->fields[6]).'", ');
    print(' "csmbUnit": "'.trim($rows_emps->fields[7]).'", ');
    print(' "qtBuy": "'.trim($rows_emps->fields[8]).'", ');
    print(' "qtSell": "'.trim($rows_emps->fields[9]).'" ');
    print('}');
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();
} elseif ( $t == 'price') {

  $sql = "SELECT csmbPrcId, dtFrom, dtTo, amount, isnull(priceBS,0) 
          FROM rsConsumablesPrices
          WHERE csmbPrcId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "prodPrcId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "dtFrom": "'.trim($rows_emps->fields[1]).'", ');
    print(' "dtTo": "'.trim($rows_emps->fields[2]).'", ');
    print(' "amount": "'.trim($rows_emps->fields[3]).'", ');
    print(' "priceBS": "'.trim($rows_emps->fields[4]).'" ');
    print('}');
    $rows_emps->MoveNext();
  } else {
    print('{');
    print(' "prodPrcId": "0", ');
    print(' "dtFrom": "'.date("Y-m-d").'", ');
    print(' "dtTo": "'.date("Y").'-12-31'.'", ');
    print(' "amount": "0", ');
    print(' "priceBS": "0" ');
    print('}');
  }
  $rows_emps->Close();
} 

?>