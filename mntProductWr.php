<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = 'nrr'; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['p'] )) { $p = $_GET['p'];  } else { $p = ''; };

//$data = json_decode(file_get_contents('php://input'), true);
$post = getPost();
//print_r($post);

$user = $_SESSION['userId'];

if ( $t == 'f' && $r == 0 ) {

    if ( !is_numeric($post[prodWeight]) ) $post[prodWeight] = 0;
    if ( !is_numeric($post[qtBuy])      ) $post[qtBuy] = 1;
    if ( !is_numeric($post[qtSell])     ) $post[qtSell] = 1;

		$sql = "INSERT INTO rsProducts (prodArticle, prodName, prodTypeId, prodUnitId, prodWeight, qtBuy, qtsell, isDeleted, createStamp, createUser)
				VALUES ('".$post[prodArticle]."', '".str_replace("'","''",$post[prodName])."', '".$post[prodTypeId]."', '".$post[prodUnitId]."', '".$post[prodWeight]."', '".$post[qtBuy]."', '".$post[qtSell]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {

    if ( !is_numeric($post[prodWeight]) ) $post[prodWeight] = 0;
    if ( !is_numeric($post[qtBuy])      ) $post[qtBuy] = 1;
    if ( !is_numeric($post[qtSell])     ) $post[qtSell] = 1;

        $sql = "UPDATE rsProducts 
                    SET prodArticle    = '".$post[prodArticle]."', 
                        prodName = '".str_replace("'","''",$post[prodName])."', 
                        prodTypeId = '".$post[prodTypeId]."', 
                        prodUnitId    = '".$post[prodUnitId]."', 
                        prodWeight = '".$post[prodWeight]."',
                        qtBuy      = '".$post[qtBuy]."',
                        qtSell     = '".$post[qtSell]."',
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE prodId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r == 0 ) {
        $sql = "INSERT INTO rsProductsPrices ( prodId, dtFrom, dtTo, amount, priceBS, pSize, isDeleted, createStamp, createUser )
                VALUES ('".$p."', '".$post[dtFrom]."', '".$post[dtTo]."', '".$post[amount]."', '".$post[priceBS]."', '".$post[pSize]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r != 0 ) {
        $sql = "UPDATE rsProductsPrices 
                    SET dtFrom  = '".$post[dtFrom]."', 
                        dtTo    = '".$post[dtTo]."', 
                        amount  = '".$post[amount]."', 
                        priceBS = '".$post[priceBS]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE prodPrcId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fs' && $r == 0 ) {
        $sql = "INSERT INTO rsProductsSizes ( prodId, sizeId, psWeight, psWidth, psHeight, psLength, psUnitId, isDeleted, createStamp, createUser )
                VALUES ('".$p."', '".$post[sizeId]."', '".$post[psWeight]."', '".$post[psWidth]."', '".$post[psHeight]."', '".$post[psLength]."', '".$post[psUnitId]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fs' && $r != 0 ) {
        $sql = "UPDATE rsProductsSizes 
                    SET sizeId   = '".$post[sizeId]."', 
                        psWeight = '".$post[psWeight]."', 
                        psWidth  = '".$post[psWidth]."', 
                        psHeight = '".$post[psHeight]."', 
                        psLength = '".$post[psLength]."', 
                        priceBS  = '".$post[psUnitId]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE psId = ".$r;
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

