<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

if ( $t == 'tools') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT toolId, toolName, sfam.sfamName, tt.toolTypeName, sfam.sfamIcon
          FROM rsTools tl
          INNER JOIN tblToolType tt on tt.toolTypeId = tl.toolTypeId
          LEFT JOIN tblSubFamily sfam on sfam.sfamId = tl.toolEquipmentTypeId
          WHERE isnull(tl.isDeleted,0) = 0
          ORDER BY toolName ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"toolId": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"toolName": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"sfamName": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"toolTypeName": "'.trim($rows_emps->fields[3]).'", ';
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[4]).'\"> </i> ';
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

  $sql = "SELECT toolPrcId, dtFrom, dtTo, amount
          FROM rsToolsPrices
          WHERE toolId = $c
          order by dtfrom desc";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"toolPrcId": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"dtFrom": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"dtTo": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"amount": "'.trim($rows_emps->fields[3]).'" ';
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
}  elseif ( $t == 'tool') {

  $sql = "SELECT toolId, toolName, toolSerialNumber, toolLabelCode, toolEquipmentTypeId, tl.toolTypeId, toolWeight, sfam.sfamName, tt.toolTypeName
          FROM rsTools tl
          INNER JOIN tblToolType tt on tt.toolTypeId = isnull(tl.toolTypeId,1)
          LEFT  JOIN tblSubFamily sfam on sfam.sfamId = tl.toolEquipmentTypeId
          WHERE toolId = $r ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);
  
  if (!$rows_emps->EOF) {
    print('{');
    print(' "toolId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "toolName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "toolSerialNumber": "'.trim($rows_emps->fields[2]).'", ');
    print(' "toolLabelCode": "'.trim($rows_emps->fields[3]).'", ');
    print(' "toolEquipmentTypeId": "'.trim($rows_emps->fields[4]).'", ');
    print(' "toolTypeId": "'.trim($rows_emps->fields[5]).'", ');
    print(' "toolWeight": "'.trim($rows_emps->fields[6]).'", ');
    print(' "toolEquipmentType": "'.trim($rows_emps->fields[7]).'", ');
    print(' "toolType": "'.trim($rows_emps->fields[8]).'" ');
    print('}');
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();
} elseif ( $t == 'price') {

  $sql = "SELECT prodPrcId, dtFrom, dtTo, amount, isnull(priceBS,0) 
          FROM rsProductsPrices
          WHERE prodPrcId = $r ";
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