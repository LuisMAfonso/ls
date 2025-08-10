<?php 
include( '../include.php' );
require_once('../header.php');
?>


<style>
   .dhx_layout-cell.dhx_layout-cell--resizable{
        margin-top: 52px;
    }

    .line-tr {
        text-decoration: line-through;
    }
 
    .cursor{    
        cursor: pointer;
    }
 
    .dhx_button--view_flat.dhx_button--color_primary {
        background-color: #0288d1;
        outline-width: 0;
        left: 50px;
        top: 10px;
    }
</style>


<script>
   

var wEditValuesForm = false;
// Layout Grid
const grid = new dhx.Grid("grid1", {
    columns: [
        { id: "UGroup", width:100, header: [{ text: "UGroup" , rowspan: 2},{text: ""},{ content: "inputFilter" }] },
        { id: "Nome",  header: [{ text: "Nome", rowspan: 2 },{text: ""},{ content: "inputFilter" }] }
    ],
    headerRowHeight: 35,
    rowHeight: 30,
    autoWidth: true,
    adjust: "true",
    css: "alternate_row cursor",
    selection: true
    //dragItem: "column"
});

const gridUsers = new dhx.Grid("grid2", {
    columns: [
        { id: "user", header: [{ text: "User" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( row.status  == "Inativo" ) {
                    return '<s>'+text+'</s>';
                } else {
                    return '<p>'+text+'</p>';
                }
            },
        },
        { id: "name", header: [{ text: "Nome" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( row.status  == "Inativo" ) {
                    return '<s>'+text+'</s>';
                } else {
                    return '<p>'+text+'</p>';
                }
            },
        },
        { id: "status", header: [{ text: "Estado" }],
            htmlEnable: true,
            template: function (text, row, col) {
                if ( text == "Inativo") {
                    return '<s>'+text+'</s>';
                } else {
                    return '<p>'+text+'</p>';
                }
            },
        }
    ],
    // rowCss:  function (row) { if (row.status == 'Inativo') return row.custom ? "line-tr" : "" },
    headerRowHeight: 35,
    rowHeight: 30,
    autoWidth: true,
    adjust: "true",
    css: "alternate_row",
    selection: true
    //dragItem: "column"
});

const gridAccess = new dhx.Grid("grid3", {
    columns: [
        { id: "icon-id", width:350, header: [{ text: "Menu" }] },
        { id: "read", header: [{ text: "Ver" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( text == "0") {
                    return  '<input type="checkbox" unchecked>';
                } else {
                    return '<input type="checkbox" checked>';
                }
            }, 
        },
        { id: "can-create", header: [{ text: "Criar" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( text == "0") {
                    return  '<input type="checkbox" unchecked>';
                } else {
                    return '<input type="checkbox" checked>';
                }
            }, 
        },
        { id: "can-modify", header: [{ text: "Alterar" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( text == "0") {
                    return  '<input type="checkbox" unchecked>';
                } else {
                    return '<input type="checkbox" checked>';
                }
            }, 
        },
        { id: "can-delete", header: [{ text: "Eliminar" }],
        htmlEnable: true,
            template: function (text, row, col) {
                if ( text == "0") {
                    return  '<input type="checkbox" unchecked>';
                } else {
                    return '<input type="checkbox" checked>';
                }
            },
        }
    ],
    headerRowHeight: 35,
    rowHeight: 30,
    autoWidth: true,
    adjust: "true",
    css: "alternate_row",
    selection: true
    //dragItem: "column"
});

// Adiciona tooltip as colunas
function rowDataTemplate(value, row, col) { 
    if(col.id == 'send' && value !== ''){
        return `Enviar dados de acesso`;
    }
}


// Load data grid
grid.data.load("usersGrMnQy.php?t=a").then(function(){
    loadToolbar();
});

// Get Onrow Click
grid.events.on("cellClick", function(row,column){

    if (column.id == "UGroup" || column.id == "Nome" ){

        let group = row.UGroup;

        gridUsers.data.load("usersGrMnQy.php?t=b&g="+ group +"").then(function(){
        });
        
        // gridAccess.data.load(dhx.ajax.get("usersGrMnQy.php?t=c&g="+ group +""));
        gridAccess.data.load("usersGrMnQy.php?t=c&g="+ group +"").then(function(){
        });
        
    }
});

grid.events.on('cellDblClick', function (row,column,e) {
    openWinDetails(row.Nome);
    wEditValuesForm = true;
});

function openWinDetails(Group){
    const dhxWindow = new dhx.Window({
        width: 650,
        height: 350,
        closable: true,
        movable: true,
        modal: true,
        title: "Detalhes Utilizadores"
    });
    
  

    form = new dhx.Form("form", {
        css: "dhx_widget--bg_white dhx_widget--bordered",
        padding: 10,
        rows: [
            // {
            //   type: "input",
            //   inputType: "number",
            //   label: "ID do Group",
            //   placeholder: "1",
            //   id: "GroupId",
            //   labelPosition: "left",
            //   labelWidth: 150,
            //   required: true,
            //   name: "GroupId",
            // },
            {
            type: "input",
            label: "Nome do Grupo",
            required: true,
            placeholder: "Admin",
            labelPosition: "left",
            labelWidth: 150,
            name: "GroupName"
            }, 
            {
            type: "button",
            name: "buttonSendUser",
            text: "Send",
            size: "medium",
            view: "flat",
            submit: true,
            color: "primary" 
            }
        ]
    });

    form.setValue({"GroupName":Group});  

    //Form submission
    form.getItem("buttonSendUser").events.on("click", function(events) {
        if ( form.validate() ) {
                // const g = form.getItem("GroupId").getValue();
                const c = form.getItem("GroupName").getValue();

                form.send("usersGrMnWr.php?t=f&c="+c+"").then(function(){
                    popupMessage("true", "Concluído com sucesso");
                    dhxWindow.hide();
                    grid.data.load("usersGrMnQy.php?t=a").then(function(){
                        loadToolbar();
                    });
            });
        }
    });

    // form.events.on("afterSend", function(){
    //     popup.hide();
    //  });


   
 

    dhxWindow.attach(form);
    dhxWindow.show();
    // Carrega info da linha
    if(id !== ''){
        const item = grid.data.getItem(id);
        if (item) {
            form.setValue(item);
        }
    }
}


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
            "id": "addToolUsers",
            "icon": "fa fa-plus",
            "value": "Adicionar",
        },
        {
            "id": "refreshmenuConfigC3Delete",
            "icon": "fa fa-trash-can",
            "value": "Remover"
        }
    ];
     
    
    toolbarGrid1 = new dhx.Toolbar("toolbarGrid1", {
        css:"dhx_widget--bordered",
        data: toolbarDataGrid1
    });

    toolbarGrid1.events.on("click", function(id,e){
        if(id == 'refreshmenuConfigC3Delete'){
            dhx.ajax.get("");
        } else {
            popupMessage('false', 'Erro ao eliminar');
        }
    });


    toolbarGrid1.events.on("click", function(id,e){
        if(id == 'addToolUsers'){
            wEditValuesForm = false;
            openWinDetails();
        }
    });
}


const layout2 = new dhx.Layout("layout2", {
    //type: "space",
    cols: [
        {
            header: "Grupos",
            rows:[
                {
                    id: "toolbarGrid", 
                    width: "content", 
                    height: "52px"
                },
                {
                    id: "C1",
                }
            ],
            width:"350px"
        },
        {
            header: "Utilizadores",
            //width:"25%",
            //collapsed:true,
            //resizable:true,
            //collapsable:true,
            rows:[ 
                {
                    id:"C2",
                    //type: "wide",
                    //collapsable:true,      
                    resizable:true   
                }
            ],
            width:"400px"
        },
        {
            header: "Acessos",
            //width:"25%",
            //collapsed:true,
            //resizable:true,
            //collapsable:true,
            rows:[ 
                {
                    id: "C3",
                    //type: "wide",
                    //collapsable:true,      
                    resizable:true   
                }
            ]
        }
    ]
});

layout.getCell("main").attach(layout2);
layout2.getCell("C1").attach(grid);
layout2.getCell("C2").attach(gridUsers);
layout2.getCell("C3").attach(gridAccess);

</script>