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
		$sql = "INSERT INTO rsConsumables (csmbArticle, csmbName, csmbTypeId, csmbUnitId, csmbWeight, qtBuy, qtsell, isDeleted, createStamp, createUser)
				VALUES ('".$post[csmbArticle]."', '".str_replace("'","''",$post[csmbName])."', '".$post[csmbTypeId]."', '".$post[csmbUnitId]."', '".$post[csmbWeight]."', '".$post[qtBuy]."', '".$post[qtSell]."', 0, getdate(), '$user' ) ";
		$db->debug=1;
		$rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $r != 0 ) {
        $sql = "UPDATE rsConsumables 
                    SET csmbArticle = '".$post[csmbArticle]."', 
                        csmbName    = '".str_replace("'","''",$post[csmbName])."', 
                        csmbTypeId  = '".$post[csmbTypeId]."', 
                        csmbUnitId  = '".$post[csmbUnitId]."', 
                        csmbWeight  = '".$post[csmbWeight]."',
                        qtBuy       = '".$post[qtBuy]."',
                        qtSell      = '".$post[qtSell]."',
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE csmbId = ".$r;
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r == 0 ) {
        $sql = "INSERT INTO rsConsumablesPrices ( csmbId, dtFrom, dtTo, amount, priceBS, isDeleted, createStamp, createUser )
                VALUES ('".$p."', '".$post[dtFrom]."', '".$post[dtTo]."', '".$post[amount]."', '".$post[priceBS]."', 0, getdate(), '$user' ) ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'fp' && $r != 0 ) {
        $sql = "UPDATE rsConsumables 
                    SET dtFrom  = '".$post[dtFrom]."', 
                        dtTo    = '".$post[dtTo]."', 
                        amount  = '".$post[amount]."', 
                        priceBS = '".$post[priceBS]."', 
                        modifyStamp = getdate(),
                        modifyUser  = '$user'
                WHERE csmbPrcId = ".$r;
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

