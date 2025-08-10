<?php 
include( '../include.php' );
require_once('../header.php');
?>




<script>

var wEditValuesForm = false;
// Layout TreeGrid
const treeGrid = new dhx.TreeGrid("treegrid", {
    columns: [
        { id: "name", header: [{ text: "Name" }], gravity: 1.5, htmlEnable: true},
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
// Load data treegrid
treeGrid.data.load("menuConfigQy.php?t=a").then(function(){
    treeGrid.collapseAll();
    loadToolbar();
    loadToolbarC2();
    loadToolbarC3();
});


// Layout Grid 2
const gridC3 = new dhx.Grid("grid", {
    columns: [
        { id: "GroupID", width:110, header: [{ text: "Group ID" , rowspan: 2},{text: ""}] },
        { id: "GrupoDesc", header: [{ text: "Grupo Desc", rowspan: 2 },{text: ""}] }
    ],
    headerRowHeight: 35,
    rowHeight: 30,
    autoWidth: true,
    adjust: "true",
    css: "alternate_row",
    selection: true
    //dragItem: "column"
});


// Form
const form = new dhx.Form("form", {
  css: "dhx_widget--bg_white dhx_widget--bordered",
  padding: 10,
  rows: [
    {
      type: "input",
      label: "OptId",
      //icon: "dxi dxi-magnify",
      //placeholder: "John Doe",
      labelPosition: "left",
      labelWidth: 100,
      name: "OptId"
    },
    {
      type: "input",
      label: "Descrição",
      //placeholder: "jd@mail.name",
      labelPosition: "left",
      labelWidth: 100,
      name: "Descricao"
    },
    {
        type: "combo",
        name: "DescricaoPai",
        label: "Descrição Pai",
        labelPosition: "left",
        labelWidth: 100,
        readonly: true,
        data: [
        ]
    },
    {
      type: "input",
      label: "Icon",
      //placeholder: "jd@mail.name",
      labelPosition: "left",
      labelWidth: 100,
      name: "Icon"
    },
    {
      type: "input",
      label: "Nome Ficheiro",
      //placeholder: "jd@mail.name",
      labelPosition: "left",
      labelWidth: 100,
      name: "NomeFicheiro"
    },
    {
        type: "combo",
        name: "Tipo",
        label: "Tipo",
        labelPosition: "left",
        labelWidth: 100,
        readonly: true,
        data: [
            { value: "Menu", id: "m" },
            { value: "Programa", id: "p" }
        ]
    },
    {
      type: "input",
      label: "Pos",
      //placeholder: "jd@mail.name",
      labelPosition: "left",
      labelWidth: 100,
      name: "Pos"
    }
  ]
});

var combo = form.getItem("DescricaoPai").getWidget();
combo.data.load("menuConfigQy.php?t=getDescricaoPai");



// Load toolbars
function loadToolbar(){

    //Toolbar
    const toolbarDataGrid1 = [
       {
            "id": "refreshmenuConfig",
            "icon": "fa fa-arrows-rotate",
            "value": "Atualizar"
        }
    ];
     
    toolbarGrid1 = new dhx.Toolbar("toolbarGrid1", {
        css:"dhx_widget--bordered",
        data: toolbarDataGrid1
    });

    layout2.getCell("toolbarGrid").attach(toolbarGrid1);

    toolbarGrid1.events.on("click", function(id,e){
        if(id == 'refreshmenuConfig'){
            treeGrid.data.load("menuConfigQy.php?t=a").then(function(){
                treeGrid.collapseAll();
                //loadToolbar();
            });
        }
    });
}
function loadToolbarC2(){

    //Toolbar
    const toolbarDataC2 = [
        {
            "id": "refreshmenuConfigC2Add",
            "icon": "fa fa-plus",                   
            "value": "Adicionar"
        },
        {
            "id": "refreshmenuConfigC2Save",
            "icon": "fa fa-floppy-disk",
            "value": "Guardar"
        },
        {
            "type": "separator"
        },
        {
            "id": "refreshmenuConfigC2Delete",
            "icon": "fa fa-trash-can",
            "value": "Remover"
        }
    ];
     
    toolbarC2 = new dhx.Toolbar("toolbarC2", {
        css:"dhx_widget--bordered",
        data: toolbarDataC2
    });

    layout2.getCell("toolbarC2").attach(toolbarC2);

    toolbarC2.events.on("click", function(id,e){
        if(id == 'refreshmenuConfigSave'){
        }
    });
}
function loadToolbarC3(){

    //Toolbar
    const toolbarDataC3 = [
        {
            "id": "refreshmenuConfigC3Add",
            "icon": "fa fa-plus",
            "value": "Adicionar"
        },
        {
            "type": "separator"
        },
        {
            "id": "refreshmenuConfigC3Delete",
            "icon": "fa fa-trash-can",
            "value": "Remover"
        }
    ];
     
    toolbarC3 = new dhx.Toolbar("toolbarC2", {
        css:"dhx_widget--bordered",
        data: toolbarDataC3
    });

    layout2.getCell("toolbarC3").attach(toolbarC3);

    toolbarC3.events.on("click", function(id,e){
        if(id == 'refreshmenuConfigC3Add'){
            popupMessage('true', 'Adicionado com sucesso');
        }
        else if(id == 'refreshmenuConfigC3Delete'){
            popupMessage('false', 'Erro ao eliminar');
        }
    });
}


const layout2 = new dhx.Layout("layout2", {
    //type: "space",
    cols: [
        {
            rows:[
                {
                    id: "toolbarGrid", 
                    width: "content", 
                    height: "52px"
                },
                {
                    id: "С1",
                }
            ]
        },
        {
            header: "",
            width:"35%",
            id: "Q2",
            type: "wide",
            //collapsed:true,
            //resizable:true,
            //collapsable:true,
            rows:[
                {
                    id: "toolbarC2", 
                    width: "content", 
                    height: "52px"
                }, 
                {
                    id: "С2",
                    //type: "wide",
                    //collapsable:true,      
                    height: "50%"
                    //resizable:true   
                },
                {
                    id: "toolbarC3", 
                    width: "content", 
                    height: "52px"
                }, 
                {
                    id: "С3",
                    //html: "3",
                    //type: "wide",
                    //collapsable:true,     
                    height: "50%"
                    //resizable:true   
                }
            ]
        }
    ]
});

layout2.getCell("С1").attach(treeGrid);
layout2.getCell("С2").attach(form);
layout2.getCell("С3").attach(gridC3);
layout.getCell("main").attach(layout2);

layout.removeCell('pagination');

treeGrid.events.on("cellClick", function(row,column,e){
    if(row['id'] !== ''){
        // Carrega info da linha
        form.clear();
        const item = treeGrid.data.getItem(row['id']);
        if (item) {
            form.setValue(item);
        }
        // Load data grid
        gridC3.data.load("menuConfigQy.php?t=b&c="+row['id']).then(function(){
        });
    }
});


</script>