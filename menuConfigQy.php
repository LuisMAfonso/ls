<?php 
include( 'include.php' );

if ( isset( $_GET['t'] )) { $t = $_GET['t'];  } else { $t = ''; };
if ( isset( $_GET['u'] )) { $u = $_GET['u'];  } else { $u = ''; };
if ( isset( $_GET['c'] )) { $c = $_GET['c'];  } else { $c = ''; };

if ( $t == 'a') {
  header("Content-type: application/json");

  $output = '';

  $sql = "SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos
          FROM UsersMenus umn
          WHERE LinkId = 0 and tipo not in ('s','e')
          ORDER BY Pos";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);  

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {

    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $iconMenu = '<i class=\"'.trim($rows_emps->fields[4]).'\"> </i> ';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"name": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps->fields[2]).'", ';
    $output .= '"OptId": "'.trim($rows_emps->fields[1]).'", ';
    $output .= '"Descricao": "'.trim($rows_emps->fields[2]).'", ';
    $output .= '"DescricaoPai": "'.trim($rows_emps->fields[3]).'", ';
    $output .= '"NomeFicheiro": "'.trim($rows_emps->fields[5]).'", ';
    $output .= '"Tipo": "'.trim($rows_emps->fields[6]).'", ';
    $output .= '"Pos": "'.trim($rows_emps->fields[7]).'", ';
    $output .= '"Icon": "'.trim($rows_emps->fields[4]).'" ';
    if ( $rows_emps->fields[6] == 'm' ) {
      $output .= ', ';
      $output .= '"items": [';
      $sql1 = "SELECT ID, OptId, OptDesc, LinkId, Icon, Prog, Tipo, Pos 
          FROM UsersMenus 
          WHERE LinkId = ".$rows_emps->fields[0]." and tipo not in ('s','e')
          ORDER BY Pos";
      $rows_emps1 = $db->Execute($sql1);  


      $first2 = 1;
      while(!$rows_emps1->EOF) {
        if ( $first2 == 0 ) $output .= ', ';
        if ( $first2 == 1 ) $first2 = 0;
        $output .= '{';
        $iconMenu = '<i class=\"'.trim($rows_emps1->fields[4]).'\"> </i> ';
        $output .= '"id": "'.trim($rows_emps1->fields[0]).'", ';
        $output .= '"name": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps1->fields[2]).'", ';
        $output .= '"OptId": "'.trim($rows_emps1->fields[1]).'", ';
        $output .= '"Descricao": "'.trim($rows_emps1->fields[2]).'", ';
        $output .= '"DescricaoPai": "'.trim($rows_emps1->fields[3]).'", ';
        $output .= '"NomeFicheiro": "'.trim($rows_emps1->fields[5]).'", ';
        $output .= '"Tipo": "'.trim($rows_emps1->fields[6]).'", ';
        $output .= '"Pos": "'.trim($rows_emps1->fields[7]).'", ';
        $output .= '"Icon": "'.trim($rows_emps1->fields[4]).'" ';
        if ( $rows_emps1->fields[6] == 'm' ) {
          $output .= ', ';
          $output .= '"items": [';
          $sql2 = " SELECT ID, OptId, OptDesc, LinkId, Icon, Prog, Tipo, Pos 
                FROM UsersMenus 
                WHERE LinkId = ".$rows_emps1->fields[0]." and tipo not in ('s','e')
                ORDER BY Pos ";
          $rows_emps2 = $db->Execute($sql2);  
          
          $first3 = 1;
          while(!$rows_emps2->EOF) {
            if ( $first3 == 0 ) $output .= ', ';
            if ( $first3 == 1 ) $first3 = 0;
            $output .= '{';
            $iconMenu = '<i class=\"'.trim($rows_emps2->fields[4]).'\"> </i> ';
            $output .= '"id": "'.trim($rows_emps2->fields[0]).'", ';
            $output .= '"name": "'.$iconMenu.'&nbsp;&nbsp; '.trim($rows_emps2->fields[2]).'", ';
            $output .= '"OptId": "'.trim($rows_emps2->fields[1]).'", ';
            $output .= '"Descricao": "'.trim($rows_emps2->fields[2]).'", ';
            $output .= '"DescricaoPai": "'.trim($rows_emps2->fields[3]).'", ';
            $output .= '"NomeFicheiro": "'.trim($rows_emps2->fields[5]).'", ';
            $output .= '"Tipo": "'.trim($rows_emps2->fields[6]).'", ';
            $output .= '"Pos": "'.trim($rows_emps2->fields[7]).'", ';
            $output .= '"Icon": "'.trim($rows_emps2->fields[4]).'" ';
            $output .= '} ';
            $rows_emps2->MoveNext();
          } 
          $output .= ']';
          $rows_emps2->Close();
        }
        $output .= '}';
        $rows_emps1->MoveNext();
      } 
      $output .= ']';
      $rows_emps1->Close();
    }
    $output .= '}';
    $rows_emps->MoveNext();
  } 
  $output .= ']';
  $rows_emps->Close();


  print_r($output);
} elseif ($t == 'getParent'){

  header("Content-type: application/json");
  $output = '';

  $sql = "SELECT ID, OptId, OptDesc FROM UsersMenus WHERE LinkId = 0 AND Tipo = 'm' ORDER BY Pos ASC";
//  $db->debug=1;
  $rows_emps = $db->Execute($sql);

  $first1 = 1;
  $output .= '[';
  while(!$rows_emps->EOF) {
    if ( $first1 == 0 ) $output .= ', ';
    if ( $first1 == 1 ) $first1 = 0;
    $output .= '{';
    $output .= '"id": "'.trim($rows_emps->fields[0]).'", ';
    $output .= '"value": "'.trim($rows_emps->fields[2]).'" ';
    $output .= '}';

   $sql1 = "SELECT ID, OptId, OptDesc FROM UsersMenus WHERE LinkId = ".trim($rows_emps->fields[0])." AND Tipo = 'm' ORDER BY Pos ASC";
    $rows_emps1 = $db->Execute($sql1);
    while(!$rows_emps1->EOF) {
      $output .= ',{';
      $output .= '"id": "'.trim($rows_emps1->fields[0]).'", ';
      $output .= '"value": "'.trim($rows_emps1->fields[2]).'" ';
      $output .= '}';

      $sql2 = "SELECT ID, OptId, OptDesc FROM UsersMenus WHERE LinkId = ".trim($rows_emps1->fields[0])." AND Tipo = 'm' ORDER BY Pos ASC";
      $rows_emps2 = $db->Execute($sql2);
      while(!$rows_emps2->EOF) {
        $output .= ',{';
        $output .= '"id": "'.trim($rows_emps2->fields[0]).'", ';
        $output .= '"value": "'.trim($rows_emps2->fields[2]).'" ';
        $output .= '}';
        $rows_emps2->MoveNext();
      }
      $rows_emps2->Close();
      $rows_emps1->MoveNext();
    }
    $rows_emps1->Close();
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();
  $output .= ']';

  print_r($output);
} elseif ( $t == 'b') {
  header("Content-type: application/json");

  $sql = "SELECT groupMn.UGroupID, uGroup.UGroup , groupMn.CanRead, groupMn.CanCreate, groupMn.CanModify, groupMn.CanDelete 
          FROM UsersGroupsMenu  groupMn
          LEFT JOIN UsersUGroup  uGroup ON groupMn.UGroupID = uGroup.ID
          WHERE groupMn.OptId = $c  
          ORDER BY groupMn.UGroupID ASC";
  $rows_emps = $db->Execute($sql);
  
  print('[');
  $firstReg = 1;
  while(!$rows_emps->EOF) {
    if ( $firstReg == 0 ) print(',');
    if ( $firstReg == 1 ) $firstReg = 0;
    print('{');
    print(' "GroupID": "'.trim($rows_emps->fields[0]).'", ');
    print(' "GrupoDesc": "'.trim($rows_emps->fields[1]).'" ');
    print('}');
    $rows_emps->MoveNext();
  }
  $rows_emps->Close();
  print(']');
} 

?>