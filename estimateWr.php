<?php
include( 'include.php' );


if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['v'] )) { $v = $_GET['v'];  } else { $v = ''; };
if ( isset( $_GET['l'] )) { $l = $_GET['l'];  } else { $l = ''; };
if ( isset( $_GET['e'] )) { $e = $_GET['e'];  } else { $e = ''; };
if ( isset( $_GET['gl'] )) { $gl = $_GET['gl'];  } else { $gl = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = '0'; };
if ( isset( $_GET['r'] )) { $r = $_GET['r'];  } else { $r = ''; };
if ( isset( $_GET['s'] )) { $s = $_GET['s'];  } else { $s = ''; };
if ( isset( $_GET['lk'] )) { $lk = $_GET['lk'];  } else { $lk = ''; };
if ( isset( $_GET['eId'] )) { $eId = $_GET['eId'];  } else { $eId = '0'; };

$post = getPost();
//print_r($post);
$user = $_SESSION['userId'];
$when = date("Y-m-d H:i:s");

$sql = "SELECT (CASE estLinetype WHEN 'h' THEN estimateDetID ELSE estLineLink END), (CASE estLineType WHEN 'k' THEN estimateDetId ELSE estLineSLink END) as estLineSLink, estLineType, estGroupLine, estLineOrder
          FROM estimateDetails
          where estimateDetId = ".$gl; 
//$db->debug=1;
$rows_emps = $db->Execute($sql);

$link = $rows_emps->fields[0];
$slink = $rows_emps->fields[1];
$destT = $rows_emps->fields[2];
$destGL = $rows_emps->fields[3];
$destLine = $rows_emps->fields[4];
if ( $destT == 'h' || $destT == 'k' ) $destLine += 1; 

if ( $s != '' ) {
    $sql = "SELECT estLinetype, estLineLink, estGroupLine, estLineOrder
              FROM estimateDetails
              where estimateDetId = ".$s; 
    $db->debug=1;
    $rows_emps = $db->Execute($sql);  

    $type = $rows_emps->fields[0];
    $oldLink = $rows_emps->fields[1];      
    $oldGL = $rows_emps->fields[2]; 
    $oldLine = $rows_emps->fields[3]; 
}

if ( $t == 'mvline' && $type == 'h' && $destT == 'h' ) {
    $sql = "UPDATE estimateDetails
            SET estGroupLine = '$destGL' 
          WHERE estimateDetId = $s ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    $sql = "UPDATE estimateDetails
            SET estGroupLine = '$oldGL' 
          WHERE estimateDetId = $gl ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);

}
if ( $t == 'mvline' && $type != 'h' ) {

    $wToAdd = 0;
    if ( $type == 'k' ) {
        $sql = "SELECT count(*) as qt
                FROM estimateDetails
                WHERE estLineSLink = $s ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

        $wToAdd = $rows_emps->fields[0];
    }

    if ( $link != $oldLink ) {
        $sql = "UPDATE estimateDetails 
                SET estLineOrder = est1.estLineOrder+1+$wToAdd
                FROM estimateDetails est1
                WHERE estlineLink = $link and estLineOrder >= $destLine ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        if ( $destLine < $oldLine ) {
            $sql = "UPDATE estimateDetails 
                    SET estLineOrder = est1.estLineOrder+1+$wToAdd
                    FROM estimateDetails est1
                    WHERE estlineLink = $link and estLineOrder >= $destLine and estLineOrder < $oldLine ";
            $db->debug=1;
            $rows_emps = $db->Execute($sql);
        } else {
            $sql = "UPDATE estimateDetails 
                    SET estLineOrder = est1.estLineOrder+$wToAdd
                    FROM estimateDetails est1
                    WHERE estlineLink = $link and estLineOrder > $destLine ";
            $db->debug=1;
            $rows_emps = $db->Execute($sql);

            $sql = "UPDATE estimateDetails
                    SET  estLineOrder =  '$oldLine'
                    WHERE estimateDetId = $gl ";
            $db->debug=1;
            $rows_emps = $db->Execute($sql);            
        }
    }

    $sql = "UPDATE estimateDetails
            SET estLineLink = '$link',
                estLineSLink = '$slink',
                estLineOrder =  '$destLine'
            WHERE estimateDetId = $s ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);

    if ( $type == 'k' ) {
        $sql = "UPDATE estimateDetails
              SET estLineLink = '$link',
              estLineOrder = newLineOrder
              FROM estimateDetails est
              INNEr JOIN (SELECT estimateDetId, estLineLink, estLineSlink, $destLine+row_number() OVER (ORDER BY estLineOrder) as newLineOrder
                          FROM estimateDetails
                          WHERE estLineLink = $oldLink and estLineSLink = $s ) a on a.estimateDetId = est.estimateDetId ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l != 'h' && $destLine > 0 ) {
    $wToAdd = 0;
    if ( $l == 'k' ) {
        $sql = "SELECT count(*)
              FROM packsDetail pd
              WHERE pd.bkid = '".$post[estReference]."' ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);

        $wToAdd = $rows_emps->fields[0];

    }
    $sql = "UPDATE estimateDetails 
            SET estLineOrder = est1.estLineOrder+1+$wToAdd
            FROM estimateDetails est1
            WHERE estlineLink = $link and estLineOrder >= $destLine ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $l == 'h' ) {

    if ( $post[id] == 0 ) {
        $sql = "SELECT max(estGroupLine)
                FROM estimateDetails
                WHERE estimateId = $e and isnull(isDeleted,0) = 0 ";
        $rows_emps = $db->Execute($sql);
        $groupLine = $rows_emps->fields[0]+1;


        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estGroupLine, estDesign, estLineOrder, createStamp, createUser )
                VALUES ('".$e."', 'h', '".$groupLine."', '".$post[estDesign]."', 1, getdate(), '".$user."' ) ";		
//        $db->debug=1;
		$rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estDesign = '".str_replace("'","''",$post[estDesign])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 'l' ) {

    if ( $post[id] == 0 ) {
        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineOrder, createStamp, createUser )
                VALUES ('".$e."', 'l', '".$link."', '".$slink."', '".$post[estReference]."', '".$post[estDesign]."', '".$post[estQuant]."', '".str_replace(',','.',$post[estItemValue])."', '".$post[stLineVAT]."', '".$vatTax."', '".$destLine."', getdate(), '".$user."' ) ";        
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                    estItemValue = '".str_replace(',','.',$post[estItemValue])."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 's' ) {

    if ( $post[id] == 0 ) {
        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineOrder, unitLine, createStamp, createUser )
                VALUES ('".$e."', 's', '".$link."', '".$slink."', '".$post[estReference]."', '".$post[estDesign]."', '".$post[estQuant]."', '".str_replace(',','.',$post[estItemValue])."', '".$post[stLineVAT]."', '".$vatTax."', '".$destLine."', '".$post[unitLine]."', getdate(), '".$user."' ) ";        
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                estItemValue = '".str_replace(',','.',$post[estItemValue])."',
                unitLine     = '".str_replace(',','.',$post[unitLine])."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 'e' ) {

        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

    if ( $post[id] == 0 ) {
        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineOrder, createStamp, createUser )
                VALUES ('".$e."', 'e', '".$link."', '".$slink."', '".$post[estReference]."', '".$post[estDesign]."', '".$post[estQuant]."', '".str_replace(',','.',$post[estItemValue])."', '".$post[stLineVAT]."', '".$vatTax."', '".$destLine."', getdate(), '".$user."' ) ";        
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                estItemValue = '".str_replace(',','.',$post[estItemValue])."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 'p' ) {

        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

    if ( $post[id] == 0 ) {
        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, unitLine, estLineOrder, createStamp, createUser, prodSize, withInstall, labourAmount )
                VALUES ('".$e."', 'p', '".$link."', '".$slink."', '".$post[estCode]."', '".str_replace("'","''",$post[estDesign])."', '".$post[estQuant]."', '".str_replace(',','.',$post[estItemValue])."', '".$post[stLineVAT]."', '".$vatTax."', '".$post[unitLine]."', '".$destLine."', getdate(), '".$user."', '".$post[prodSize]."', '".$post[withInstall]."', '".$post[labourAmount]."' ) ";        
 //       $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                prodsize = '".str_replace(',','.',$post[prodSize])."',
                estItemValue = '".str_replace(',','.',$post[estItemValue])."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."',
                withInstall    = '".str_replace("'","''",$post[withInstall])."',
                labourAmount    = '".str_replace(",",".",$post[labourAmount])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 'c' ) {

        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

    $sellValue = $costValue = 0;
    if ( $post[costOnlyLine] == 0 ) $sellValue = str_replace(',','.',$post[estItemValue]);
    if ( $post[costOnlyLine] == 1 ) $costValue = str_replace(',','.',$post[estItemValue]);

    if ( $post[id] == 0 ) {
        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, unitLine, estLineOrder, estCostValue, costOnlyLine, createStamp, createUser )
                VALUES ('".$e."', 'c', '".$link."', '".$slink."', '".$post[estCode]."', '".str_replace("'","''",$post[estDesign])."', '".$post[estQuant]."', '".$sellValue."', '".$post[stLineVAT]."', '".$vatTax."', '".$post[unitLine]."', '".$destLine."', '".$costValue."', 1, getdate(), '".$user."' ) ";        
 //       $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                estItemValue = '".$sellValue."',
                estCostValue = '".$costValue."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."',
                costOnlyLine = '".$post[costOnlyLine]."'
                WHERE estimateDetId = ".$post[id];
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}if ( $t == 'f' && $l == 'k' ) {

        $sql = "DECLARE @estId int;
                DECLARE @pack int;
                DECLARE @lineLink int;
                DECLARE @groupLine int;
                DECLARE @groupLineDet int;
                DECLARE @create datetime;

                SET @estId = $e;
                SET @pack = $post[estReference];
                SET @lineLink = $link;

                SELECT @groupLine = max(isnull(estGroupSLine,0)) FROM estimateDetails WHERE estimateId = @estId; 
                SET @groupLine = @groupLine+1; 

                SET @create = convert(varchar(19),getdate(),120); 

                INSERT INTO estimateDetails (estimateId, estLineType, estGroupLine, estLineLink, estGroupSLine, estLineStatus, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineTotal, estLineOrder, createStamp, createUser) 
                SELECT @estId, famType as estLineType, null as estGroupLine, @lineLink as lineLink, @groupLine as estGroupSLine, 0, bkId as estReference, bkDesig as estDesig, bkdQuant as estQuant, amount, 'ST', vattax, stot, '$destLine', @create, 'admin' 
                FROM ( 
                        SELECT fam.famType, bkId, bkDesig, 0 as bkdQuant, (CASE BomKit WHEN 'b' THEN 0 ELSE 0 END) as amount, vatTax, 0 as stot, sfam.sfamIcon 
                        FROM packs pk 
                        LEFT JOIN tblSubFamily sfam on sfam.sfamId = pk.bkType 
                        INNER JOIN tblVATtaxes vat on vat.vatId = 'ST' 
                        INNER JOIN tblFamily fam on fam.famId = sfam.famId WHERE bkId = @pack 
                ) a 
                INNER JOIN estimateDetailsSort ets on ets.estLineItem = a.famType 
                order by ets.estLineOrder 

                SELECT @groupLineDet = estimateDetId FROM estimateDetails WHERE estLineType = 'k' and createStamp = @create;

                INSERT INTO estimateDetails (estimateId, estLineType, estGroupLine, estLineLink, estLineSLink, estLineStatus, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineTotal, estLineOrder, createStamp, createUser) 
                SELECT @estId, famType as estLineType, null as estLineGroup,  @lineLink, @groupLineDet as lineSLink, 0, bkId as estReference, bkDesig as estDesig, bkdQuant as estQuant, amount, 'ST', vattax, stot, $destLine+row_number() OVER (order by ets.estLineOrder), @create, 'admin' 
                FROM ( 
                        SELECT fam.famType, bkdCodeId as bkId, bkdDesig as bkDesig, bkdQuant, isnull(amount,0) as amount, vatTax, (bkdQuant*isnull(amount,0)) as stot, sfam.sfamIcon 
                        FROM packsDetail pd 
                        INNER JOIN tblFamily fam on fam.famId = bkdFam 
                        INNER JOIN estimateDetailsSort es on es.estLineItem = fam.famType 
                        INNER JOIN tblVATtaxes vat on vat.vatId = 'ST' 
                        LEFT JOIN tblSubFamily sfam on sfam.sfamId = pd.bkdCodeId 
                        LEFT JOIN tblSubFamilyPrices sfp on sfam.sfamId = sfp.sfamId and getdate() between dtFrom and dtTo and prcTable = 1 and sfp.sfamId = sfam.sfamId 
                        WHERE bkId = @pack and fam.famtype <> 'p' 
                        UNION ALL 
                        SELECT fam.famType, bkdCodeId as bkId, bkdDesig as bkDesig, bkdQuant, isnull(amount,0) as amount, vatTax, (bkdQuant*isnull(amount,0)) as stot, sfam.sfamIcon 
                        FROM packsDetail pd 
                        INNER JOIN tblFamily fam on fam.famId = bkdFam 
                        INNER JOIN estimateDetailsSort es on es.estLineItem = fam.famType 
                        INNER JOIN tblVATtaxes vat on vat.vatId = 'ST' 
                        LEFT JOIN rsProducts prod on prod.prodId = pd.bkdCodeId 
                        LEFT JOIN tblSubFamily sfam on sfam.sfamId = prod.prodTypeId 
                        LEFT JOIN rsProductsPrices pdp on pdp.prodId = prod.prodId and getdate() between pdp.dtFrom and pdp.dtTo 
                        WHERE bkId = @pack and fam.famtype = 'p' 
                ) a 
                INNER JOIN estimateDetailsSort ets on ets.estLineItem = a.famType 
                order by ets.estLineOrder ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'f' && $l == 'n' ) {

    if ( $post[id] == 0 ) {

        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estNotes, estLineOrder, createStamp, createUser )
                VALUES ('".$e."', 'n', '".$link."', '".$slink."', '', '', '0', '".$post[estNote]."', '".$destLine."', getdate(), '".$user."' ) ";        
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estNote = '".$post[estNote]."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'f' && $l == 't' ) {

        $sql = "SELECT vatTax
                FROM tblVATtaxes
                WHERE getdate() between vatFrom and vatTo and vatId = '".$post[stLineVAT]."'";
        $rows_emps = $db->Execute($sql);
        $vatTax = $rows_emps->fields[0];

    if ( $post[id] == 0 ) {
        $sql = "INSERT INTO estimateDetails ( estimateId, estLineType, estLineLink, estLineSLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, estLineOrder, createStamp, createUser )
                VALUES ('".$e."', 't', '".$link."', '".$slink."', '".$post[estReference]."', '".$post[estDesign]."', '".$post[estQuant]."', '".str_replace(',','.',$post[estItemValue])."', '".$post[stLineVAT]."', '".$vatTax."', '".$destLine."', getdate(), '".$user."' ) ";        
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    } else {
        $sql = "UPDATE estimateDetails 
                SET estQuant = '".$post[estQuant]."',
                estItemValue = '".str_replace(',','.',$post[estItemValue])."',
                estDesign    = '".str_replace("'","''",$post[estDesign])."'
                WHERE estimateDetId = ".$post[id];
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
    }
}
if ( $t == 'dl' && $l == 'h' ) {

        $sql = "UPDATE estimateDetails
                SET isDeleted = 1, deleteStamp = '$when', deleteUser = '$user'
                WHERE estimateDetId = $id or estLineLink = $id";
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'dl' && $l == 'k' ) {

        $sql = "UPDATE estimateDetails
                SET isDeleted = 1, deleteStamp = '$when', deleteUser = '$user'
                WHERE estimateDetId = $id or estLineSLink = $id";
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'dl' && ($l=='l' || $l=='e' || $l=='p' || $l=='c' || $l=='s' || $l=='n' || $l=='t') ) {

        $sql = "UPDATE estimateDetails
                SET isDeleted = 1, deleteStamp = '$when', deleteUser = '$user'
                WHERE estimateDetId = $id ";
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}

if ( $t == 'fe' && $r == 0 ) {
    $sql = "INSERT INTO estimateHeader (estName, estProjectId, estStatus, isDeleted, createStamp, createUser)
                    VALUES ('".$post[estName]."', '".$post[estProjectId]."', 1, 0, getdate(), '$user' ) ";
    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}
if ( $t == 'fe' && $r != 0 ) {
    $sql = "UPDATE estimateHeader 
                SET estName    = '".$post[estName]."', 
                    estProjectId = '".$post[estProjectId]."', 
                    modifyStamp = getdate(),
                    modifyUser  = '$user'
            WHERE estimateId = ".$r;
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}

if ( $t == 'del' ) {
    $sql = "UPDATE estimateHeader 
                SET isDeleted = 1, 
                    deleteStamp = getdate(),
                    deleteUser  = '$user'
            WHERE estimateId = ".$r;
//    $db->debug=1;
    $rows_emps = $db->Execute($sql);
}

if ( $t == 'n' ) {

        $noteCust = 0;
        if ( $post[estNoteCust] == 1 ) $noteCust = 1;

        $sql = "UPDATE estimateDetails
                SET estNotes = '".str_replace("'","''",$post[estNote])."',
                    estNotesCust = ".$noteCust."
                WHERE estimateDetId = $id ";
//        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'mar' ) {

        $wFactor = (100+str_replace(',','.',$post[margin]))/100;

        $sql = "UPDATE estimateHeader
                SET partsFactor = '".$wFactor."'
                WHERE estimateId = $id ";
        $db->debug=1;
        $rows_emps = $db->Execute($sql);
}
if ( $t == 'impf' ) {

    $sql = "INSERT INTO estimateDetails (estimateId, estLineType, estLineLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, estLineVATtax, prodSize, labourAmount, withInstall, createStamp, createUser, estLineOrder)
            SELECT estimateId, 'p' as estLineType, estLineLink, prod.prodid, estDesign, estQuant, estItemValue, vatid, vatTax,labourGroup as prodSize, (CASE withInstall WHEN 1 THEN isnull(psp.pspAmount,0) ELSE 0 END), withInstall, '2025-02-09 16:02' as createStamp, 'admin' as createUser, '$destLine'+ROW_NUMBER() OVER (order by impId) as rowNum
              FROM estimateDetailsImport
              LEFT JOIN rsProducts prod on prod.prodArticle = estReference
              LEFT JOIN tblProdSize ps on ps.sizeCode = labourGroup
              LEFT JOIN tblProdSizePrices psp on psp.sizeid = ps.sizeId and getdate() between psp.dtFrom and psp.dtTo
              INNER JOIN tblVATtaxes vat on vat.byDefault = 1
              where estimateId = '$eId' and estLineLink = '$gl' ";
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

