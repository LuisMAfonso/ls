<?php 
include( '../include.php' );
require_once('../header.php');
?>




<script>

var wEditValuesForm = false;
// Layout Grid
const grid = new dhx.Grid("grid", {
    columns: [
        { id: "UserID", width:130, header: [{ text: "Cod.Utilizador" , rowspan: 2},{text: ""},{ content: "inputFilter" }] },
        { id: "UserName", header: [{ text: "Nome", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "IsAdminG", width:100,header: [{ text: "Admin", rowspan: 2 },{text: ""},{ content: "selectFilter" }] },
        { id: "Employee", width:130, header: [{ text: "Numero", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "email", header: [{ text: "Email", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "stationG", header: [{ text: "Estação", rowspan: 2 },{text: ""},{ content: "inputFilter" }] },
        { id: "homeDepositG", width:130, header: [{ text: "Home Deposit", rowspan: 2 },{text: ""},{ content: "selectFilter" }] },
        { id: "LockedG", width:130, header: [{ text: "Estado", rowspan: 2 },{text: ""},{ content: "selectFilter" }], htmlEnable: true},
        { id: "send", width:50, header: [{ text: "", rowspan: 2 },{text: ""},{ content: "inputFilter" }], htmlEnable: true, tooltipTemplate: rowDataTemplate },
        { id: "IsAdmin", width:130, header: [{ text: "IsAdmin", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true},
        { id: "homeDeposit", width:130, header: [{ text: "homeDeposit", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true},
        { id: "reset_pass", width:130, header: [{ text: "reset_pass", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true},
        { id: "Locked", width:130, header: [{ text: "Locked", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true},
        { id: "station", width:130, header: [{ text: "station", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true},
        { id: "homeDepositPIN", width:130, header: [{ text: "homeDepositPIN", rowspan: 2 },{text: ""},{ content: "selectFilter" }], hidden: true}
    ],
    headerRowHeight: 35,
    rowHeight: 25,
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
grid.data.load("usersMainQy.php?t=i").then(function(){
    loadToolbar();
});

grid.events.on('cellDblClick', function (row,column,e) {
    openWinDetails(row.id);
    wEditValuesForm = true;
});

function openWinDetails(id){
    const dhxWindow = new dhx.Window({
        width: 650,
        height: 700,
        closable: true,
        movable: true,
        modal: true,
        title: "Detalhes Utilizadores"
    });

   form = new dhx.Form("form", {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 10,
      rows: [
        {
          id: "id",
          type: "input",
          name: "id",
          hidden: "true"
        },
        {
          type: "input",
          label: "Cod. Utilizador",
          //icon: "dxi dxi-magnify",
          placeholder: "jsilva",
          name: "UserID",
          id: "UserID",
          labelPosition: "left",
          labelWidth: 150,
          required: true,
          validation: function(value) {
             checkUserExist(value);
          }
        },
        {
          type: "input",
          label: "Nome",
          required: true,
          placeholder: "João Silva",
          labelPosition: "left",
          labelWidth: 150,
          name: "UserName"
        },
        {
          type: "input",
          label: "Nr. Colaborador",
          labelPosition: "left",
          labelWidth: 150,
          name: "Employee"
        },
        {
          type: "input",
          inputType: "password",
          label: "Password",
          placeholder: "********",
          labelPosition: "left",
          labelWidth: 150,
          name: "UserPass"
        },
        {
          type: "input",
          inputType: "password",
          label: "Confirma Password",
          placeholder: "********",
          labelPosition: "left",
          labelWidth: 150,
          name: "UserPassValida"
        },
        {
          type: "checkbox",
          text: "Forçar Reset PW",
          value: "checkboxvalue",
          labelPosition: "left",
          labelWidth: 150,
          id: "resetPass",
          name: "reset_pass"
        },    
        {
          type: "combo",
          name: "Station",
          label: "Estação",
          labelPosition: "left",
          labelWidth: 150,
          readonly: true,
          required: true,
          //labelPosition: "left",
          //labelWidth: "120px",
          data: [
          ]
        },
        {
          type: "input",
          label: "Email",
          placeholder: "jd@mail.name",
          labelPosition: "left",
          labelWidth: 150,
          name: "email"
        },
        {
          type: "checkbox",
          text: "Bloqueado",
          value: "checkboxvalue",
          id: "Locked",
          labelPosition: "left",
          labelWidth: 150,
          name: "Locked"
        },  
        {
          type: "checkbox",
          text: "Administrador",
          value: "checkboxvalue",
          id: "admin",
          labelPosition: "left",
          labelWidth: 150,
          name: "IsAdmin"
        },  
        {
          type: "checkbox",
          text: "Home Deposit",
          value: "checkboxvalue",
          id: "homeDeposit",
          labelPosition: "left",
          labelWidth: 150,
          name: "homeDeposit"
        },  
        {
          type: "input",
          label: "HD PIN",
          labelPosition: "left",
          labelWidth: 150,
          name: "homeDepositPIN"
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
    

    //Form submission
    form.getItem("buttonSendUser").events.on("click", function(events) {
        if ( form.validate() ) {
            form.send("usersMainWr.php?t=a","POST");  
        }
    });


    var combo = form.getItem("Station").getWidget();
    combo.data.load("usersMainQy.php?t=getStations");

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

function checkUserExist(value) {
    if(!wEditValuesForm){
        dhx.ajax.get("usersMainQy.php?t=checkUserExist&r="+value).then(function (data) {
            const propertyNames = JSON.parse(data);
            if(propertyNames.result == 'true'){
                form.getItem("UserID").focus();
                form.getItem("UserID").setValue('');
                return false;
                dhx.alert({
                    header: "Validação",
                    text: "O utilizador <b>"+value+"</b> já existe.",
                    buttonsAlignment: "center",
                    buttons: ["ok"],
                });
            }
            else{
                return true;
            }
        });
    }
    else{
        return true;
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
            "value": "Adicionar"
        }
    ];
     
    toolbarGrid1 = new dhx.Toolbar("toolbarGrid1", {
        css:"dhx_widget--bordered",
        data: toolbarDataGrid1
    });

    layout2.getCell("toolbarGrid").attach(toolbarGrid1);

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
            header: "Grupos Acessos",
            width:"25%",
            collapsed:true,
            //resizable:true,
            collapsable:true,
            rows:[ 
                {
                    id: "С2",
                    //type: "wide",
                    //collapsable:true,      
                    resizable:true   
                }
            ]
        }
    ]
});

layout.getCell("main").attach(layout2);
layout2.getCell("С1").attach(grid);


</script>