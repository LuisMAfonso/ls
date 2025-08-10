<?php 

include( '../include.php' );
include( "../logedCheck.php" );

require_once('header.php');

?>

<div id="layout" style="height: 100vh;"></div>

<?php
require_once('layout.php');
?>

<script>

var pLayout;
var tbStaff, pGridExpenses, dsExpenses;
var formExp, dsTimes;
var cbProject, cbExpType;
var wSelected = 0;

pLayout = new dhx.Layout(null, {
    type: "line",
    rows: [
        { type: "line",
            rows: [ 
                { id: "lTbStaff", html: "", height: "55px" },
                { id: "staff", html: "" },
            ]
        },
        { id: "showDet", html: "", height: "370px"  },
    ]
});
mainLayout.getCell("workplace").attach(pLayout);

loadPermission();
dsExpenses = new dhx.DataCollection();
dsTimes = new dhx.DataCollection();
loadUsers();

function loadUsers() {
    dsExpenses.removeAll();
    dsExpenses.load("readDB.php?t=expenses").then(function(){
//          console.log("done users read");
    });
};
tbStaff = new dhx.Toolbar(null, {
    css: "dhx_widget--bordered"
});
tbStaff.data.load("toolbarsQy.php?t=ex_search").then(function(){
    tbStaff.disable(['edit','delete']);
});
tbStaff.events.on("input", function (event, arguments) {
    console.log(arguments);
    const value = arguments.toString().toLowerCase();
    pGridExpenses.data.filter(obj => {
        return Object.values(obj).some(item => 
            item.toString().toLowerCase().includes(value)
        );
    });
});
tbStaff.events.on("click", function(id,e){
    console.log(id); 
    if ( id == 'edit' ) { addEditExpense(); }
    if ( id == 'add' )  { wSelected = 0; addEditExpense(); }
});
pLayout.getCell("lTbStaff").attach(tbStaff);

pGridExpenses = new dhx.Grid(null, {
    columns: [
        { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 100, id: "expDate", header: [{ text: "Date" }], autoWidth: true, align: "left" },
        { width: 0, minWidth: 100, id: "expTypeName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
        { width: 75, id: "expValue", header: [{ text: "Value" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
    ],
    selection:"row",
    adjust: "true", 
    data: dsExpenses
});
pGridExpenses.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelected = row.Id;
    tbStaff.enable(['edit']);
    showExpense();
});

var f_expense = {        
        css: "dhx_widget--bordered",
        padding: 10,
        rows: [
            { type: "input", name: "Id", label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
            { type: "input", name: "expDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", readOnly: true },
            { id: "expName", name: "expName", type: "input", label: "Exp.Type", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
            { id: "projName", name: "projName", type: "input", label: "Project", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
            { type: "input", name: "expValue", label: "Value", labelPosition: "left", labelWidth: "50px", width: "200px", validation: "numeric", readOnly: true,  },
            { type: "textarea", name: "workDone", required: false, label: "Reason", labelPosition: "left", labelWidth: "50px" },
            { type: "avatar", name: "avatar", label: "Invoice", labelPosition: "left", labelWidth: "50px", width: "200px", target: "uplExpense.php", value: "", fieldName: "file", readOnly: true, },
        ]
};
formExp = new dhx.Form(null, f_expense);

pLayout.getCell("staff").attach(pGridExpenses);
pLayout.getCell("showDet").attach(formExp);

function showExpense() {
    dhx.ajax.get("readDB.php?t=exp&id="+wSelected).then(function (data) {
        console.log(data);
        var obj = JSON.parse(data);
        console.log(obj);
        formExp.setValue(obj);
    }).catch(function (err) {
        console.log(err);
    });   
}
function addEditExpense(argument) {
    const dhxWindow = new dhx.Window({
        width: 370,
        height: 530,
        closable: true,
        movable: true,
        modal: true,
        title: "Expense registration"
    });
    const form = new dhx.Form(null, {
        css: "dhx_widget--bordered",
        padding: 10,
        width: 330,
        rows: [
            { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
            { type: "datepicker", name: "expDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", dateFormat: "%Y-%m-%d" },
            { id: "expId", name: "expId", type: "combo", label: "Exp.Type", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
            { id: "projId", name: "projId", type: "combo", label: "Project", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
            { type: "input", name: "expValue", label: "Value", labelPosition: "left", labelWidth: "50px", width: "200px", validation: "numeric",  },
            { type: "textarea", name: "workDone", required: false, label: "Reason", labelPosition: "left", labelWidth: "50px" },
            { type: "avatar", name: "avatar", label: "Invoice", labelPosition: "left", labelWidth: "50px", width: "200px", required: true, value: "", fieldName: "file", },
            {
                align: "end",
                cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                ]
            }
        ]
    }); 
    cbExpType = form.getItem("expId").getWidget();
    cbProject = form.getItem("projId").getWidget();
    cbProject.data.load("../commonQy.php?t=prjs").then(function(){
        dhx.ajax.get("readDB.php?t=exp&id="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            form.setValue(obj);
            cbProject.setValue(obj.projId);
            cbExpType.data.load("../commonQy.php?t=fam&s=x").then(function(){
                 cbExpType.setValue(obj.expId);
            });
        }).catch(function (err) {
            console.log(err);
        });
    });
    form.events.on("click", function(name,e){
        console.log(name+" "+e);
        if ( name == 'cancel' ) {
            const config = {
                header: "Expense registration ",
                text: "Confirm cancelation?",
                buttons: ["no", "yes"],
                buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    wSelected = 0;
                    form.destructor();
                    dhxWindow.destructor();
                }
            });         
        };
        if ( name == 'send' ) {
            const config = {
                header: "Expense registration ",
                text: "Confirm expense registration?",
                buttons: ["no", "yes"],
                buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    const send = form.send("writeDB.php?t=exp", "POST").then(function(data){
//                            message = JSON.parse(data);
//                        console.log(data);
                        wSelected = 0;
                        form.destructor();
                        dhxWindow.destructor();
                    });
                };
            });         
        };
    });

    dhxWindow.attach(form);
    dhxWindow.show();

}

function loadPermission() {
    dhx.ajax.get("../menuQy.php?t=perm&p=regTimes").then(function (data) {
        console.log(data);
        var obj = JSON.parse(data);
        console.log(obj);
        canRead = obj.canRead;
        canCreate = obj.canCreate; 
        canModify = obj.canModify;             
        canDelete = obj.canDelete;             
    }).catch(function (err) {
        console.log(err);
    });
};

</script>
