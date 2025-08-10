<?php

include( 'include.php' );
require_once('TCPDF/tcpdf.php');

if ( isset( $_GET['g'] )) { $g = $_GET['g'];  } else { $g = '0'; };
if ( isset( $_GET['eId'] )) { $eId = $_GET['eId'];  } else { $eId = '0'; };


class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'gruta_logo.jpg';
        $this->Image($image_file, 10, 10, 30, '', 'JPG', '', 'T', false, 300, 'R', false, false, 0, false, false, false);
        // Set font
        $this->setFontSubsetting(true);
        $this->SetFont('freeserif', '', 10);
        // Title
        $this->SetX(15);
        $this->Cell(0, 0, 'UAB GRUTA', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetX(15);
        $this->Cell(0, 0, 'Sumsko g: 1', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetY(15);
        $this->Cell(0, 0, 'sarunas@gruta.lt', 0, false, 'L', 0, '', 0, false, 'M', 'M');
        $this->SetX(15);
        $this->Cell(0, 0, '02246 Vilnius', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->setFontSubsetting(true);
        $this->SetFont('freeserif', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

function rome($N){
    $c='IVXLCDM';
    for($a=5,$b=$s='';$N;$b++,$a^=7)
        for($o=$N%$a,$N=$N/$a^0;$o--;$s=$c[$o>2?$b+$N-($N&=-2)+$o=1:$b].$s);
    return $s;
}
$viso = '';

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'PDF_HEADER_TITLE', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->setFontSubsetting(true);
$pdf->SetFont('freeserif', '', 10);

// add a page
$pdf->AddPage();
$pdf->SetY(35);

$tbl = '
<table cellspacing="0" cellpadding="1" style="border: 1px solid #444;">
    <tr style="border: 1px solid #444;">
        <td width="30" style="border: 1px solid #ddd;">Nr.</td>
        <td width="270" style="border: 1px solid #ddd;">Pavadinimas</td>
        <td width="50" style="border: 1px solid #ddd;">Kiekis</td>
        <td width="50" style="border: 1px solid #ddd;">Mat.vnt.</td>
        <td width="75" style="border: 1px solid #ddd;">Kaina medžiagų</td>
        <td width="75" style="border: 1px solid #ddd;">Kaina darbų</td>
        <td width="85" style="border: 1px solid #ddd;">Bendra kaina</td>
        <td width="100" style="border: 1px solid #ddd;">Viso be PVM</td>
        <td width="180" style="border: 1px solid #ddd;" style="border: 1px solid #ddd;">Pastabos</td>
    </tr> ';

  $sql = "SELECT totId, labour, parts,total
          FROM (
              SELECT (CASE WHEN estD.estGroupLine is not null THEN estD.estimateDetId ELSE estD.estLineLink END) as TotId, sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE estD.estQuant*isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END) *(1+estD.estLineVATtax)) as total
              FROM estimateDetails estD
              INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
              LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
              LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
              WHERE estD.estimateId = $eId and estD.estLineType not in ('h','k') and isnull(estD.isDeleted,0) = 0 
              GROUP BY (CASE WHEN estD.estGroupLine is not null THEN estD.estimateDetId ELSE estD.estLineLink END)
              UNION ALL
              SELECT (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END) as TotId, sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE 0 END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END) *(1+estD.estLineVATtax)) as total
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

  $sql = "SELECT estD.estimateDetId, estD.estLineType, (CASE WHEN estd.estLineType in ('h','k') THEN estD.estGroupLine ELSE isnull(estD.estReference,'') END), (CASE estD.estLineType WHEN 'n' THEN isnull(estD.estNotes,'') ELSE estD.estDesign END), (CASE isnull(estD.estQuant,0) WHEN 0 THEN 1 ELSE estD.estQuant END),  (CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estItemValue ELSE isnull(estD.LabourAmount,0) END) as labour, (CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, estD.estLineVATtax, (estD.estQuant*estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)*(1+estD.estLineVATtax)), estHS.estLineBold, isnull(fam.famIcon,'mdi mdi-segment'), isnull(estD.estGroupLine,eLine.estGroupLine), estd.estLineLink, (CASE WHEN estd.estLineType in ('h','k') THEN 1 ELSE 0 END) as toCalc, isnull(estd.estLineSLink,0), isnull(estD.estNotes,''), isnull(estD.estNotesCust,0), isnull(estD.unitLine,''), (estD.estQuant*estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)) as sTot 
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          INNER JOIN estimateDetailsSort estHS on estHS.estLineItem = estD.estLineType
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateId = $eId  and isnull(estD.isDeleted,0) = 0  ";
  if ( $g == 1 ) $sql .= " and estLineBold = 1 ";
  $sql .= " ORDER BY (CASE WHEN estD.estGroupLine is not null THEN estD.estGroupLine ELSE eLine.estGroupLine END), (CASE WHEN estD.estGroupSLine is not null THEN estD.estimateDetId ELSE estd.estLineSLink END) , estHS.estLineOrder";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $firstReg = 1;
  while(!$rows_emps->EOF) {

    if ( $firstReg == 0 && trim($rows_emps->fields[1]) == 'h' ) {
        $tbl .= "<tr>";
        $tbl .= '<td colspan="10" style="border: 1px solid #ddd;"></td>';
        $tbl .= "</tr>";        
    }
    $lineBold = trim($rows_emps->fields[9]);
    if ( $rows_emps->fields[1] == 'n' ) {
        if ( $rows_emps->fields[9] == '1' && trim($rows_emps->fields[1]) == 'k' && $g == 1 ) { $lineBold = 0; }
        $tbl .= "<tr>";
        if ( $rows_emps->fields[1] == 'h' ) { $col1 = $rows_emps->fields[2]; } else { $col1 = ''; }
        $tbl .= '<td width="30" align="right" style="border: 1px solid #ddd;">'.$col1.'</td>';
        $col2 = trim($rows_emps->fields[3]);
        $tbl .= '<td colspan="9" style="border: 1px solid #ddd;">'.$col2.'</td>';
        $tbl .= "</tr>";
    }
    if ( $rows_emps->fields[1] != 'n' ) {
        if ( $rows_emps->fields[9] == '1' && trim($rows_emps->fields[1]) == 'k' && $g == 1 ) { $lineBold = 0; }
        $tbl .= "<tr>";
        if ( $rows_emps->fields[1] == 'h' ) { 
            $col1 = rome($rows_emps->fields[2]); 
            $viso .= $col1.'+';
        } else { $col1 = ''; }
        $tbl .= '<td width="30" align="right" style="border: 1px solid #ddd;">'.$col1.'</td>';
        $col2 = trim($rows_emps->fields[3]);
        $design = '';
        if ( $rows_emps->fields[9] == '0' ) $design .= "&nbsp;&nbsp;&nbsp;";
        if ( $rows_emps->fields[9] == '1' && trim($rows_emps->fields[1]) == 'k' ) $design .= "&nbsp;&nbsp;&nbsp;";
        if ( $rows_emps->fields[9] == '0' && $rows_emps->fields[14] > 0 ) $design .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        $col2 = $design.$col2;
        if ( $lineBold == 1 ) $col2 = '<b>'.$col2.'</b>';
        $tbl .= '<td width="270" style="border: 1px solid #ddd;">'.$col2.'</td>';
        $col4 = trim($rows_emps->fields[4]);
        if ( $lineBold == 1 ) $col4 = '<b>'.$col4.'</b>';
        $tbl .= '<td width="50" style="border: 1px solid #ddd;" align="center">'.$col4.'</td>';
        $col17 = trim($rows_emps->fields[17]);
        if ( $lineBold == 1 ) $col17 = '<b>'.$col17.'</b>';
        $tbl .= '<td width="50" style="border: 1px solid #ddd;" align="center">'.$col17.'</td>';
        if ( trim($rows_emps->fields[13]) == '0' ) $col5 = number_format($rows_emps->fields[5],2,'.',',');
        if ( trim($rows_emps->fields[13]) == '1' ) $col5 = number_format($tLineL[trim($rows_emps->fields[0])],2,'.',',');
        if ( $lineBold == 1 ) $col5 = '<b>'.$col5.'</b>';
        $tbl .= '<td width="75" style="border: 1px solid #ddd;" align="right">'.$col5.' €</td>';
        if ( trim($rows_emps->fields[13]) == '0' ) $col6 = number_format($rows_emps->fields[6],2,'.',',');
        if ( trim($rows_emps->fields[13]) == '1' ) $col6 = number_format($tLineP[trim($rows_emps->fields[0])],2,'.',',');
        if ( $lineBold == 1 ) $col6 = '<b>'.$col6.'</b>';
        $tbl .= '<td width="75" style="border: 1px solid #ddd;" align="right">'.$col6.' €</td>';
        if ( trim($rows_emps->fields[13]) == '0' ) $col7 = number_format(($rows_emps->fields[18]),2,'.',',');
        if ( trim($rows_emps->fields[13]) == '1' ) $col7 = number_format($tLineL[trim($rows_emps->fields[0])]+$tLineP[trim($rows_emps->fields[0])],2,'.',',');
        if ( $lineBold == 1 ) $col7 = '<b>'.$col7.'</b>';
        $tbl .= '<td width="85" style="border: 1px solid #ddd;" align="right">'.$col7.' €</td>';
        if ( trim($rows_emps->fields[13]) == '0' ) $col8 = number_format($rows_emps->fields[4]*($rows_emps->fields[5]+$rows_emps->fields[6]),2,'.',',');
        if ( trim($rows_emps->fields[13]) == '1' ) {
            $qt = $rows_emps->fields[4];
            $col8 = number_format($qt*($tLineL[trim($rows_emps->fields[0])]+$tLineP[trim($rows_emps->fields[0])]),2,'.',',');
        }
        if ( $lineBold == 1 ) $col8 = '<b>'.$col8.'</b>';
        $tbl .= '<td width="100" style="border: 1px solid #ddd;" align="right">'.$col8.' €</td>';
        $col9 = '';
        if ( trim($rows_emps->fields[16]) == '1' ) {
            $col9 = trim($rows_emps->fields[15]);
        }
        if ( $lineBold == 1 ) $col9 = '<b>'.$col9.'</b>';
        $tbl .= '<td width="180" style="border: 1px solid #ddd;" align="left">'.$col9.'</td>';
        $tbl .= "</tr>";
    }

    if ( $firstReg == 1 ) $firstReg = 0;
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();

  $viso = substr($viso,0,-1);
  $tbl .= "<tr>";
  $tbl .= '<td colspan="10" style="border: 1px solid #ddd;"></td>';
  $tbl .= "</tr>";        

  $sql = "SELECT  sum(CASE isnull(fam.famLP,'p') WHEN 'l' THEN estD.estQuant*estD.estItemValue ELSE estD.estQuant*isnull(estD.LabourAmount,0) END) as labour, sum(CASE isnull(fam.famLP,'p') WHEN 'p' THEN estD.estQuant*estD.estItemValue*isnull(partsFactor,1) ELSE 0 END) as parts, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(estD.estLineVATtax)) as vat, sum(estD.estQuant*(estD.estItemValue*(CASE isnull(fam.famLP,'p') WHEN 'p' THEN isnull(partsFactor,1) ELSE 1 END)+isnull(estD.LabourAmount,0)) *(1+estD.estLineVATtax)) as total
          FROM estimateDetails estD
          INNER JOIN estimateHeader estH on estH.estimateId = estD.estimateId
          LEFT  JOIN estimateDetails eLine on eLine.estimateDetId = estD.estLineLink
          LEFT  JOIN tblFamily fam on fam.famType = estD.estLineType
          WHERE estD.estimateId = $eId and isnull(estD.isDeleted,0) = 0 ";
  $rows_emps = $db->Execute($sql);

  $tbl .= "<tr>";
  $tbl .= '<td colspan="4" style="border: 1px solid #ddd;"></td>';
  $tbl .= '<td colspan="3" style="border: 1px solid #ddd;" align="right"><b>Viso ('.$viso.')</b></td>';
  $colt = number_format($rows_emps->fields[0]+$rows_emps->fields[1],2,'.',',');
  $tbl .= '<td width="100" style="border: 1px solid #ddd;" align="right"><b>'.$colt.' €</b></td>';
  $tbl .= '<td width="180" style="border: 1px solid #ddd;" align="right"></td>';
  $tbl .= "</tr>";        
  $tbl .= "<tr>";
  $tbl .= '<td colspan="4" style="border: 1px solid #ddd;"></td>';
  $tbl .= '<td colspan="3" style="border: 1px solid #ddd;" align="right">PVM</td>';
  $colt = number_format($rows_emps->fields[2],2,'.',',');
  $tbl .= '<td width="100" style="border: 1px solid #ddd;" align="right">'.$colt.' €</td>';
  $tbl .= '<td width="180" style="border: 1px solid #ddd;" align="right"></td>';
  $tbl .= "</tr>";        
  $tbl .= "<tr>";
  $tbl .= '<td colspan="4" style="border: 1px solid #ddd;"></td>';
  $tbl .= '<td colspan="3" style="border: 1px solid #ddd;" align="right">Viso su PVM</td>';
  $colt = number_format($rows_emps->fields[3],2,'.',',');
  $tbl .= '<td width="100" style="border: 1px solid #ddd;" align="right">'.$colt.' €</td>';
  $tbl .= '<td width="180" style="border: 1px solid #ddd;" align="right"></td>';
  $tbl .= "</tr>";        

$tbl .= '</table>';

//echo $tbl;

$pdf->writeHTML($tbl, true, false, false, false, '');

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_003.pdf', 'I');
