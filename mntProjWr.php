<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {
		$sql = "INSERT INTO projects (projName, projAddress, projZipcode, projCity, projCountry, projCustomer, calColor, isDeleted, createStamp, createUser, projStatus)
				VALUES ('".$post[projName]."', '".$post[projAddress]."', '".$post[projZipcode]."', '".$post[projCity]."', '".$post[projCountry]."', '".$post[custId]."', '".$post[calColor]."', 0, getdate(), '$user', 1 ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE projects 
                    SET projName     = '".$post[projName]."', 
                        projAddress  = '".$post[projAddress]."', 
                        projZipcode  = '".$post[projZipcode]."', 
                        projCity     = '".$post[projCity]."', 
                        projCountry  = '".$post[projCountry]."', 
                        projCustomer = '".$post[custId]."', 
                        calColor     = '".$post[calColor]."', 
                        modifyStamp  = getdate(),
                        modifyUser   = '$user'
                WHERE projId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'ef' && $r != 0 ) {
        $sql = "UPDATE projects 
                    SET projName     = '".$post[projName]."', 
                        projLink     = '".$post[projLink]."', 
                        projAddress  = '".$post[projAddress]."', 
                        projZipcode  = '".$post[projZipcode]."', 
                        projCity     = '".$post[projCity]."', 
                        projCountry  = '".$post[projCountry]."', 
                        projCustomer = '".$post[custId]."', 
                        projFinalCust = '".$post[custfId]."', 
                        projDesign   = '".$post[projDesignId]."', 
                        projTypeSite = '".$post[projTypeSiteId]."', 
                        projPrioLevel = '".$post[projPrioLevelId]."', 
                        projStatus   = '".$post[projStatusId]."', 
                        projDesignLink = '".$post[projDesignLink]."', 
                        calColor     = '".$post[calColor]."', 
                        modifyStamp  = getdate(),
                        modifyUser   = '$user'
                WHERE projId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'df' && $r != 0 ) {

    $sql = "UPDATE projects 
                SET fvDsg        = '".$post[fvDesign]."', 
                    fvPrj        = '".$post[fvProjMan]."', 
                    fvStm        = '".$post[fvSiteMan]."', 
                    fvLink       = '".$post[fvLink]."', 
                    soDsg        = '".$post[soDesign]."', 
                    soPrj        = '".$post[soProjMan]."', 
                    soStm        = '".$post[soSiteMan]."', 
                    soLink       = '".$post[soLink]."', 
                    dlDsg        = '".$post[dlDesign]."', 
                    dlPrj        = '".$post[dlProjMan]."', 
                    dlStm        = '".$post[dlSiteMan]."', 
                    slLink       = '".$post[dlLink]."', 
                    paStatus     = '".$post[propAcc]."', 
                    paDate       = '".$post[pracDate]."', 
                    paLink       = '".$post[pracLink]."', 
                    modifyStamp  = getdate(),
                    modifyUser   = '$user'
            WHERE projId = ".$r;
    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}

if ( $t == 'del' ) {
        $sql = "UPDATE projects 
                SET isDeleted = 1, deleteStamp = getdate(), deleteUser = '$user'
                WHERE projId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
function getPost()
{
    if(!empty($_POST))
    {
        // when using application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request
        // NOTE: if this is the case and $_POST is empty, check the variables_order in php.ini! - it must contain the letter P
        return $_POST;
    }

    // when using application/json as the HTTP Content-Type in the request 
    $post = json_decode(file_get_contents('php://input'), true);
    if(json_last_error() == JSON_ERROR_NONE)
    {
        return $post;
    }

    return [];
}
?>

