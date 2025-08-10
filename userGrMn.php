<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>
<div id="layout" style="height: 100vh;"></div>

<?php
require_once('sidebar.php');
?>
<script>
    var pLayout, tbOptions, tbGroups;
    var tbUsers, gUsers, gGroups, cbTree;
    var wSelected = 0;
    var treeGrid = '';
    var editOpt = false;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { id: "lGroups", html: "1", width: "250px"},
            { id: "lUsers", html: "1", width: "400px"},
            { type: "wide",
              rows: [
                { 
                    type: "none",
                    rows: [ 
                        { id: "hTree", height: "45px",  },
                        { id: "lTree", html: "" },
                    ],
                },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    tbOptions = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbOptions.data.load("toolbarsQy.php?t=change").then(function(){
//        tbOptions.disable(['edit', 'delete']);
    });;
    tbOptions.events.on("click", function(id,e){
        editOpt = tbOptions.getState("change");
    });
    pLayout.getCell("hTree").attach(tbOptions);

    treeGrid = new dhx.Grid(null, {
        type: "tree",
        columns: [
            { id: "value", width:0, header: [{ text: "Menu" }], gravity: 1.5, htmlEnable: true},
            { id: "OptId", header: [{ text: "OptId" }], gravity: 1.5, hidden: true},
            { id: "Descricao", header: [{ text: "Descricao" }], gravity: 1.5, hidden: true},
            { id: "DescricaoPai", header: [{ text: "DescricaoPai" }], gravity: 1.5, hidden: true},
            { id: "NomeFicheiro", header: [{ text: "NomeFicheiro" }], gravity: 1.5, hidden: true},
            { id: "Tipo", header: [{ text: "Tipo" }], gravity: 1.5, hidden: true},
            { id: "Pos", header: [{ text: "Pos" }], gravity: 1.5, hidden: true},
            { id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, hidden: true},
            { id: "CanRead", type: "boolean" ,width:80, header: [{ text: "View" }], htmlEnable: true, align: "center", hidden: false},
            { id: "CanCreate", type: "boolean",width:80, header: [{ text: "Create" }], htmlEnable: true, align: "center", hidden: false},
            { id: "CanModify", type: "boolean", width:80, header: [{ text: "Modify" }], htmlEnable: true, align: "center", hidden: false},
            { id: "CanDelete", type: "boolean", width:80, header: [{ text: "Delete" }], htmlEnable: true, align: "center", hidden: false}
        ],
        editable: true,
        autoWidth: true,
        css: "alternate_row",
        selection: true
    });
    treeGrid.events.on("beforeEditStart", (row, column, editorType) => {
        if( editOpt == false ) return false;
    });    
    treeGrid.events.on("afterEditEnd", (value, row, column) => {
        console.log(value+" "+row.id+" "+column.id);
        dhx.ajax.get("userGrMnWr.php?t=opt&r="+row.id+"&c="+column.id+"&v="+value+"&g="+wSelected).then(function (data) {
        }).catch(function (err) {
                console.log(err);
        });    
    });

    gUsers = new dhx.Grid(null, {
        columns: [
            { id: "UserID", width:100, header: [{ text: "User ID" }] },
            { id: "UserName", header: [{ text: "User Name" }] }
        ],
        autoWidth: true,
        adjust: "true",
        css: "alternate_row",
        selection: true
    });


    gGroups = new dhx.Grid(null, {
        columns: [
            { id: "GroupID", width:50, header: [{ text: "ID" }] },
            { id: "GrupoDesc", header: [{ text: "Grupo Desc" }] }
        ],
        autoWidth: true,
        adjust: "true",
        css: "alternate_row",
        selection: true
    });
    gGroups.events.on("cellClick", function(row,column,e){
//        console.log(row.GroupID+" "+column+" "+e);
        wSelected = row.GroupID;
        if(row.id !== ''){
            treeGrid.data.load("menuQy.php?t=man&g="+row.GroupID).then(function(){
                treeGrid.expandAll();
            });
            gUsers.data.load("userGrMnQy.php?t=usrs&g="+row.GroupID).then(function(){
            });
        }
    });

    pLayout.getCell("lTree").attach(treeGrid);
    pLayout.getCell("lUsers").attach(gUsers);
    pLayout.getCell("lGroups").attach(gGroups);
   
    function loadGroups() {
        gGroups.data.load("userGrMnQy.php?t=grps").then(function(){
        });
    }
    
    loadGroups();
 
</script>