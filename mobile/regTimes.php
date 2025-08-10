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
var tbStaff, pGridStaff, dsStaff;
var timesGrid, dsTimes;
var wSelTime = 0;

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
dsStaff = new dhx.DataCollection();
dsTimes = new dhx.DataCollection();
loadUsers();

function loadUsers() {
    dsStaff.removeAll();
    dsStaff.load("readDB.php?t=staff").then(function(){
//          console.log("done users read");
    });
};
tbStaff = new dhx.Toolbar(null, {
    css: "dhx_widget--bordered"
});
tbStaff.data.load("toolbarsQy.php?t=tm_search").then(function(){
    tbStaff.disable(['add','edit','delete']);
});
tbStaff.events.on("input", function (event, arguments) {
    console.log(arguments);
    const value = arguments.toString().toLowerCase();
    pGridStaff.data.filter(obj => {
        return Object.values(obj).some(item => 
            item.toString().toLowerCase().includes(value)
        );
    });
});
tbStaff.events.on("click", function(id,e){
    console.log(id);
    if ( id == 'edit' ) { addEditTime(); }
    if ( id == 'add' )  { wSelTime = 0; addEditTime(); }
});
pLayout.getCell("lTbStaff").attach(tbStaff);

pGridStaff = new dhx.Grid(null, {
    columns: [
        { width: 30, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 50, id: "staffNumber", header: [{ text: "#" }], autoWidth: true },
        { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
    ],
    selection:"row",
    adjust: "true", 
    data: dsStaff
});
pGridStaff.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelected = row.Id;
    tbStaff.enable(['add']);
    loadTimes();

});

timesGrid = new dhx.Grid(null, {
    columns: [
        { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 0, minWidth: 100, id: "projId", header: [{ text: "Name" }], autoWidth: true, align: "left" },
        { width: 130, id: "date", header: [{ text: "Date" }], autoWidth: true, align: "left" },
        { width: 45, id: "numHR", header: [{ text: "Hr." }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
    ],
    selection:"row",
    adjust: "true", 
    data: dsTimes
});
timesGrid.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelTime = row.Id;
    if ( canModify == 1 ) tbStaff.enable(['edit']);
    if ( canDelete == 1 ) tbStaff.enable(['delete']);
});
function loadTimes() {
    dsTimes.removeAll();
    dsTimes.load("readDB.php?t=times&r="+wSelected).then(function(){
    });
};

pLayout.getCell("staff").attach(pGridStaff);
pLayout.getCell("showDet").attach(timesGrid);

function addEditTime(argument) {
    const dhxWindow = new dhx.Window({
        width: 400,
        height: 400,
        closable: true,
        movable: true,
        modal: true,
        title: "Time registration"
    });
    const form = new dhx.Form(null, {
        css: "dhx_widget--bordered",
        padding: 10,
        width: 380,
        rows: [
            { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
            { id: "projId", name: "projId", type: "combo", label: "Project", labelWidth: "40px", width: "320px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
            { type: "datepicker", name: "date", label: "Date", labelPosition: "left", labelWidth: "40px", width: "250px", dateFormat: "%Y-%m-%d" },
            {
                align: "start",
                cols: [
                    { type: "input", name: "TmFrom", label: "From", labelPosition: "left", labelWidth: "40px", width: "110px", placeholder: "00:00",
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    },
                    { type: "input", name: "TmTo", label: "To", labelPosition: "left", labelWidth: "25px", width: "95px", placeholder: "00:00",
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    },
                    { type: "input", name: "TmBreak", label: "Break", labelPosition: "left", labelWidth: "45px", width: "115px", placeholder: "00:00", 
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    }                 
                ]
            },
            { type: "textarea", name: "workDone", required: false, label: "Works", labelPosition: "left", labelWidth: "40px" },
            {
                align: "end",
                cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                ]
            }
        ]
    });
    cbProject = form.getItem("projId").getWidget();
    cbProject.data.load("../commonQy.php?t=prjs").then(function(){
        dhx.ajax.get("../addTimeHrQy.php?t=r&id="+wSelTime).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            form.setValue(obj);
            cbProject.setValue(obj.projId);
        }).catch(function (err) {
                console.log(err);
        });
    });
    form.events.on("Change",function(name, new_value){
        console.log(name+" - "+new_value);
        if ( name == 'TmFrom' || name == 'TmTo' || name == 'TmBreak' ) {
            if (new_value.length < 4 && new_value.length > 0) {
                dhx.alert({
                    header: "Hours input",
                    text: "Must be in the format 00:00",
                    buttonsAlignment: "center",
                    buttons: ["ok"],
                });
            }
            if (new_value.length == 4 ) {
                hours = new_value.substring(0,2);
                minutes = new_value.substring(2,4);
                console.log(hours+" "+minutes);
                if ( hours < 0 || hours > 23 ) {
                    dhx.alert({
                        header: "Hours range",
                        text: "Must be from 00 to 23",
                        buttonsAlignment: "center",
                        buttons: ["ok"],
                    });
                }
                if ( minutes < 0 || minutes > 59 ) console.log("minutes wrong");
            }
        }
    });
    form.events.on("click", function(name,e){
        console.log(name+" "+e);
        if ( name == 'cancel' ) {
            const config = {
                header: "Time registration ",
                text: "Confirm cancelation?",
                buttons: ["no", "yes"],
                buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    form.destructor();
                    dhxWindow.destructor();
                }
            });         
        };
        if ( name == 'send' ) {
            const config = {
                header: "Time registration ",
                text: "Confirm time registration?",
                buttons: ["no", "yes"],
                buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    const send = form.send("../addTimeHrWr.php?t=f&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                        console.log(data);
                        wSelTime = 0;
                        loadTimes();
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
