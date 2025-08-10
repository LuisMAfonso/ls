<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'projects' ) {

  $sql = "SELECT projId, projName, projCity, ps.prjStatusName, pp.prjPrioName, pt.prjTypeName, (CASE projDesign WHEN 1 THEN 'Yes' ELSE 'No' END) as projDesign, projStatus, projPrioLevel, projTypeSite, isnull(projDesign,0) as projDesignId, calColor
          FROM projects prj 
          INNER JOIN tblProjectPrio pp on pp.prjPrioId = isnull(projPrioLevel,1)
          INNER JOIN tblProjectType pt on pt.prjTypeId = isnull(projTypeSite,1)
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
    print(' "projId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "projName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "projCity": "'.trim($rows_emps->fields[2]).'", ');
    print(' "projStatus": "'.trim($rows_emps->fields[3]).'", ');
    print(' "projPrioLevel": "'.trim($rows_emps->fields[4]).'", ');
    print(' "projTypeSite": "'.trim($rows_emps->fields[5]).'", ');
    print(' "projDesign": "'.trim($rows_emps->fields[6]).'", ');
    print(' "calColor": "'.trim($rows_emps->fields[11]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'project' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT projId, projName, projAddress, projZipcode, projCity, ps.prjStatusName, pp.prjPrioName, pt.prjTypeName, (CASE projDesign WHEN 1 THEN 'Yes' ELSE 'No' END) as projDesign, projStatus, projPrioLevel, projTypeSite, isnull(projDesign,0) as projDesignId, isnull(projLat,0), isnull(projLng,0), isnull(projAvatar,'noImage.jpg'), prj.projCustomer, cus.custName, tct.cTypeName, tba.bActName, prj.projFinalCust, cusf.custName, projDesignLink, projLink, projCountry, calColor
          FROM projects prj 
          INNER JOIN tblProjectPrio pp on pp.prjPrioId = isnull(projPrioLevel,1)
          INNER JOIN tblProjectType pt on pt.prjTypeId = isnull(projTypeSite,1)
          INNER JOIN tblProjectStatus ps on ps.prjStatusId = isnull(projStatus,1)
          LEFT  JOIN customers cus on cus.custId = prj.projCustomer
          LEFT  JOIN tblCustType tct on tct.cTypeId = isnull(cus.custTypeId,1)
          LEFT  JOIN tblBusActivity tba on tba.bActId = isnull(cus.custBusActId,1)
          LEFT  JOIN customers cusf on cusf.custId = prj.projFinalCust
          where projId = $r ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "projId": "'.trim($rows_emps->fields[0]).'", ');
      print(' "projName": "'.trim($rows_emps->fields[1]).'", ');
      print(' "projAddress": "'.trim($rows_emps->fields[2]).'", ');
      print(' "projZipcode": "'.trim($rows_emps->fields[3]).'", ');
      print(' "projCity": "'.trim($rows_emps->fields[4]).'", ');
      print(' "projCountry": "'.trim($rows_emps->fields[24]).'", ');
      print(' "projStatus": "'.trim($rows_emps->fields[5]).'", ');
      print(' "projPrioLevel": "'.trim($rows_emps->fields[6]).'", ');
      print(' "projTypeSite": "'.trim($rows_emps->fields[7]).'", ');
      print(' "projDesign": "'.trim($rows_emps->fields[8]).'", ');
      print(' "projLat": "'.trim($rows_emps->fields[13]).'", ');
      print(' "projLng": "'.trim($rows_emps->fields[14]).'", ');
      print(' "projAvatar": "'.trim($rows_emps->fields[15]).'", ');
      print(' "projStatusId": "'.trim($rows_emps->fields[9]).'", ');
      print(' "projPrioLevelId": "'.trim($rows_emps->fields[10]).'", ');
      print(' "projTypeSiteId": "'.trim($rows_emps->fields[11]).'", ');
      print(' "projDesignId": "'.trim($rows_emps->fields[12]).'", ');
      print(' "custId": "'.trim($rows_emps->fields[16]).'", ');
      print(' "custName": "'.trim($rows_emps->fields[17]).'", ');
      print(' "custType": "'.trim($rows_emps->fields[18]).'", ');
      print(' "custBusiness": "'.trim($rows_emps->fields[19]).'", ');
      print(' "custfId": "'.trim($rows_emps->fields[20]).'", ');
      print(' "custfName": "'.trim($rows_emps->fields[21]).'", ');
      print(' "projDesignLink": "'.trim($rows_emps->fields[22]).'", ');
      print(' "projLink": "'.trim($rows_emps->fields[23]).'", ');
      print(' "calColor": "'.trim($rows_emps->fields[25]).'" ');
      print('}');
  } else {
      print('{');
      print(' "projLat": "0", ');
      print(' "projLng": "0", ');
      print('}');
  }

} elseif ( $t == 'projDate' ) {

  if ( $r != 0 ) {    
      $sql = "SELECT projId, replace(fvDsg,'1900-01-01',''), replace(fvPrj,'1900-01-01',''), replace(fvStm,'1900-01-01',''), fvLink, replace(soDsg,'1900-01-01',''), replace(soPrj,'1900-01-01',''), replace(soStm,'1900-01-01',''), soLink, replace(dlDsg,'1900-01-01',''), replace(dlPrj,'1900-01-01',''), replace(dlStm,'1900-01-01',''), slLink, paStatus, replace(paDate,'1900-01-01',''), paLink
              FROM projects
              WHERE projId  = $r ";
//      $db->debug=1;
      $rows_emps = $db->Execute($sql);

      print('{');
      print(' "projId": "'.trim($rows_emps->fields[0]).'", ');
      print(' "fvDesign": "'.trim($rows_emps->fields[1]).'", ');
      print(' "fvProjMan": "'.trim($rows_emps->fields[2]).'", ');
      print(' "fvSiteMan": "'.trim($rows_emps->fields[3]).'", ');
      print(' "fvLink": "'.trim($rows_emps->fields[4]).'", ');
      print(' "soDesign": "'.trim($rows_emps->fields[5]).'", ');
      print(' "soProjMan": "'.trim($rows_emps->fields[6]).'", ');
      print(' "soSiteMan": "'.trim($rows_emps->fields[7]).'", ');
      print(' "soLink": "'.trim($rows_emps->fields[8]).'", ');
      print(' "dlDesign": "'.trim($rows_emps->fields[9]).'", ');
      print(' "dlProjMan": "'.trim($rows_emps->fields[10]).'", ');
      print(' "dlSiteMan": "'.trim($rows_emps->fields[11]).'", ');
      print(' "dlLink": "'.trim($rows_emps->fields[12]).'", ');
      print(' "propAcc": "'.trim($rows_emps->fields[13]).'", ');
      print(' "pracDate": "'.trim($rows_emps->fields[14]).'", ');
      print(' "pracLink": "'.trim($rows_emps->fields[15]).'" ');
      print('}');
  } 

} elseif ( $t == 'estimates' ) {

  $sql = "SELECT estimateId, estName, tes.estStatusName, isnull(isLocked,0) as isLocked, estStatusIcon
          FROM estimateHeader eHr
          INNER JOIN tblEstimateStatus tes on tes.estStatusId = eHr.estStatus
          where estProjectId = $r and isDeleted = 0
          ORDER BY estimateId";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "estimateId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "estName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "estStatusName": "'.trim($rows_emps->fields[2]).'", ');
    if ( $rows_emps->fields[3] == 0 ) $icon = '<i class=\"mdi mdi-18px mdi-lock-open-variant-outline\"> </i> ';
    if ( $rows_emps->fields[3] != 0 ) $icon = '<i class=\"mdi mdi-18px mdi-lock-outline\"> </i> ';
    print(' "iconLock": "'.$icon.'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[4]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
   print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

}
?>