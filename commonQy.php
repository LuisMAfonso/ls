<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };

$userID = $_SESSION['userId'];

if ( $t == 'ct' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT couCode, couName
  			FROM countries
  			ORDER BY couName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
} 
if ( $t == 'ty' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT cTypeId, cTypeName
			  FROM tblCustType
			  ORDER BY cTypeId";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'sty' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT sTypeId, sTypeName
			  FROM tblSuppType
			  ORDER BY sTypeId";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
} 

if ( $t == 'ba' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT bActId, bActName
			  FROM tblBusActivity
			  ORDER BY bActName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
} 
if ( $t == 'rt' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT staffId, staffName
  			FROM staff
  			WHERE isnull(isDeleted,0) = 0
			ORDER BY staffName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
} 
if ( $t == 'cp' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT posId, posName
  			FROM tblCompPosition
			ORDER BY posName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'family' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT famId, famName
			  FROM tblFamily fm 
			  ORDER BY famName";
//	$db->debug=1;
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'fam' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT sfamId, sfamName
			  FROM tblSubFamily sf
			  INNER JOIN tblFamily fm on fm.famId = sf.famid
			  where fm.famType = '$s' and isnull(sf.isDeleted,0) = 0
			  ORDER BY sfamName";
//	$db->debug=1;
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'sfam' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT sfamId, sfamName
			  FROM tblSubFamily sf
			  where sf.famId = '$s' and null(isDeleted,0) = 0
			  ORDER BY sfamName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'un' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT unitId, rtrim(unitDesig)+'  ('+rtrim(unitCode)+')'
  			FROM tblProdUnit
			ORDER BY unitDesig";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'unit' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT unitCode, rtrim(unitDesig)+'  ('+rtrim(unitCode)+')'
  			FROM tblProdUnit
			ORDER BY unitDesig";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'sizes' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT sizeId, rtrim(sizeDesig)+'  ('+rtrim(sizeCode)+')'
  			FROM tblProdSize 
			ORDER BY sizeDesig";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'size' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT tps.sizeCode, rtrim(sizeDesig)+'  ('+rtrim(sizeCode)+')'
  			FROM tblProdSize tps
  			INNER JOIN rsProductsSizes rps on rps.sizeId = tps.sizeId AND rps.prodId = '$s'
			ORDER BY sizeDesig";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'prod' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT prodId, prodName
			  FROM rsProducts
			  WHERE prodTypeId = '$s'
			  ORDER BY prodName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.str_replace('"','\"',trim($rows_emps->fields[1])).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'csmb' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT csmbId, csmbName
  			FROM rsConsumables
			  WHERE csmbTypeId = '$s'
			  ORDER BY csmbName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'prjs' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT projId, projName
			  FROM projects
			  WHERE isnull(isDeleted,0) = 0";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'exp' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT expTypeId, expTypeName
			FROM tblExpenseTypes
			WHERE isnull(isDeleted,0) = 0
			ORDER BY expTypeName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'pack' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT bkId, bkDesig
			  FROM packs
			  ORDER BY bkDesig";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'roles' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT roleId, roleName
			  FROM tblRoles
			  ORDER BY RoleName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'cnts' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT contId, contName
			  FROM contacts
			  ORDER BY contName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'sup' ) {

  	$c1 = '"';
  	$c2 = '\"';

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT suppId, replace(suppName,'$c1','$c2') as suppName
			  FROM suppliers
			  ORDER BY suppName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'cus' ) {

  	$c1 = '"';
  	$c2 = '\"';

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT custId, custName
			  FROM customers
			  ORDER BY custName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'tl' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT toolTypeId, toolTypeName
  			FROM tblToolType
			ORDER BY toolTypeName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'impFiles' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT impTypeCode, impTypeDesc
  			FROM dataImpTypes
			ORDER BY impTypeDesc";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'tnote' ) {

  	$c1 = '"';
  	$c2 = '\"';

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT notesTypeId, replace(notesTypeName,'$c1','$c2') as notesTypeName
			  FROM tblNotesTypes
			  ORDER BY notesTypeName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'prio' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT prjPrioId, prjPrioName
			FROM tblProjectPrio
			ORDER BY prjPriority";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'ptype' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT prjTypeId, prjTypeName
		  	FROM tblProjectType
		  	ORDER BY prjTypeName";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
if ( $t == 'pstatus' ) {

  	header("Content-type: application/json");
	$output = '';

	$sql = "SELECT prjStatusId, prjStatusName
		  	FROM tblProjectStatus
		  	ORDER BY prjStatusType";
	$rows_emps = $db->Execute($sql);

    $first1 = 1;
    $output .= '[';
	while(!$rows_emps->EOF){
	    if ( $first1 == 0 ) $output .= ', ';
	    if ( $first1 == 1 ) $first1 = 0;
	    $output .= '{';
	    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
	    $output .= '"value": "'.trim($rows_emps->fields[1]).'" ';
	    $output .= '}';
		$rows_emps->MoveNext();
	}
	$rows_emps->Close();
    $output .= ']';

    echo $output;
}
?>
