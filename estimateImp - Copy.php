<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['y'] )) { $y = $_GET['y'];  } else { $y = ''; };
if ( isset( $_GET['m'] )) { $m = $_GET['m'];  } else { $m = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = ''; };


require_once 'vendor/autoload.php';
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

$user = $_SESSION['userCode'];
$post = getPost();

$tipo = $post[fileType];

$splitId = explode("_", $id);
$estimateId = $splitId[1];
$estLineLink = $splitId[2];

echo $tipo." - ".$estimateId." - ".$estLineLink;

$lote = 0;

if ( $t == 'f' ) {


	$sql4 = "SELECT id, filename, tipo, fileext
   			FROM DataImpFiles 
 			where filename = '$id' AND proDate is null"; 
	$db->debug=1;
	$rows_emps4 = $db->Execute($sql4);

	$dfid   = $rows_emps4->fields[0];
	$fName  = $rows_emps4->fields[1];
	$ext    = $rows_emps4->fields[3];

	

		$path="c:\\data\\ls\\estimate\\".$fName.".".$ext;
		echo $path.'<br>';
		if (file_exists($path)) {
			echo $fName." -> OK  ".$tipo."<br>";

			if ( $ext == 'xlsx' ) $reader = ReaderFactory::create(Type::XLSX); // for XLSX files
			if ( $ext == 'csv' ) {
				$reader = ReaderFactory::create(Type::CSV); // for XLSX files
				$reader->setFieldDelimiter(',');
			}
//			$reader->setShouldFormatDates(true);
			$reader->open($path);

			$sql = "select NEXT VALUE FOR dbo.ImpFiles";
			$rows_emps = $db->Execute($sql);
			$lote = $rows_emps->fields[0];
//			echo $lote.'<br>';


			foreach ($reader->getSheetIterator() as $sheet) {
				$worksheetTitle = $sheet->getName();
				echo "--->".$worksheetTitle." -- <br>";
 				$impdate = Date("Y-m-d", time());
	    		
	    		$rowNum = 0;    			
	    		foreach ($sheet->getRowIterator() as $row) {
    				++$rowNum;
					if ( $rowNum > 1) {
						if ( $row[0] != '' ) {
							print_r($row);
							echo '<br>';

							$c0 = $row[0];
							$descrip = $row[1];
							$quant = $row[2];
							$size = $row[3];
							$price = $row[4];
//							$data = substr($tData,6,4).'/'.substr($tData,3,2).'/'.substr($tData,0,2);
//							$data = substr($row[9]->date,0,10);
						
							if ( $row[0] != '' && $rowNum > 1 ) {
								$sql4 = "INSERT INTO estimateDetailsImport ( impType, estimateId, estLineLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, labourGroup, labourQuant, labourValue, integratedId) VALUES ( '$tipo', '$estimateId', '$estLineLink', ' ', '$descrip', '$quant', '$price', 'ST', '$size', 0, 0, 0 )";
								$db->debug=1;
								$rows_emps4 = $db->Execute($sql4);
							}						
						}	
					}
				}
			}
        	$reader->close();

			echo '<br><B>'.$rowNum.'</b>';
		}		

		$sql2 = "UPDATE DataImpFiles
	        	 SET proDate = getdate(),
	        		 proUserId = '".$user."',
	        		 tipo = '".$tipo."',
	        		 lote = $lote
	             WHERE Id = $dfid ";
//		$db->debug=1;
		$rows_emps2 = $db->Execute($sql2);


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