<?php 
include( '../include.php' );
require_once('../header.php');
?>


<style>
    .my_custom_row_al {
        background: coral;
    }
</style>

<script>

var wEditValuesForm = false;
// Layout Grid
const grid = new dhx.Grid("grid", {
    columns: [
        { id: "Spid", width:70, header: [{ text: "Spid" , rowspan: 2},{text: ""},{ content: "inputFilter" }] },
        { id: "Status", header: [{ text: "Status", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Hostname", header: [{ text: "Hostname", rowspan: 2 },{text: ""},{ content: "selectFilter" }] },
        { id: "login", header: [{ text: "Login", rowspan: 2 },{text: ""},{ content: "selectFilter" }] },
        { id: "CPU", width:130, header: [{ text: "CPU", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Bloking", width:80,header: [{ text: "Bloking", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Wait_ms", width:80,header: [{ text: "Wait_ms", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Start_time", header: [{ text: "Start Time", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Command", header: [{ text: "Command", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "ProgName", header: [{ text: "Prog.Name", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "Text", minWidth:400, header: [{ text: "Query", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "kill",width:70, align:"center", header: [{ text: "Kill", rowspan: 2 ,align:"center"},{text: ""},{ content: "inputFilter" }],htmlEnable: true }
    ],
    headerRowHeight: 35,
    rowHeight: 30,
    autoWidth: true,
    adjust: "true",
    //css: "alternate_row",
    selection: true,
    rowCss: function (row) { console.log(row.custom); return row.custom ? "my_custom_row_al" : "" },
    //dragItem: "column"
});


// Load data grid
grid.data.load("sysLocksQy.php?t=a").then(function(){
    loadToolbar();
});


// Load toolbar
function loadToolbar(){
    const pagination = new dhx.Pagination("pagination", {
        css: "dhx_widget--bordered dhx_widget--no-border_top",
        align: "center",
        data: grid.data,
        pageSize: 50
    });

    form = new dhx.Form(null, {
        align: "center",
        padding: 0,
        rows: [
            {
                id: "pagCount",
                type: "button",
                view: "link",
                text: "Records "+grid.data.getLength(),
                circle: true
            }
        ]
    });

    layoutPagination.getCell("pagTab1").attach(pagination);
    layoutPagination.getCell("pagTab2").attach(form);
    layout.getCell("pagination").attach(layoutPagination);

    //Toolbar
    const toolbarDataGrid1 = [
        {
            "id": "refreshBlockSys",
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
        if(id == 'refreshBlockSys'){
            grid.data.load("sysLocksQy.php?t=a").then(function(){
                loadToolbar();
            });
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
        }
    ]
});

layout.getCell("main").attach(layout2);
layout2.getCell("С1").attach(grid);

grid.events.on("cellClick", function(row,column,e){
    if(column['id'] == 'kill'){
        dhx.confirm({
            text: 'Confirma matar processo '+row['Spid']+' ?',
            buttonsAlignment: "center",
            buttons: ["Cancelar", "Sim"]
        }).then(function(a){
            if(a){
                dhx.ajax.get("sysLocksWr.php?t=d&r="+row['Spid']).then(function (data) {
                }); 
            } 
        });
    }
});


//grid.addRowCss(0, "my_сustom_сlass");



</script>