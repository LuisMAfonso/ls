<?php 

if ($_SESSION['userCode'] == ''){
    $actual_link = "$_SERVER[REQUEST_URI]";
    header("Location: /birent3/index.php?p=".$actual_link);
    exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
	<meta charset="utf-8">

    <?php
        $actual_link = "$_SERVER[REQUEST_URI]";
        $actual_link = explode("/",$actual_link);
        $pageTemp = '';
        $pageTempTB = '';
        $sql = "SELECT [OptDesc] FROM prod.[BIrent].[dbo].[UsersMenus] WHERE Prog = '".$actual_link[3].".php' ";
        $rows_emps = $db->Execute($sql);
        if($rows_emps->rowCount() > 0){
            $pageTemp = utf8(trim($rows_emps->fields[0])).' | ';
            $pageTempTB = utf8(trim($rows_emps->fields[0]));
        }
    ?> 
    <title><?php echo $pageTemp; ?>BI Rent v3 - DEV</title> 
	

    <link rel="shortcut icon" href="../images/favicon.ico" />  
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <link rel="stylesheet" type="font/x-woff" href="../codebase/fonts/roboto-bold-webfont.woff"/>
    <link rel="stylesheet" type="text/css" href="../codebase/suite.css"/>
    <script src="../codebase/suite.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.4.95/css/materialdesignicons.css?v=6.4.2" media="all" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="../codebase/fontawesome-6.2.0/css/all.css"/>


    <!-- Adicionado 18-10-2021
    André Silva -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <script>
        window.dhx_globalImgPath="../codebase/imgs/";
        var shortName = '<?php echo $_SESSION["user_name"]; ?>';
        var userCode = '<?php echo $_SESSION["userCode"]; ?>';
        var userEmployee = '<?php echo $_SESSION["Employee"]; ?>';
        var notificationStatus = '<?php echo $_SESSION["notification"]; ?>';
        if(notificationStatus == 1){
            var myMenuStr = '<div id="contextArea"><b>'+shortName+'</b> <span class="w3-badge w3-red">1</span>&nbsp;<img src="images/menu/m_options_18.png"></div>';
        }
        else{
            var myMenuStr = '<div id="contextArea"><b>'+shortName+'</b>&nbsp;<img src="images/menu/m_options_18.png"></div>';
        }
        var myMenu;
        var wToolbarIcon = 32;
    </script>



   <style type="text/css" media="screen">
        html, body {
            width: 100%;
            height: 100%;
            margin: 0px;
            padding: 0px;
            overflow: hidden;
            background-color:white;
        }
        .dhx_toolbar_btn .dhx_toolbar_btn img{
            position:relative; top:-7px;
            float:none !important;
            display:inline-block;
        }
        .dhx_toolbar_btn .dhxtoolbar_text {
            float:none !important;
            display:inline-block;
        }
        .dhx_toolbar_btn{
            white-space: nowrap;
        }
        .td_btn_txt .btn_sel_text{
            white-space: nowrap;
        }
        .dhx_header_cmenu{
            background-color:#ffffff;
            border:2px outset silver;
            z-index:2;
        }
        .dhx_header_cmenu_item{
            white-space:nowrap;
        }
        div #objId {
            width: 100%;
            height: 100%;
            overflow: auto;
        }

        body {
            margin: 0;
        }
        .dhx_navbar--vertical {
            overflow: auto;
            flex: 1 1 auto;
        }
        .user-info_container {
            padding-top: 6px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
        }
        .user-info_avatar {
            height: 40px;
            width: 40px;
            border-radius: 100%;
        }
        .user-info_title {
            font-family: Roboto;
            font-style: normal;
            font-weight: 500;
            font-size: 16px;
            line-height: 24px;
            margin-top: 8px;
            color: #f97d00;
        }
        .user-info_contact {
            font-family: Roboto;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            line-height: 20px;
            margin-bottom: 28px;
            color: rgba(0, 0, 0, 0.5);
        }
        .dhx_sidebar--minimized .user-info_avatar {
            height: 30px;
            width:30px;
        }
        .dhx_sidebar--minimized .user-info_title,
        .dhx_sidebar--minimized .user-info_contact {
            visibility: hidden;
        }
        .dhx_sample-container,
        .dhx_sample-container__widget {
            height: 100%;
        }
        /*.dhx_grid-row {
            height: 30px !important;
        }
        .dhx_string-cell {
            height: 30px !important;
        }*/
        .dhx_layout-wide.dhx_layout-rows > .dhx_layout-cell {
            margin-bottom: 0px;
        }

        /*CORES DAS LINHAS DA GRID*/
        .alternate_row .dhx_grid-row {
            background: #ffffff;
        }

        .alternate_row .dhx_grid-row:nth-child(2n) {
            background: #f2f2f2;
        }


        .customToolbar{
            color: #f29d00;
            font-size: 20px;
            padding-left: 15px;
            font-weight: bold;
        }

         /* <link href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.4.95/css/materialdesignicons.css?v=6.4.2" media="all" rel="stylesheet" type="text/css">

        <!-- component container -->
        <section class="dhx_sample-container">
            <div class="dhx_sample-container__widget sidebar-with-custom-html" id="sidebar"></div>
        </section> */

        /* .hvr a:not(:first-child):hover { */
        /* TESTES */


        .dhx_sidebar-button:hover {
            background-color: #f97d00;
        }

        .dhx_button.dhx_sidebar-button.dhx_sidebar-button--icon.toggle-button:hover{
            background-color: #d3d3d3;
        }
 
        .dhx_menu-button--active:not(:disabled), .dhx_menu-button:active:not(:disabled), .dhx_menu-button:focus:not(:disabled), .dhx_menu-button:hover:not(:disabled) {
            background-color: #f97d00;
            transition: background-color .2s ease-out;
        }

                
        .dhx_grid-cell {
            font-size: 16px;
            font-family: Tahoma;
        }

        /* ALTERA CABEÇALHO DA GRID */
        .dhx_header-spans .dhx_span-cell {
          /*background: #d4d4d4;
          color: #f97d00;*/
        }

        .dhx_grid-header-cell{
          /*background: #d4d4d4;
          color: #f97d00;*/
        }



        /* ALTERA COR DO RODAPE */
        .dhx_button--view_link.dhx_button--color_primary {
            color: #f97d00;
        }


        .dhx_grid-header-cell-text_content {
            font-size: 15px;
            font-weight: 800;
        }

        .dhx_span-cell {
            white-space: break-spaces;
        }
        /* .dhx_menu-button--active:not(:disabled), .dhx_menu-button:active:not(:disabled), .dhx_menu-button:focus:not(:disabled), .dhx_menu-button:hover:not(:disabled) {
            background-color: #f97d00;
        } */



        /* ALTERA COR DOS ICONS DOS BOTÕES DA TOOLBAR */
        .fa-plus {
            color:green;
        }
        .fa-floppy-disk {
            color:blue;
        }
        .fa-trash-can {
            color:red;
        }


        /* ALTERA CSS MESSAGE - POPUP */
        .customMessageT {
            border: 2px solid green;
            border-radius: 8px;
            padding-top: 10px;
        }
        .customMessageF {
            border: 2px solid red;
            border-radius: 8px;
            padding-top: 10px;
        }
        .dhx_message-container.dhx_message-container--top-right.dhx_message-container--in-body{
            top: 30px;
        }


     </style>

	</head>
	<body>
<script>

// configuring Sidebar structure

<?php 
//print_r($_SESSION);
//include( 'include.php' );

/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

    //header("Content-type: application/json");
    //$db->debug=1;
    $userCode = $_SESSION['userCode'];
    //$userCode = 'andre_silva';

    $sql = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, IconNew
            FROM (
                SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, IconNew
                FROM UsersMenus umn
                left join UsersGroups ugr on ugr.UserID = '".$userCode."' 
                left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
                WHERE LinkId = '0' 
                and ( CanRead = 1 OR CanCreate = 1 OR CanModify = 1 OR CanDelete = 1) 
                AND birent3 = 1
            ) a
            ORDER BY Pos ";
    $rows_emps = $db->Execute($sql);
    $structure = '[';
    $structure .= ' {

            "id": "toggle",
            "css": "toggle-button",
            "icon": "fa fa-bars-staggered" 
        },
        {
            "type": "customHTML",
            "id": "userInfo",
            "css": "user-info_item",
            "html": "<div class=\'user-info_container\'>" +
            "<img class=\'user-info_avatar\' src=\'https://snippet.dhtmlx.com/codebase/data/common/img/02/avatar_62.jpg\'/>" +
            "<div class=\'user-info_title\'>" +
            shortName+
            "</div>" +
            "<div class=\'user-info_contact\'>" +
            userCode+"<br><center style=\'font-size: 10px;\'>"+userEmployee+
            "</center></div>" +
            "</div>"
        },
        {
            "id": "homePage",
            "value": "Home",
            "icon": "fa fa-house",
            //"url": "home/home"
            "url": "home"
        },
        {
            "type": "separator"
        },
        {
            "id": "gruposHome",
            "value": "Grupos",
            "icon": "fa fa-car",
            //"html": "<i class=\'fa-solid fa-car\'></i>",
            //"url": "home/home"
            "url": "main"
        },';

    while(!$rows_emps->EOF) {
        $structure .= '{';
        $structure .= '"id":'.'"'.utf8(trim($rows_emps->fields[0])).'",';
        $structure .= '"value":'.'"'.utf8(trim($rows_emps->fields[2])).'",';
        $structure .= '"icon":'.'"fa '.utf8(trim($rows_emps->fields[8])).'",';
        if(utf8(trim($rows_emps->fields[6])) == 'm'){
            //$structure .= '"items": [';
            $sql1 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, IconNew
                FROM (
                    SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, IconNew
                    FROM UsersMenus umn
                    left join UsersGroups ugr on ugr.UserID = '".$userCode."' 
                    left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
                    WHERE LinkId = ".$rows_emps->fields[0]."
                    AND birent3 = 1
                    ) a
            ORDER BY Pos ";
            $rows_emps1 = $db->Execute($sql1);
            if($rows_emps1->rowCount() > 0){
                $structure .= '"items": [';
            }
            while(!$rows_emps1->EOF) {
                $structure .= '{';
                $structure .= '"id":'.'"'.utf8(trim($rows_emps1->fields[0])).'",';
                $structure .= '"value":'.'"'.utf8(trim($rows_emps1->fields[2])).'",';
                $structure .= '"icon":'.'"fa '.utf8(trim($rows_emps1->fields[8])).'",';
                if(utf8(trim($rows_emps1->fields[6])) == 'm'){
                    $sql2 = "SELECT distinct Id, OptId, OptDesc, LinkId, Icon, prog, Tipo, Pos, IconNew
                        FROM (
                            SELECT umn.ID, umn.OptId, OptDesc, LinkId, Icon, Prog as prog, Tipo, Pos, IconNew
                            FROM UsersMenus umn
                            left join UsersGroups ugr on ugr.UserID = '".$userCode."' 
                            left join UsersGroupsMn ugm on ugm.OptId = umn.Id and ugm.UGroupID = ugr.UGroupID
                            WHERE LinkId = ".$rows_emps1->fields[0]."
                            AND birent3 = 1
                            ) a
                    ORDER BY Pos ";
                    $rows_emps2 = $db->Execute($sql2);
                    if($rows_emps2->rowCount() > 0){
                        $structure .= '"items": [';
                    }
                    while(!$rows_emps2->EOF) {
                        $structure .= '{';
                        $structure .= '"id":'.'"'.utf8(trim($rows_emps2->fields[0])).'",';
                        $structure .= '"value":'.'"'.utf8(trim($rows_emps2->fields[2])).'",';
                        $structure .= '"icon":'.'"fa '.utf8(trim($rows_emps2->fields[8])).'",';
                        $structure .= '"url":'.'"'.str_replace('.php', '', utf8(trim($rows_emps2->fields[5]))).'"';
                        $structure .= '},';
                        $rows_emps2->MoveNext();
                    }
                    if($rows_emps2->rowCount() > 0){
                        $structure = substr($structure,0,-1);
                        $structure .= ']';
                    }
                }
                else{
                    $structure .= '"url":'.'"'.str_replace('.php', '', utf8(trim($rows_emps1->fields[5]))).'"';
                }
                $structure .= '},';
                $rows_emps1->MoveNext();
            }
            if($rows_emps1->rowCount() > 0){
                $structure = substr($structure,0,-1);
                $structure .= ']';
            }
        }
        else{
            $structure .= '"url":'.'"'.utf8(trim($rows_emps->fields[5])).'"';
        }
        $structure .= '},';
        $rows_emps->MoveNext();
    }
    $structure = substr($structure,0,-1);
    $structure .= ']';

    $rows_emps->Close();
    //print($structure);
?>
const structure = <?php echo $structure; ?>;

  


dhx.scrollViewConfig.enable = true;
dhx.scrollViewConfig.autoHide = true; 
dhx.scrollViewConfig.timeout = 1000; 

const sidebar = new dhx.Sidebar("sidebar", {
  css: "dhx_widget--border_right",
      itemHeight: 5,    height: "100%",    dragMode: "both"
});

//sidebar.data.parse(structure);

sidebar.events.on("click", function(id){
  if(id === "toggle"){
    const toggleItem = sidebar.data.getItem("toggle");

    sidebar.toggle();

    if(sidebar.config.collapsed){
      toggleItem.icon = "fa fa-bars";
    }
    else {
      toggleItem.icon = "fa fa-bars-staggered";
    }
  }
  else{
    const linkTemp = sidebar.data.getItem(id);
    //alert(linkTemp.url);
    if(linkTemp.url !== undefined){
        window.location.href = '/birent3/files/'+linkTemp.url;
    }
  }
});




// Layout initialization
const layout = new dhx.Layout("layout", {
    cols: [
        { id: "sidebar", width: "content" },
        {
            type: "wide",
            rows: [
                { id: "toolbar", width: "content" , height: "52px"},
                { id: "main", width: "content" },
                { id: "pagination", width: "content", height: "45px" }
               
            ]
        }
    ]
});


// loading the structure into Sidebar
sidebar.data.parse(structure);
layout.getCell("sidebar").attach(sidebar);


//Toolbar
const toolbarData = [
    /*{
        "id": "pageName",
        "color": "#f97d00",
        "value": "<?php echo $pageTempTB; ?>"
    },*/
    {
        "type": "customHTML",
        "html": "<?php echo $pageTempTB; ?>",
        "css": "customToolbar"
    },
    {
        "type": "spacer"
    },
    {
      type: "separator"
    },
    {
        "type": "button",
        "view": "link",
        "color": "secondary",
        "circle": true,
        "id": "notificationsBT",
        "icon": "fa fa-bell",
        "tooltip": "Notifications",
        "count": 1
    },
    {
        "type": "button",
        "view": "link",
        "color": "primary",
        "circle": true,
        "id": "logoutBT",
        "icon": "fa fa-power-off",
        "tooltip": "Logout"
    }
];
 
const toolbar = new dhx.Toolbar("toolbar", {
    css:"dhx_widget--bordered",
    data: toolbarData
});
 
toolbar.events.on("click", function(id,e){
    if(id == 'logoutBT'){
        dhx.confirm({
            text: 'Tem a certeza que deseja sair?',
            buttonsAlignment: "center",
            buttons: ["Cancelar", "Sim"]
        }).then(function(a){
            if(a){
                window.location = '/birent3/logout';  
            } 
        });
    }
    else if(id == 'pageName'){
        location.reload(); 
    }
});

layout.getCell("toolbar").attach(toolbar);

layoutPagination = new dhx.Layout("first-pagination-holder", {
    css: "dhx_widget--bordered",
    align: "center",
    cols: [
        {
            id: "pagTab1",
            width: "content"
        },
        {
            id: "pagTab2",
            width: "content"
        }
    ]
});



function popupMessage(result, text){
    iconM = '';
    infoCustom = '';
    expTime = '';
    if(result == 'true'){var iconM = 'dxi dxi-check'; infoCustom = 'customMessageT'; expTime = 3000;}
    if(result == 'false'){var iconM = 'dxi dxi-information-outline'; infoCustom = 'customMessageF'; expTime = 0;}
    dhx.message({
        text: text,
        expire: expTime,
        icon: iconM,
        position: "top-right",
        css: infoCustom
    });
}


// Controlo Collapse/Expand Sidebar
var url = document.URL;
const myArray = url.split("/");
if(myArray[5] !== 'home'){
    sidebar.collapse();
}
else{
    sidebar.expand();
}




</script>

</body>




