<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };
if ( isset( $_GET['lk'] )) { $lk = $_GET['lk'];  } else { $lk = ''; };
if ( isset( $_GET['eId'] )) { $eId = $_GET['eId'];  } else { $eId = '0'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['l'] )) { $l = $_GET['l'];  } else { $l = ''; };
if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = '0'; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };

if ( $t == 'estimations' ) {

  $sql = "SELECT estimateId, estH.estName, tes.estStatusName, convert(varchar(10),estH.createStamp,120), prj.projName, tps.prjStatusName, prj.projCity, prj.projId, isnull(estH.partsFactor,1)*100
          FROM estimateHeader estH
          INNER JOIN tblEstimateStatus tes on tes.estStatusId = estH.eststatus
          left JOIN projects prj on prj.projId = estH.estProjectId
          left JOIN tblProjectStatus tps on tps.prjStatusId = prj.projStatus
          WHERE isnull(estH.isDeleted,0) = 0";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "estId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "estName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "estStatus": "'.trim($rows_emps->fields[2]).'", ');
    print(' "estDate": "'.trim($rows_emps->fields[3]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[4]).'", ');
    print(' "projStatus": "'.trim($rows_emps->fields[5]).'", ');
    print(' "projCity": "'.trim($rows_emps->fields[6]).'", ');
    print(' "projId": "'.trim($rows_emps->fields[7]).'", ');
    print(' "margin": "'.(trim($rows_emps->fields[8])-100).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');  
}

if ( $t == 'estimate' ) {

  if ( $p != '' ) {
    $sql = "SELECT estimateId
            FROM estimateHeader
            WHERE estProjectId = $p ";
    $rows_emps = $db->Execute($sql);
    $eId = $rows_emps->fields[0];
  }

  $sql = "SELECT totId, cast(labour as decimal(18,2)), cast(parts as decimal(18,2)), cast(total as decimal(18,2))
          FROM (
              SELECT (CASE WHEN estD.estGroupLine is not null THEN estD.estimateDetId ELSE estD.estLineLink END) as TotId, sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE estD.estQuant*isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(1+estD.estLineVATtax)) as total
              FROM estimateDetails estD
              INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
              LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
              LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
              WHERE estD.estimateId = $eId and estD.estLineType not in ('h','k') and isnull(estD.isDeleted,0) = 0
              GROUP BY (CASE WHEN estD.estGroupLine is not null THEN estD.estimateDetId ELSE estD.estLineLink END)
              UNION ALL
              SELECT (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END) as TotId, sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(1+estD.estLineVATtax)) as total
              FROM estimateDetails estD
              INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
              LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
              LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
              WHERE estD.estimateId = $eId and estD.estLineType not in ('h','k') and (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END) is not null and isnull(estD.isDeleted,0) = 0
              GROUP BY (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END)
          ) a
            ORDER BY totId";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $tLineL = array();
  $tLineP = array();
  $tLineT = array();
  while(!$rows_emps->EOF) {
    $tLineL[trim($rows_emps->fields[0])] = trim($rows_emps->fields[1]);
    $tLineP[trim($rows_emps->fields[0])] = trim($rows_emps->fields[2]);
    $tLineT[trim($rows_emps->fields[0])] = trim($rows_emps->fields[3]);
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  $sql = "SELECT estD.estimateDetId, estD.estLineType, (CASE WHEN estd.estLineType in ('h','k') THEN estD.estGroupLine ELSE isnull(estD.estReference,'') END), (CASE estD.estLineType WHEN 'n' THEN isnull(estD.estNotes,'') ELSE estD.estDesign END), estD.estQuant, (CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estItemValue ELSE isnull(estD.LabourAmount,0) END) as labour, (CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, estD.estLineVATtax, (estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0))*(1+estD.estLineVATtax)), estHS.estLineBold, (CASE estD.estLineType WHEN 'n' THEN 'mdi-note-text-outline' ELSE isnull(fam.famIcon,'mdi-segment') END), isnull(estD.estGroupLine,eLine.estGroupLine), estd.estLineLink, (CASE WHEN estd.estLineType in ('h','k') THEN 1 ELSE 0 END) as toCalc, isnull(estd.estLineSLink,0), isnull(estD.estNotes,''), isnull(estD.estNotesCust,0), isnull(estD.unitLine,''), estd.estLineOrder, cast(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) as decimal(18,2)) as sTot, isnull(estD.costOnlyLine,0) as cLine, isnull(estD.prodSize,''), estd.labourAmount 
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          INNER JOIN estimateDetailsSort estHS on estHS.estLineItem = estD.estLineType
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateId = $eId  and isnull(estD.isDeleted,0) = 0 ";
  if ( $g == 1 ) $sql .= " and estLineBold = 1 ";
  $sql .= " ORDER BY  (CASE WHEN estD.estGroupLine is not null THEN estD.estGroupLine ELSE eLine.estGroupLine END), estd.estLineOrder ";
//  $sql .= " ORDER BY  (CASE WHEN estD.estGroupLine is not null THEN estD.estGroupLine ELSE eLine.estGroupLine END), (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END), estd.estLineOrder ";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "estimateDetId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "isPack": "'.trim($rows_emps->fields[14]).'", ');
    print(' "estReference": "'.trim($rows_emps->fields[2]).'", ');
    $design = '';
    if ( $rows_emps->fields[9] == '0' ) $design .= "&nbsp;&nbsp;&nbsp;";
    if ( $rows_emps->fields[9] == '1' && trim($rows_emps->fields[1]) == 'k' ) $design .= "&nbsp;&nbsp;&nbsp;";
    if ( $rows_emps->fields[9] == '0' && $rows_emps->fields[14] > 0 ) $design .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    $design .= trim($rows_emps->fields[3]);
    print(' "estDesign": "'.$design.'", ');
    print(' "estQuant": "'.trim($rows_emps->fields[4]).'", ');
    print(' "unitLine": "'.trim($rows_emps->fields[17]).'", ');
    if ( trim($rows_emps->fields[13]) == '0' ) print(' "estItemValueL": "'.trim($rows_emps->fields[5]).'", ');
    if ( trim($rows_emps->fields[13]) == '1' ) print(' "estItemValueL": "'.$tLineL[trim($rows_emps->fields[0])].'", ');
    if ( trim($rows_emps->fields[13]) == '0' ) print(' "estItemValueP": "'.trim($rows_emps->fields[6]).'", ');
    if ( trim($rows_emps->fields[13]) == '1' ) print(' "estItemValueP": "'.$tLineP[trim($rows_emps->fields[0])].'", ');
//    if ( trim($rows_emps->fields[13]) == '0' ) $totLP = $rows_emps->fields[5]+$rows_emps->fields[6];
    if ( trim($rows_emps->fields[13]) == '0' ) $totLP = trim($rows_emps->fields[19]);
    if ( trim($rows_emps->fields[13]) == '1' ) $totLP = $tLineL[trim($rows_emps->fields[0])]+$tLineP[trim($rows_emps->fields[0])];
    print(' "estLineValue": "'.$totLP.'", ');
    if ( trim($rows_emps->fields[13]) == '0' ) print(' "estLineTotal": "'.trim($rows_emps->fields[8]).'", ');
    if ( trim($rows_emps->fields[13]) == '1' ) print(' "estLineTotal": "'.$tLineT[trim($rows_emps->fields[0])].'", ');
    if ( $rows_emps->fields[9] == '1' && trim($rows_emps->fields[1]) == 'k' && $g == 1 ) {
      print(' "lineBold": "0", ');
    } else {
      print(' "lineBold": "'.trim($rows_emps->fields[9]).'", ');
    }
    print(' "groupLine": "'.trim($rows_emps->fields[11]).'", ');
    print(' "target": "'.trim($rows_emps->fields[11]).'", ');
    print(' "lineType": "'.trim($rows_emps->fields[1]).'", ');
    print(' "lineOrder": "'.trim($rows_emps->fields[18]).'", ');
    print(' "prodSize": "'.trim($rows_emps->fields[21]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[10]).'\"> </i> ';
    print(' "icon": "'.$icon.'", ');
    $icon = '';
    if (  trim($rows_emps->fields[20]) == '1' ) $icon = '<i class=\"mdi mdi-18px mdi-cash-plus\"> </i> ';
    print(' "costOnly": "'.$icon.'", ');

    $icon = '';
    if ( trim($rows_emps->fields[1]) != 'n' && trim($rows_emps->fields[15]) > '' ) {
      print(' "notes": "'.trim($rows_emps->fields[15]).'", ');
      if ( trim($rows_emps->fields[16]) == 1 )  print(' "notesCust": "Yes", ');
      if ( trim($rows_emps->fields[16]) == 0 )  print(' "notesCust": "No", ');
      $icon = '<i class=\"mdi mdi-18px mdi-message-bulleted\"> </i> ';
      print(' "iNote": "'.$icon.'" ');
    } else {
      print(' "notes": "", ');
      print(' "notesCust": "", ');
      print(' "iNote": "" ');
    }
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
if ( $t == 'estimateItems' ) {

  $sql = "SELECT famId, famName, famIcon, famType
          FROM tblFamily";
  $rows_emps = $db->Execute($sql);

  print('[');
  print('{');
  print(' "id": "h", ');
  print(' "value": "Group line", ');
  $icon = '<i class=\"mdi mdi-48px mdi-segment\"> </i> ';
  print(' "icon": "'.$icon.'" ');
  print('}');

  while(!$rows_emps->EOF) {

    print(',');
    print('{');
    print(' "id": "'.trim($rows_emps->fields[3]).'", ');
    print(' "value": "'.trim($rows_emps->fields[1]).'", ');
    $icon = '<i class=\"mdi mdi-48px '.trim($rows_emps->fields[2]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(',');
  print('{');
  print(' "id": "n", ');
  print(' "value": "Notes", ');
  $icon = '<i class=\"mdi mdi-48px mdi-note-text-outline\"> </i> ';
  print(' "icon": "'.$icon.'" ');
  print('}');

  print(']');

} 
if ( $t == 'ref' ) {
    $sql = "SELECT sf.sfamName, isnull(amount,0) as amount
            FROM tblSubFamily sf
            INNER JOIN tblFamily fam on fam.famId = sf.famId
            LEFT JOIN tblSubFamilyPrices sfp on sf.sfamId = sfp.sfamId  and getdate() between dtFrom and dtTo and prcTable = 1 and sfp.sfamId = sf.sfamId
            where fam.famType = '$l' and sf.sfamId = '$v' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->estItemValue = trim($rows_emps->fields[1]);
      $dataJson->estDesign = trim($rows_emps->fields[0]);
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = true;
    } else {
      $dataJson->estItemValue = 0;
      $dataJson->estDesign = '';
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'prod' ) {
    $sql = "SELECT prodName, cast((CASE pdp.priceBS WHEN 0 THEN pdp.amount*1.0/(pd.qtBuy*1.0/pd.qtSell) ELSE isnull(pdp.amount,0) END) as decimal(18,2)), tpu.unitCode, isnull(pdp.amount,0) as amount
            FROM rsProducts pd
            LEFT JOIN rsProductsPrices pdp on pdp.prodId = pd.prodId and getdate() between pdp.dtFrom and pdp.dtTo
            LEFT JOIN tblProdUnit tpu on tpu.unitId = pd.prodUnitId
            WHERE pd.prodId = '$v' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->estItemValue = trim($rows_emps->fields[1]);
      $dataJson->estDesign = str_replace('"','\"',trim($rows_emps->fields[0]));
      $dataJson->unitLine = trim($rows_emps->fields[2]);
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = true;
    } else {
      $dataJson->estItemValue = 0;
      $dataJson->estDesign = '';
      $dataJson->unitLine = '';
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'csmb' ) {
    $sql = "SELECT csmbName, cast((CASE pdp.priceBS WHEN 0 THEN pdp.amount*1.0/(pd.qtBuy*1.0/pd.qtSell) ELSE isnull(pdp.amount,0) END) as decimal(18,2)), tpu.unitCode, isnull(pdp.amount,0) as amount
            FROM rsConsumables pd
            LEFT JOIN rsConsumablesPrices pdp on pdp.csmbId = pd.csmbId and getdate() between pdp.dtFrom and pdp.dtTo
            LEFT JOIN tblProdUnit tpu on tpu.unitId = pd.csmbUnitId
            WHERE pd.csmbId = '$v' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->estItemValue = trim($rows_emps->fields[1]);
      $dataJson->estDesign = trim($rows_emps->fields[0]);
      $dataJson->unitLine = trim($rows_emps->fields[2]);
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = true;
    } else {
      $dataJson->estItemValue = 0;
      $dataJson->estDesign = '';
      $dataJson->unitLine = '';
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'pack' ) {
    $sql = "SELECT bkDesig, 0 as amount
            FROM packs pc
            WHERE pc.bkId = '$v' ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->estItemValue = trim($rows_emps->fields[1]);
      $dataJson->estDesign = trim($rows_emps->fields[0]);
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = true;
    } else {
      $dataJson->estItemValue = 0;
      $dataJson->estDesign = '';
      $dataJson->stLineVAT = 'ST';
      $dataJson->exist = false;
    }
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'r' ) {
    $sql = "SELECT estD.estimateDetId, estD.estLineType, estD.estReference, estD.estDesign, estD.estQuant, estD.estItemValue, estD.estLineVATtax, (estD.estQuant*estD.estItemValue*(1+estD.estLineVATtax)), estHS.estLineBold, isnull(fam.famIcon,'mdi mdi-segment'), isnull(estD.estGroupLine,eLine.estGroupLine)
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          INNER JOIN estimateDetailsSort estHS on estHS.estLineItem = estD.estLineType
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateDetId = $id  and isnull(estD.isDeleted,0) = 0
          ORDER BY isnull(estD.estGroupLine,eLine.estGroupLine), estHS.estLineOrder ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->estimateDetId = trim($rows_emps->fields[0]);
      $dataJson->estReference = trim($rows_emps->fields[2]);
      $dataJson->estDesign = trim($rows_emps->fields[3]);
      $dataJson->estQuant = trim($rows_emps->fields[4]);
      $dataJson->estItemValue = trim($rows_emps->fields[5]);
      $dataJson->stLineVAT = trim($rows_emps->fields[6]);
      $dataJson->groupLine = trim($rows_emps->fields[10]);
     $dataJson->exist = true;
    } 
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'p' ) {
    $sql = "SELECT estD.estimateDetId, estD.estLineType, estD.estReference, estD.estDesign, estD.estQuant, estD.estItemValue, vat.vatId, (estD.estQuant*estD.estItemValue*(1+estD.estLineVATtax)), estHS.estLineBold, isnull(fam.famIcon,'mdi mdi-segment'), isnull(estD.estGroupLine,eLine.estGroupLine), prod.prodTypeId, estD.prodSize, estD.unitLine, estD.withInstall, cast(estD.labourAmount as decimal(18,2))
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          INNER JOIN estimateDetailsSort estHS on estHS.estLineItem = estD.estLineType
          INNER JOIN rsProducts prod on prod.prodId = estD.estReference
          INNER JOIN tblVATtaxes vat on vat.vatTax = estD.estLineVATtax
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateDetId = $id  and isnull(estD.isDeleted,0) = 0
          ORDER BY isnull(estD.estGroupLine,eLine.estGroupLine), estHS.estLineOrder ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->estimateDetId = trim($rows_emps->fields[0]);
      $dataJson->estReference = trim($rows_emps->fields[11]);
      $dataJson->estDesign = trim($rows_emps->fields[3]);
      $dataJson->estQuant = trim($rows_emps->fields[4]);
      $dataJson->estItemValue = trim($rows_emps->fields[5]);
      $dataJson->stLineVAT = trim($rows_emps->fields[6]);
      $dataJson->groupLine = trim($rows_emps->fields[10]);
      $dataJson->estCode = trim($rows_emps->fields[2]);
      $dataJson->prodSize = trim($rows_emps->fields[12]);
      $dataJson->unitLine = trim($rows_emps->fields[13]);
      $dataJson->labourAmount = trim($rows_emps->fields[15]);
      if ( $rows_emps->fields[14] == 0 ) $dataJson->withInstall = false;
      if ( $rows_emps->fields[14] == 1 ) $dataJson->withInstall = true;
      $dataJson->exist = true;
    } 
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'c' ) {
    $sql = "SELECT estD.estimateDetId, estD.estLineType, estD.estReference, estD.estDesign, estD.estQuant, estD.estItemValue, vat.vatId, (estD.estQuant*estD.estItemValue*(1+estD.estLineVATtax)), estHS.estLineBold, isnull(fam.famIcon,'mdi mdi-segment'), isnull(estD.estGroupLine,eLine.estGroupLine), csmb.csmbTypeId, isnull(estd.costOnlyLine,0) as cLine, isnull(estD.estCostValue,0) as vCost
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          INNER JOIN estimateDetailsSort estHS on estHS.estLineItem = estD.estLineType
          INNER JOIN rsConsumables csmb on csmb.csmbId = estD.estReference
          INNER JOIN tblVATtaxes vat on vat.vatTax = estD.estLineVATtax
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateDetId = $id  and isnull(estD.isDeleted,0) = 0
          ORDER BY isnull(estD.estGroupLine,eLine.estGroupLine), estHS.estLineOrder ";
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $dataJson = '';
    if(!$rows_emps->EOF) {
      $dataJson->id = trim($rows_emps->fields[0]);
      $dataJson->estimateDetId = trim($rows_emps->fields[0]);
      $dataJson->estReference = trim($rows_emps->fields[11]);
      $dataJson->estDesign = trim($rows_emps->fields[3]);
      $dataJson->estQuant = trim($rows_emps->fields[4]);
      $dataJson->stLineVAT = trim($rows_emps->fields[6]);
      $dataJson->groupLine = trim($rows_emps->fields[10]);
      $dataJson->estCode = trim($rows_emps->fields[2]);
      if ( trim($rows_emps->fields[12]) == 0 ) {
        $dataJson->costOnlyLine = false;
        $dataJson->estItemValue = trim($rows_emps->fields[5]);
      } else { 
        $dataJson->costOnlyLine = true;
        $dataJson->estItemValue = trim($rows_emps->fields[13]);
      }
      $dataJson->exist = true;
    } 
    $myJSON = json_encode($dataJson);
    echo $myJSON;
}
if ( $t == 'totals' ) {

  $sql = "SELECT  sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE estD.estQuant*isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue ELSE 0 END) as parts, sum(estD.estQuant*(estD.estItemValue+isnull(estD.LabourAmount,0)) *(estD.estLineVATtax)) as totVAT, sum(estD.estQuant*(estD.estItemValue+isnull(estD.LabourAmount,0))*(1+estD.estLineVATtax)) as total,  sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as partsM, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0))*(estD.estLineVATtax)) as totVATm, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(1+estD.estLineVATtax)) as totalM
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateId = $eId  and isnull(estD.isDeleted,0) = 0 ";
  $rows_emps = $db->Execute($sql);

  print('[');

  print('{');
  print(' "id": "Labour", ');
  print(' "lineBold": "0", ');
  print(' "value": "'.trim($rows_emps->fields[0]).'", ');
  print(' "valueM": "'.trim($rows_emps->fields[0]).'" ');
  print('}');
  print(',');
  print('{');
  print(' "id": "Parts", ');
  print(' "lineBold": "0", ');
  print(' "value": "'.trim($rows_emps->fields[1]).'", ');
  print(' "valueM": "'.trim($rows_emps->fields[4]).'" ');
  print('}');
  print(',');
  print('{');
  print(' "id": "Sub-Total", ');
  print(' "lineBold": "1", ');
  print(' "value": "'.($rows_emps->fields[0]+$rows_emps->fields[1]).'", ');
  print(' "valueM": "'.($rows_emps->fields[0]+$rows_emps->fields[4]).'" ');
  print('}');
  print(',');
  print('{');
  print(' "id": "VAT", ');
  print(' "lineBold": "0", ');
  print(' "value": "'.trim($rows_emps->fields[2]).'", ');
  print(' "valueM": "'.trim($rows_emps->fields[5]).'" ');
  print('}');
  print(',');
  print('{');
  print(' "id": "TOTAL", ');
  print(' "lineBold": "1", ');
  print(' "value": "'.trim($rows_emps->fields[3]).'", ');
  print(' "valueM": "'.trim($rows_emps->fields[6]).'" ');
  print('}');

  $rows_emps->Close();

  print(']');

} 
if ( $t == 'estimation' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT estimateId, estName, estProjectId
              FROM estimateHeader estH
              WHERE isnull(estH.isDeleted,0) = 0 and estimateId = $r ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "estId": "'.trim($rows_emps->fields[0]).'", ');
      print(' "estName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "estProjectId": "'.trim($rows_emps->fields[2]).'" ');
      print('}');
  } else {
      print('{');
      print('}');
  }

} 
if ( $t == 'n' ) {

    
      $sql = "SELECT estNotes, isnull(estNotesCust,0)
              FROM estimateDetails 
              WHERE estimateDetId = $id ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "estNote": "'.trim($rows_emps->fields[0]).'", ');
      print(' "estNoteCust": '.trim($rows_emps->fields[1]).' ');
      print('}');

} 
if ( $t == 'mar' ) {

    
      $sql = "SELECT estimateId, isnull(partsFactor,0)*100-100
              FROM estimateHeader
              WHERE estimateId =  $id ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "id": "'.trim($rows_emps->fields[0]).'", ');
      print(' "margin": '.trim($rows_emps->fields[1]).' ');
      print('}');

} 
if ( $t == 'impLines' ) {

  $sql = "SELECT impId, estReference, estDesign, estQuant, estItemValue, labourGroup, (CASE withInstall WHEN 1 THEN isnull(psp.pspAmount,0) ELSE 0 END), withInstall,isnull(prod.prodArticle,'0'), (CASE isnull(tps.sizeId,0) WHEN 0 THEN 1 ELSE (CASE isnull(ps.sizeId,0) WHEN 0 THEN 2 ELSE 0 END) END) as existSize, (CASE isnull(prod.prodId,0) WHEN 0 THEN 1 ELSE 0 END) as prodError
          FROM estimateDetailsImport
          LEFT JOIN rsProducts prod on prod.prodArticle = estReference
          LEFT JOIN tblProdSize tps on tps.sizeCode = labourGroup
      LEFT JOIN rsProductsSizes ps  on ps.prodid = prod.prodId and ps.sizeId = tps.sizeId
          LEFT JOIN tblProdSizePrices psp on psp.sizeid = ps.sizeId and getdate() between psp.dtFrom and psp.dtTo
          where estimateId = $eId and estLineLink = $lk ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "estReference": "'.trim($rows_emps->fields[1]).'", ');
    print(' "estDesign": "'.trim($rows_emps->fields[2]).'", ');
    print(' "estQuant": "'.trim($rows_emps->fields[3]).'", ');
    print(' "estItemValue": "'.trim($rows_emps->fields[4]).'", ');
    print(' "labourGroup": "'.trim($rows_emps->fields[5]).'", ');
    print(' "labourAmount": "'.trim($rows_emps->fields[6]).'", ');
    print(' "withInstall": "'.trim($rows_emps->fields[7]).'", ');
    print(' "sizeError": "'.trim($rows_emps->fields[9]).'", ');
    print(' "prodError": "'.trim($rows_emps->fields[10]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');  
}
?>