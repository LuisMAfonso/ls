<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };

if ( $t == 'products') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT prodId, prodArticle, prodName, sfam.sfamName, pu.unitcode, sfam.sfamIcon
          FROM rsProducts pd
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(prodUnitId,1)
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pd.prodTypeId
          WHERE isnull(pd.isDeleted,0) = 0
          ORDER BY prodArticle";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"prodArticle": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"prodName": "'.str_replace('"','\"',trim($rows_emps->fields[2])).'", ';
//    $output .= '"prodName": "'.trim($rows_emps->fields[2]).'", ';
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

  $sql = "SELECT prodPrcId, dtFrom, dtTo, amount, isnull(tps.sizeDesig,''), (CASE isnull(priceBS,0) WHEN 0 THEN 'Price for Buy Qt.' ELSE 'Price for Sell Qt.' END)
          FROM rsProductsPrices rpp
          LEFT JOIN tblProdSize tps on tps.sizeId = rpp.pSize
          WHERE prodId = $c
          order by dtfrom desc";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"prodPrcId": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"dtFrom": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"dtTo": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"amount": "'.trim($rows_emps->fields[3]).'", ';
    $output .= '"size": "'.trim($rows_emps->fields[4]).'", ';
    $output .= '"priceBS": "'.trim($rows_emps->fields[5]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
} elseif ( $t == 'sizes') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT psId, tps.sizeDesig, psWeight, psWidth, psHeight, psLength, tpu.unitCode, rps.sizeId
          FROM rsProductsSizes rps
          INNER JOIN tblProdSize tps on tps.sizeId = rps.sizeId
          LEFT  JOIN tblProdUnit tpu on tpu.unitId = psUnitId
          WHERE prodId = $c 
          ORDER BY tps.sizeCode";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"psId": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"sizeId": "'.trim($rows_emps->fields[7]).'", ';
    $output .= '"sizeCode": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"psWeight": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"psWidth": "'.trim($rows_emps->fields[3]).'", ';
    $output .= '"psHeight": "'.trim($rows_emps->fields[4]).'", ';
    $output .= '"psLength": "'.trim($rows_emps->fields[5]).'", ';
    $output .= '"unitCode": "'.trim($rows_emps->fields[6]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
} elseif ( $t == 'product') {

  $sql = "SELECT prodId, prodArticle, prodName, prodTypeId, isnull(prodUnitId,1), prodWeight, sfam.sfamName, pu.unitcode, isnull(qtBuy,1), isnull(qtSell,1), sfam.sfamIcon
          FROM rsProducts pd
          INNER JOIN tblProdUnit pu on pu.unitId = isnull(prodUnitId,1)
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = pd.prodTypeId
          WHERE prodId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "prodId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "prodArticle": "'.trim($rows_emps->fields[1]).'", ');
    print(' "prodName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "prodTypeId": "'.trim($rows_emps->fields[3]).'", ');
    print(' "prodUnitId": "'.trim($rows_emps->fields[4]).'", ');
    print(' "prodWeight": "'.trim($rows_emps->fields[5]).'", ');
    print(' "prodType": "'.trim($rows_emps->fields[6]).'", ');
    print(' "prodUnit": "'.trim($rows_emps->fields[7]).'", ');
    print(' "qtBuy": "'.trim($rows_emps->fields[8]).'", ');
    print(' "qtSell": "'.trim($rows_emps->fields[9]).'" ');
    print('}');
    $rows_emps->MoveNext();
  } else {
    print('{');
    print(' "prodId": "0", ');
    print(' "prodArticle": "", ');
    print(' "prodName": "", ');
    print(' "prodTypeId": "", ');
    print(' "prodUnitId": "", ');
    print(' "prodWeight": "0", ');
    print(' "prodType": "", ');
    print(' "prodUnit": "", ');
    print(' "qtBuy": "1", ');
    print(' "qtSell": "1" ');
    print('}');
  }
  $rows_emps->Close();
} elseif ( $t == 'price') {

  $sql = "SELECT prodPrcId, dtFrom, dtTo, amount, isnull(priceBS,0), pSize 
          FROM rsProductsPrices
          WHERE prodPrcId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "prodPrcId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "dtFrom": "'.trim($rows_emps->fields[1]).'", ');
    print(' "dtTo": "'.trim($rows_emps->fields[2]).'", ');
    print(' "pSize": "'.trim($rows_emps->fields[5]).'", ');
    print(' "amount": "'.trim($rows_emps->fields[3]).'", ');
    print(' "priceBS": "'.trim($rows_emps->fields[4]).'" ');
    print('}');
    $rows_emps->MoveNext();
  } else {
    print('{');
    print(' "prodPrcId": "0", ');
    print(' "dtFrom": "'.date("Y-m-d").'", ');
    print(' "dtTo": "'.date("Y").'-12-31'.'", ');
    print(' "pSize": "'.$s.'", ');
    print(' "amount": "0", ');
    print(' "priceBS": "0" ');
    print('}');
  }
  $rows_emps->Close();
} elseif ( $t == 'size') {

  $sql = "SELECT psId, sizeId, psWeight, psWidth, psHeight, psLength, psUnitId
          FROM rsProductsSizes rps
          WHERE psId = $r";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "psId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "sizeId": "'.trim($rows_emps->fields[1]).'", ');
    print(' "psWeight": "'.trim($rows_emps->fields[2]).'", ');
    print(' "psWidth": "'.trim($rows_emps->fields[3]).'", ');
    print(' "psHeight": "'.trim($rows_emps->fields[4]).'", ');
    print(' "psLength": "'.trim($rows_emps->fields[5]).'", ');
    print(' "psUnitId": "'.trim($rows_emps->fields[6]).'" ');
    print('}');
    $rows_emps->MoveNext();
  } else {
    print('{');
    print(' "psId": "0", ');
    print(' "sizeId": "", ');
    print(' "psWeight": "0", ');
    print(' "psWidth": "0", ');
    print(' "psHeight": "0", ');
    print(' "psLength": "0", ');
    print(' "psUnitId": "0" ');
    print('}');
  }
  $rows_emps->Close();
} 
 

?>