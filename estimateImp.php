<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['y'] )) { $y = $_GET['y'];  } else { $y = ''; };
if ( isset( $_GET['m'] )) { $m = $_GET['m'];  } else { $m = ''; };
if ( isset( $_GET['id'] )) { $id = $_GET['id'];  } else { $id = ''; };


require_once 'vendor/autoload.php';

$user = $_SESSION['userCode'];
$json = file_get_contents('php://input');
$data = json_decode($json);

$tipo = $data->fileType;
echo $tipo;
$tipo = 'products';

$splitId = explode("_", $id);
$estimateId = $splitId[1];
$estLineLink = $splitId[2];

echo $tipo." - ".$estimateId." - ".$estLineLink;

$sql = "DELETE FROM estimateDetailsImport
		WHERE estimateId = '$estimateId' AND estLineLink = '$estLineLink' ";
$rows_emps = $db->Execute($sql);
 
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

			if('csv' == $ext ){
		        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();

		    }elseif('xls' == $ext ){
		        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();

		    }else {
		        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		     
		    }			
		    /**  Load $inputFileName to a Spreadsheet Object  **/
			$reader->setReadDataOnly(true);
			$spreadsheet = $reader->load($path);	

			$sql = "select NEXT VALUE FOR dbo.ImpFiles";
			$rows_emps = $db->Execute($sql);
			$lote = $rows_emps->fields[0];
//			echo $lote.'<br>';

			$sheet = $spreadsheet->getActiveSheet()->toArray();

			if ( $tipo == 'products' ) {
			  	$columns = $sheet[1];
				for ($i = 1; $i < count($sheet); $i++) {
					print_r($sheet[$i]);
				    $row = $sheet[$i];

					$c0 = $row[0];
					$descrip = str_replace("'","''",str_replace('"','\'', $row[1]));
					$quant = $row[2];
					$size = $row[3];
					$price = $row[4];
					$install = $row[5];
					$notes = $row[6];
				
					if ( $row[0] != '' ) {
						$sql3 = "INSERT INTO estimateDetailsImport ( impType, estimateId, estLineLink, estReference, estDesign, estQuant, estItemValue, estLineVAT, labourGroup, labourQuant, labourValue, integratedId, withInstall, notes) VALUES ( '$tipo', '$estimateId', '$estLineLink', '$c0', '$descrip', '$quant', '$price', 'ST', '$size', 0, 0, 0, '$install', '$notes' )";
						$db->debug=1;
						$rows_emps3 = $db->Execute($sql3);
					}	
				}
			} 
			echo '<br><B>'.$rowNum.'</b>';
		}		

		$sql2 = "UPDATE DataImpFiles
	        	 SET proDate = getdate(),
	        		 proUserId = '".$user."',
	        		 tipo = '".$tipo."',
	        		 lote = $lote
	             WHERE Id = $dfid ";
//		$db->debug=1;
//		$rows_emps2 = $db->Execute($sql2);


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