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
var tbSurvey, pGridSurvey, dsSurveys;
var formExp, dsTimes;
var cbProject, cbExpType;
var wSelected = 0;

pLayout = new dhx.Layout(null, {
    type: "line",
    rows: [
        { type: "line",
            height: "200px",
            rows: [ 
                { id: "ltbSurvey", html: "", height: "55px" },
                { id: "staff", html: "", },
            ]
        },
        { id: "showDet", html: ""  },
    ]
});
mainLayout.getCell("workplace").attach(pLayout);

loadPermission();
dsSurveys = new dhx.DataCollection();
dsTimes = new dhx.DataCollection();
loadSurveys();

function loadSurveys() {
    dsSurveys.removeAll();
    dsSurveys.load("readDB.php?t=surveys").then(function(){
//          console.log("done users read");
    });
};
tbSurvey = new dhx.Toolbar(null, {
    css: "dhx_widget--bordered"
});
tbSurvey.data.load("toolbarsQy.php?t=ex_search").then(function(){
    tbSurvey.disable(['edit','delete']);
});
tbSurvey.events.on("input", function (event, arguments) {
    console.log(arguments);
    const value = arguments.toString().toLowerCase();
    pGridSurvey.data.filter(obj => {
        return Object.values(obj).some(item => 
            item.toString().toLowerCase().includes(value)
        );
    });
});
tbSurvey.events.on("click", function(id,e){
    console.log(id); 
    if ( id == 'edit' ) { addEditSurvey(); }
    if ( id == 'add' )  { wSelected = 0; addEditSurvey(); }
});
pLayout.getCell("ltbSurvey").attach(tbSurvey);

pGridSurvey = new dhx.Grid(null, {
    columns: [
        { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 100, id: "survDate", header: [{ text: "Date" }], autoWidth: true, align: "left" },
        { width: 0, minWidth: 100, id: "projName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
    ],
    selection:"row",
    adjust: "true", 
    data: dsSurveys
});
pGridSurvey.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelected = row.Id;
    tbSurvey.enable(['edit']);
    showSurvey();
});

var f_survey = {        
        css: "dhx_widget--bordered",
        padding: 10,
        rows: [
            { type: "input", name: "Id", label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true 
            },
            { type: "input", id: "survDate", name: "survDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", readOnly: true 
            },
            { type: "input", id: "projName", name: "projName", label: "Project", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
            },
            { type: "input", name: "sizeArea", label: "Size/Area", labelPosition: "left", labelWidth: "50px", width: "200px", readOnly: true,  
            },
            { type: "textarea", id: "locAddress", name: "locAddress", label: "Location", labelPosition: "top", labelWidth: "50px", readOnly: true 
            },
            { id: "access", name: "access", type: "input", label: "Access", labelWidth: "50px", width: "290px",  labelPosition: "left", maxlength: "50", gravity: "false" , readOnly: true
            },
            { type: "radioGroup", name: "projType", label: "Type", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "b2b", value: "b2b", }, { type: "radioButton", text: "b2c", value: "b2c", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "street", label: "Street", labelPosition: "left", labelWidth: "90px", width: "200px", options: { rows: [ { type: "radioButton", text: "narrow", value: "narrow", }, { type: "radioButton", text: "wide", value: "wide", }, { type: "radioButton", text: "difficult", value: "difficult", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "territory", label: "Territory", labelPosition: "left", labelWidth: "90px", width: "200px", options: { rows: [ { type: "radioButton", text: "private", value: "private", }, { type: "radioButton", text: "public", value: "public", }, { type: "radioButton", text: "constractions", value: "constractions", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "site", label: "Site", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "level", value: "level", }, { type: "radioButton", text: "slope", value: "slope", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "existingSoil", label: "Existing soil", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "good", value: "good", }, { type: "radioButton", text: "rubish", value: "rubish", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "sizeArea", label: "Stairs", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "equipment", label: "Equipment", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "newSoil", label: "New Soil", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "rubbishRemove", label: "Rubbish remove", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "fiskar", label: "Fiskar", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "toilets", label: "Toilets", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            }, 
            { type: "radioGroup", name: "pruning", label: "Tree pruning", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "barrier", label: "Barrier", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "fence", label: "Fence", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { id: "duration", name: "duration", type: "input", label: "Duration (days)", labelWidth: "90px", width: "180px", inputType: "number", labelPosition: "left", maxlength: "50", gravity: "false" , readOnly: true
            },
            { type: "radioGroup", name: "parkPaid", label: "Parking", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Paid", value: "Paid", }, { type: "radioButton", text: "Free", value: "Free", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "robot", label: "Is a robot", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "irrigation", label: "irrigation", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "New", value: "New", }, { type: "radioButton", text: "Old", value: "Old", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "waterConn", label: "Water Connec.after work", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "transpOld", label: "Transpl.old plants", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "moveTerritory", label: "Movement (need boards)", labelPosition: "left", labelWidth: "160px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "hourRestr", label: "Hours restrictions", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            },
            { type: "radioGroup", name: "lightCables", label: "Are there lighting cables", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } , readOnly: true
            }, 
            { type: "textarea", name: "notes", label: "Notes", labelPosition: "top", labelWidth: "50px", maxlength: "1000" , readOnly: true
            },
        ] 
};
formExp = new dhx.Form(null, f_survey);
formExp.forEach(function(item, index, array) {
    if ( item.config.type == 'radiogroup' ) {
        formExp.getItem(item.config.name).events.on("beforeChange", function(value) {
            return false;
        });
    }
//    console.log("control: ", item);
});
pLayout.getCell("staff").attach(pGridSurvey);
pLayout.getCell("showDet").attach(formExp);

function showSurvey() {
    dhx.ajax.get("readDB.php?t=survey&id="+wSelected).then(function (data) {
        console.log(data);
        var obj = JSON.parse(data);
//        console.log(obj);
        formExp.setValue(obj);
    }).catch(function (err) {
        console.log(err);
    });   
}
function addEditSurvey(argument) {
    const dhxWindow = new dhx.Window({
        width: 370,
        height: 530,
        closable: true,
        movable: true,
        modal: true,
        title: "Expense registration"
    }); 
    const form = new dhx.Form(null, {
        padding: 10,
        rows: [
            { type: "input", name: "Id", label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
            { type: "datepicker", name: "survDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", dateFormat: "%Y-%m-%d", required: true },
            { type: "combo", id: "survProj", name: "survProj", label: "Project", labelWidth: "50px", width: "280px",  labelPosition: "left", maxlength: "200", gravity: "false", required: true,  },
            { type: "input", name: "sizeArea", label: "Size/Area", labelPosition: "left", labelWidth: "50px", width: "200px",  },
            { type: "textarea", name: "locAddress", label: "Location", labelPosition: "top", labelWidth: "50px" },
            { id: "access", name: "access", type: "input", label: "Access", labelWidth: "50px", width: "290px",  labelPosition: "left", maxlength: "50", gravity: "false" 
            },
            { type: "radioGroup", name: "projType", label: "Type", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "b2b", value: "b2b", }, { type: "radioButton", text: "b2c", value: "b2c", }, ] } 
            },
            { type: "radioGroup", name: "street", label: "Street", labelPosition: "left", labelWidth: "90px", width: "200px", options: { rows: [ { type: "radioButton", text: "narrow", value: "narrow", }, { type: "radioButton", text: "wide", value: "wide", }, { type: "radioButton", text: "difficult", value: "difficult", }, ] } 
            },
            { type: "radioGroup", name: "territory", label: "Territory", labelPosition: "left", labelWidth: "90px", width: "200px", options: { rows: [ { type: "radioButton", text: "private", value: "private", }, { type: "radioButton", text: "public", value: "public", }, { type: "radioButton", text: "constractions", value: "constractions", }, ] } 
            },
            { type: "radioGroup", name: "site", label: "Site", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "level", value: "level", }, { type: "radioButton", text: "slope", value: "slope", }, ] } 
            },
            { type: "radioGroup", name: "existingSoil", label: "Existing soil", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "good", value: "good", }, { type: "radioButton", text: "rubish", value: "rubish", }, ] } 
            },
            { type: "radioGroup", name: "sizeArea", label: "Stairs", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "equipment", label: "Equipment", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "newSoil", label: "New Soil", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "rubbishRemove", label: "Rubbish remove", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "fiskar", label: "Fiskar", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "toilets", label: "Toilets", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            }, 
            { type: "radioGroup", name: "pruning", label: "Tree pruning", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "barrier", label: "Barrier", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "fence", label: "Fence", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { id: "duration", name: "duration", type: "input", label: "Duration (days)", labelWidth: "90px", width: "180px", inputType: "number", labelPosition: "left", maxlength: "50", gravity: "false" 
            },
            { type: "radioGroup", name: "parkPaid", label: "Parking", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Paid", value: "Paid", }, { type: "radioButton", text: "Free", value: "Free", }, ] } 
            },
            { type: "radioGroup", name: "robot", label: "Is a robot", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "irrigation", label: "irrigation", labelPosition: "left", labelWidth: "90px", width: "200px", options: { cols: [ { type: "radioButton", text: "New", value: "New", }, { type: "radioButton", text: "Old", value: "Old", }, ] } 
            },
            { type: "radioGroup", name: "waterConn", label: "Water Connec.after work", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "transpOld", label: "Transpl.old plants", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "moveTerritory", label: "Movement (need boards)", labelPosition: "left", labelWidth: "160px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "hourRestr", label: "Hours restrictions", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            },
            { type: "radioGroup", name: "lightCables", label: "Are there lighting cables", labelPosition: "left", labelWidth: "160px", width: "200px", options: { cols: [ { type: "radioButton", text: "Yes", value: "y", }, { type: "radioButton", text: "No", value: "n", }, ] } 
            }, 
            { type: "textarea", name: "notes", label: "Notes", labelPosition: "top", labelWidth: "50px", maxlength: "1000" 
            },
            {
                align: "end",
                cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                ]
            }
        ]
    }); 
    cbProject = form.getItem("survProj").getWidget();
    cbProject.data.load("../commonQy.php?t=prjs").then(function(){
        dhx.ajax.get("readDB.php?t=survey&id="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            form.setValue(obj);
            cbProject.setValue(obj.projId);
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
                    const send = form.send("writeDB.php?t=surv", "POST").then(function(data){
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
