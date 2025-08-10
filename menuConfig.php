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
    var tbUsers, fOptions, gGroups, cbTree;
    var wSelected = 0;
    var treeGrid = '';

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { id: "lTree", html: "1", width: "500px"},
            { type: "wide",
              rows: [
                { 
                    type: "none",
                    rows: [ 
                        { id: "hOptions", height: "45px",  },
                        { id: "lOptions", html: "" },
                    ],
                    height: "395px"
                },
                { 
                    type: "none",
                    rows: [ 
                        { id: "hGroups", height: "45px",  },
                        { id: "lGroups", html: "" },
                    ]
                },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    tbOptions = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbOptions.data.load("toolbarsQy.php?t=m_aed").then(function(){
        tbOptions.disable(['edit', 'delete']);
    });;
    tbOptions.events.on("click", function(id,e){
        console.log(id);
//        if ( id == 'edit' ) { addEditUser(); }
//        if ( id == 'add' )  { wSelected = 0; addEditUser(); }
    });
    pLayout.getCell("hOptions").attach(tbOptions);

    tbGroups = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbGroups.data.load("toolbarsQy.php?t=g_ad").then(function(){
        tbGroups.disable(['edit', 'delete']);
    });;
    tbGroups.events.on("click", function(id,e){
        console.log(id);
//        if ( id == 'edit' ) { addEditUser(); }
//        if ( id == 'add' )  { wSelected = 0; addEditUser(); }
    });
    pLayout.getCell("hGroups").attach(tbGroups);

    treeGrid = new dhx.Grid(null, {
        type: "tree",
        columns: [
            { id: "name", header: [{ text: "Menu" }], gravity: 1.5, htmlEnable: true},
            { id: "OptId", header: [{ text: "OptId" }], gravity: 1.5, hidden: true},
            { id: "Descricao", header: [{ text: "Descricao" }], gravity: 1.5, hidden: true},
            { id: "DescricaoPai", header: [{ text: "DescricaoPai" }], gravity: 1.5, hidden: true},
            { id: "NomeFicheiro", header: [{ text: "NomeFicheiro" }], gravity: 1.5, hidden: true},
            { id: "Tipo", header: [{ text: "Tipo" }], gravity: 1.5, hidden: true},
            { id: "Pos", header: [{ text: "Pos" }], gravity: 1.5, hidden: true},
            { id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, hidden: true}
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: true
    });
    treeGrid.data.load("menuConfigQy.php?t=a").then(function(){
        treeGrid.collapseAll();
    });
    treeGrid.events.on("cellClick", function(row,column,e){
        if(row.id !== ''){
            fOptions.clear();
            const item = treeGrid.data.getItem(row.id);
            if (item) {
                fOptions.setValue(item);
            }
            // Load data grid
            gGroups.data.load("menuConfigQy.php?t=b&c="+row.id).then(function(){
            });
        }
    });


    fOptions = new dhx.Form(null, {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 5,
      rows: [
        { type: "input", label: "OptId", labelPosition: "left", labelWidth: 100, name: "OptId" },
        { type: "input", label: "Descrição", labelPosition: "left", labelWidth: 100, name: "Descricao" },
        { type: "combo", name: "DescricaoPai", label: "Descrição Pai", labelPosition: "left", labelWidth: 100, readonly: true, data: [ ] },
        { type: "input", label: "Icon", labelPosition: "left", labelWidth: 100, name: "Icon" },
        { type: "input", label: "Nome Ficheiro", labelPosition: "left", labelWidth: 100, name: "NomeFicheiro" },
        { type: "combo", name: "Tipo", label: "Tipo", labelPosition: "left", labelWidth: 100, readonly: true, data: [ { value: "Menu", id: "m" }, { value: "Programa", id: "p" } ] },
        { type: "input", label: "Pos", labelPosition: "left", labelWidth: 100, name: "Pos" }
      ]
    });
    cbTrans = fOptions.getItem("DescricaoPai").getWidget();
    cbTrans.data.load("menuConfigQy.php?t=getParent");


    gGroups = new dhx.Grid(null, {
        columns: [
            { id: "GroupID", width:110, header: [{ text: "Group ID" , rowspan: 2},{text: ""}] },
            { id: "GrupoDesc", header: [{ text: "Grupo Desc", rowspan: 2 },{text: ""}] }
        ],
        headerRowHeight:20,
        autoWidth: true,
        adjust: "true",
        css: "alternate_row",
        selection: true
    });

    pLayout.getCell("lTree").attach(treeGrid);
    pLayout.getCell("lOptions").attach(fOptions);
    pLayout.getCell("lGroups").attach(gGroups);
   

    

 
</script>