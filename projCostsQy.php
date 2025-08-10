<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };

if ( $t == 'projects' ) {

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
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'requests' ) {

  $sql = "SELECT reqId, suppName, reqDate, sfamName, reqStatusName,reqNotes, valor, fm.famIcon  
          FROM (      
            SELECT 'r'+cast(rq.reqId as char) as reqId, sp.suppName, reqDate, sf.sfamName, rs.reqStatusName, reqNotes, sum(reqsQuant*reqsPrice) as valor, 's' as sType
            FROM Request rq
            INNER JOIN RequestServices rqs on rqs.reqId = rq.reqId
            INNER JOIN requestsStatus rs on rs.reqStatusId = rq.reqStatus
            LEFT JOIN suppliers sp on sp.suppId = rq.suppId
            LEFT JOIN tblSubFamily sf on sf.sfamId = rq.subfamId
            where rq.projId = $p
            GROUP BY rq.reqId, sp.suppName, reqDate, sf.sfamName, rs.reqStatusName, reqNotes
            UNION ALL 
            SELECT 'r'+cast(rq.reqId as char) as reqId, sp.suppName, reqDate, sf.sfamName, rs.reqStatusName, reqNotes, sum(reqpQuant*reqpPrice) as valor, 'p' as sType
            FROM Request rq
            INNER JOIN RequestProducts rqp on rqp.reqId = rq.reqId
            INNER JOIN requestsStatus rs on rs.reqStatusId = rq.reqStatus
            LEFT JOIN suppliers sp on sp.suppId = rq.suppId
            LEFT JOIN tblSubFamily sf on sf.sfamId = rq.subfamId
            where rq.projId = $p
            GROUP BY rq.reqId, sp.suppName, reqDate, sf.sfamName, rs.reqStatusName, reqNotes
            UNION ALL 
            SELECT 'x'+cast(ex.expId as char) as reqId, rtrim(isnull(st.staffName,'('+us.username+')')), expDate, sf.sfamName, 'New' as reqStatusName, expNotes, (expValue) as valor, 'x' as sType
            FROM expenses ex
            LEFT JOIN staff st on st.staffId = ex.staffId
            LEFT JOIN users us on us.userid = ex.createUser
            LEFT JOIN tblSubFamily sf on sf.sfamId = ex.expType
            where ex.expProj = $p
          ) a
          INNER JOIN tblFamily fm on fm.famType = a.sType
          ORDER BY reqDate desc";
//      $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "reqId": "'.trim($rows_emps->fields[0]).'", ');
    print(' "suppName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "reqDate": "'.trim($rows_emps->fields[2]).'", ');
    print(' "sfamName": "'.trim($rows_emps->fields[3]).'", ');
    print(' "reqStatus": "'.trim($rows_emps->fields[4]).'", ');
    print(' "amount": "'.trim($rows_emps->fields[6]).'", ');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    print(' "icon": "'.$icon.'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'labour' ) {

  $sql = "SELECT staffId, staffName, posName, cast(numHr as decimal(18,2)) as numHr, cast(hrValue as decimal(18,2)) as hrValue
          FROM (
            SELECT stf.staffId, stf.staffName, cp.posName, sum(datediff(mi,stftmFrom, stfTMTo)/60.0) as numHr, sum(datediff(mi,stftmFrom, stfTMTo)/60.0*stp.hourRate) as hrValue
            FROM staffTime stm
            INNER JOIN staff stf on stf.staffId = stm.StaffId
            LEFT  JOIN staffPrices stp on stp.staffId = stm.StaffId and stfTmFrom between stp.dtFrom and stp.dtTo
            INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
            WHERE stm.projId = $p
            GROUP BY stf.staffId, stf.staffName, cp.posName
          ) a     
          ORDER BY staffName desc ";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "Id": "'.trim($rows_emps->fields[0]).'", ');
    print(' "staffName": "'.trim($rows_emps->fields[1]).'", ');
    print(' "posName": "'.trim($rows_emps->fields[2]).'", ');
    print(' "tTime": "'.trim($rows_emps->fields[3]).'", ');
    print(' "tValue": "'.trim($rows_emps->fields[4]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} elseif ( $t == 'totals' ) {

  $sql = "SELECT *, vEst-vReq as vProf
          FROM (
              SELECT fam.famId, fam.famName, sum(qt) as qt, sum(vReq) as vReq, sum(vAcc) as vAcc, sum(vDel) as vDel, sum(vEst) as vEst, fam.famIcon
              FROM (
                  SELECT tType, cast(qt as decimal(18,2)) as qt, cast( val as decimal(18,2)) as vReq, cast( val as decimal(18,2)) as vAcc, cast( val as decimal(18,2)) as vDel, 0 as vEst
                  FROM (
                      SELECT 'l' as tType, sum(datediff(mi,stftmFrom, stfTMTo)/60.0) as qt, sum(datediff(mi,stftmFrom, stfTMTo)/60.0*stp.hourRate) as val
                      FROM staffTime stm
                      INNER JOIN staff stf on stf.staffId = stm.StaffId
                      LEFT  JOIN staffPrices stp on stp.staffId = stm.StaffId and stfTmFrom between stp.dtFrom and stp.dtTo
                      INNER JOIN tblCompPosition cp on cp.posId = isnull(staffPosition,1)
                      WHERE stm.projId = $p
                  ) a
                  UNION ALL
                  SELECT tType, qt, vReq, vAcc, vDel, 0 as vEst
                  FROM (
                      SELECT 's' as tType, count(*) as qt, sum(reqsQuant*1.0*reqsPrice) as vReq, sum(CASE isnull(reqsAccepted,'1900-01-01') WHEN '1900-01-01' THEN 0 ELSE reqsQuant*1.0*reqsPrice END) as vAcc, sum(CASE isnull(reqsDelivered,'1900-01-01') WHEN '1900-01-01' THEN 0 ELSE reqsQuant*1.0*reqsPrice END) as vDel
                      FROM RequestServices rs
                      INNER JOIN Request rq on rq.reqId = rs.reqId
                      WHERE rq.projId = $p
                  ) a
                  UNION ALL 
                  SELECT tType, qt, vReq, vAcc, vDel, 0 as vEst
                  FROM (
                      SELECT 'p' as tType, count(*) as qt, sum(reqpQuant*1.0*reqpPrice) as vReq, sum(reqpQuant*1.0*reqpPrice) as vAcc, sum(CASE isnull(reqpDelivered,'1900-01-01') WHEN '1900-01-01' THEN 0 ELSE reqpQuant*1.0*reqpPrice END) as vDel
                      FROM RequestProducts rp
                      INNER JOIN Request rq on rq.reqId = rp.reqId
                      WHERE rq.projId = $p
                  ) a
                  UNION ALL 
                  SELECT totId, 0 as qt, 0 as vReq, 0 as vAcc, 0 as vDel, cast(total as decimal(18,2)) as vEst
                  FROM (
                      SELECT estD.estLineType as TotId, sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE estD.estQuant*isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(1+estD.estLineVATtax)) as total
                      FROM estimateDetails estD
                      INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
                      LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
                      LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
                      WHERE estH.estProjectId = $p and estD.estLineType not in ('h','k','n') and isnull(estD.isDeleted,0) = 0
                      GROUP BY estD.estLineType
                  ) a
                  UNION ALL 
                  SELECT 'x' as totId, count(*) as qt, sum(expValue) as vReq, 0 as vAcc, 0 as vDel, 0 as vEst
                  FROM expenses
                  WHERE expProj = $p and isnull(isDeleted,0) = 0
              ) b
              INNER JOIN tblFamily fam on fam.famType = b.tType
              GROUP BY fam.famName, fam.famId, fam.famIcon 
          ) c
          order by famId";
//    $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  print('[');
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    $icon = '<i class=\"mdi mdi-18px '.trim($rows_emps->fields[7]).'\"> </i> ';
    print(' "icon": "'.$icon.'", ');
    print(' "tType": "'.trim($rows_emps->fields[1]).'", ');
    print(' "qt": "'.trim($rows_emps->fields[2]).'", ');
    print(' "vReq": "'.trim($rows_emps->fields[3]).'", ');
    print(' "vAcc": "'.trim($rows_emps->fields[4]).'", ');
    print(' "vDel": "'.trim($rows_emps->fields[5]).'", ');
    print(' "vEst": "'.trim($rows_emps->fields[6]).'", ');
    print(' "vProf": "'.trim($rows_emps->fields[8]).'" ');
    print('}');

    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  print(']');

} 
?>