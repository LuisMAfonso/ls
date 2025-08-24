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
var tbProjs, pGridProjs, dsProjs, dsWd;
var timesGrid, dsTimes;
var wSelTime = 0;
var gTStaff, dsTStaff, tbTSTimes;
var selWorkdone, mtForm = '';

pLayout = new dhx.Layout(null, {
    type: "line",
    rows: [
        { type: "line",
            rows: [ 
                { id: "lTbProjs", html: "", height: "55px" },
                { id: "projs", html: "" },
            ]
        },
        { id: "showDet", html: "", height: "370px"  },
    ]
});
mainLayout.getCell("workplace").attach(pLayout);

loadPermission();
dsProjs = new dhx.DataCollection();
dsTimes = new dhx.DataCollection();
dsTStaff = new dhx.DataCollection();
dsWd = new dhx.DataCollection();
loadProjs();

function loadProjs() {
    dsTimes.removeAll();
    dsProjs.removeAll();
    dsProjs.load("readDB.php?t=projs").then(function(){
//          console.log("done users read");
    });
};
tbProjs = new dhx.Toolbar(null, {
    css: "dhx_widget--bordered"
});
tbProjs.data.load("toolbarsQy.php?t=tp_search").then(function(){
    tbProjs.disable(['add','edit','delete']);
});
tbProjs.events.on("input", function (event, arguments) {
    console.log(arguments);
    const value = arguments.toString().toLowerCase();
    pGridProjs.data.filter(obj => {
        return Object.values(obj).some(item => 
            item.toString().toLowerCase().includes(value)
        );
    });
});
tbProjs.events.on("click", function(id,e){
    console.log(id);
    if ( id == 'edit' ) { addEditTime(); }
    if ( id == 'add' )  { addMultiTime(); }
});
pLayout.getCell("lTbProjs").attach(tbProjs);

pGridProjs = new dhx.Grid(null, {
    columns: [
        { width: 30, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 0, minWidth: 150, id: "projName", header: [{ text: "Project" }], autoWidth: true },
        { width: 120, id: "projCity", header: [{ text: "City" }], autoWidth: true, align: "left" },
        { width: 45, id: "icon", header: [{ text: "S." }], align: "center", htmlEnable: true  },
    ],
    selection:"row",
    adjust: "true", 
    data: dsProjs
});
pGridProjs.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelected = row.Id;
    tbProjs.enable(['add']);
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
    if ( canModify == 1 ) tbProjs.enable(['edit']);
    if ( canDelete == 1 ) tbProjs.enable(['delete']);
});
function loadTimes() {
    dsTimes.removeAll();
    dsTimes.load("readDB.php?t=prjtimes&r="+wSelected).then(function(){
    });
};

pLayout.getCell("projs").attach(pGridProjs);
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
    mtForm = new dhx.Form(null, {
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
                    }                 ]
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
    cbProject = mtForm.getItem("projId").getWidget();
    cbProject.data.load("../commonQy.php?t=prjs").then(function(){
        dhx.ajax.get("../addTimeHrQy.php?t=r&id="+wSelTime).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            mtForm.setValue(obj);
            cbProject.setValue(obj.projId);
        }).catch(function (err) {
                console.log(err);
        });
    });
    mtForm.events.on("focus", function(name, value, id) {
        if ( name == 'workDone' && value == '' ) {
            console.log(name, value);
            selWorks();
        }
    });    
    mtForm.events.on("Change",function(name, new_value){
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
    mtForm.events.on("click", function(name,e){
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
                    mtForm.destructor();
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
                    const send = mtForm.send("../addTimeHrWr.php?t=f&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                        console.log(data);
                        wSelTime = 0;
                        loadTimes();
                        mtForm.destructor();
                        dhxWindow.destructor();
                    });
                };
            });         
        };
    });

    dhxWindow.attach(mtForm);
    dhxWindow.show();

}

function addMultiTime(argument) {
    const dhxWindow = new dhx.Window({
        width: 400,
        height: 550,
        closable: true,
        movable: true,
        modal: true,
        title: "Time registration"
    });

    tStaffLayout = new dhx.Layout(null, {
        type: "line",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbTStaff", html: "", height: "170px" },
                { id: "gTStaff", html: "" },
              ]
            },
        ]
    });

    mtForm = new dhx.Form(null, {
        css: "dhx_widget--bordered",
        padding: 1,
        rows: [
            {
                cols: [
                    { type: "datepicker", name: "date", label: "Date", labelPosition: "left", labelWidth: "40px", width: "180px", dateFormat: "%Y-%m-%d", required: true },
                    {
                        align: "end",
                        cols: [
                            { type: "button", name: "cancel", view: "link", text: "Cancel", },
                            { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                        ]
                    }
            ]
            },
            {
                align: "start",
                cols: [
                    { type: "input", name: "TmFrom", label: "From", labelPosition: "left", labelWidth: "40px", width: "110px", required: true, placeholder: "00:00",
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    },
                    { type: "input", name: "TmTo", label: "To", labelPosition: "left", labelWidth: "30px", width: "100px", required: true, placeholder: "00:00",
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    },
                    { type: "input", name: "TmBreak", label: "Break", labelPosition: "left", labelWidth: "50px", width: "120px", required: true, placeholder: "00:00", 
                        patternMask: { pattern: "H0:M0", 
                                        charFormat: { "H": /[0-2]/, "M": /[0-5]/,} }  
                    }                 
                ]
            },
            { type: "textarea", name: "workDone", required: false, label: "Works", labelPosition: "left", labelWidth: "40px" 
            },
        ]
    });
    mtForm.events.on("afterValidate", function(name, value, isValid) {
        console.log("afterValidate", name, value, isValid); 
        if ( mtForm.validate(true) ) mtForm.getItem("send").enable();
    });
    mtForm.events.on("focus", function(name, value, id) {
        if ( name == 'workDone' && value == '' ) {
            console.log(name, value);
            selWorks();
        }
    });    
    mtForm.events.on("Change",function(name, new_value){
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
    mtForm.events.on("click", function(name,e){
        if ( name == 'cancel' ) {
            const config = {
                header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    mtForm.destructor();
                    gTStaff.destructor();
                    dhxWindow.destructor();
                }
            });         
        };
        if ( name == 'send' ) {
            const config = {
                header: "User ", text: "Confirm insertion?", buttons: ["no", "yes"], buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
              if (answer) {
                wWorks = mtForm.getItem("workDone").getValue();
                allSel = '[';
                firstRec = 1;
                dsTStaff.forEach(function (item, index, array) {
//                    console.log(JSON.stringify(item)); 
                    wItem = JSON.parse(JSON.stringify(item));
                    if ( wItem.selStaff == true ) {
                        if ( firstRec == 0 ) allSel += ', ';
                        if ( firstRec == 1 ) firstRec = 0;
                        allSel += '{ "id": "'+wItem.id+'", "tmFrom": "'+wItem.tmFrom+'", "tmTo": "'+wItem.tmTo+'" , "tmBreak": "'+wItem.tmBreak+'", "wd": "'+wWorks+'" } ';
                    }
                });
                allSel += ']';
//                console.log(allSel);
                wDate = mtForm.getItem("date").getValue();
                dhx.ajax.post("../addProjTimeWr.php?t=mWr&p="+wSelected+"&d="+wDate, allSel).then(function (data) {
                    console.log(data);
                }).catch(function (err) {
                    console.log(err);
                });
                loadProjs();
                mtForm.destructor();
                gTStaff.destructor();
                dhxWindow.destructor(); 
                };
            });         
        };
    });

    gTStaff = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "id", header: [{ text: "N." }], autoWidth: true, hidden: true },
            { width:45, id: "selStaff", type: "boolean" ,header: [{ text: "Sel." }], htmlEnable: true, align: "center", hidden: false},               
            { width: 0, minWidth: 100, id: "staffName", header: [{ text: "Name" }], autoWidth: true },
            { width: 55, id: "tmFrom", header: [{ text: "Fr." }], autoWidth: true, align: "left" },
            { width: 55, id: "tmTo", header: [{ text: "To" }], autoWidth: true, align: "left" },
            { width: 55, id: "tmBreak", header: [{ text: "Br." }], autoWidth: true, align: "left" },
        ],
        editable: true,
        autoWidth: true,
        css: "alternate_row",
        selection: true,
        data: dsTStaff
    });
    gTStaff.events.on("afterEditEnd", (value, row, column) => {
        console.log(value+" "+row.id+" "+column.id);

        tSplit = mtForm.getItem("TmFrom").getValue();
        hr1 =  tSplit.substr(0, 2)+":"+tSplit.substr(2, 2);
        tSplit =  mtForm.getItem("TmTo").getValue();
        hr2 =  tSplit.substr(0, 2)+":"+tSplit.substr(2, 2);
        tSplit =  mtForm.getItem("TmBreak").getValue();
        hrB =  tSplit.substr(0, 2)+":"+tSplit.substr(2, 2);
//            hr = ((hr2.hour*60+hr2.minute)-(hr1.hour*60+hr1.minute))/60;
        if ( value ) bla = JSON.parse(JSON.stringify({ tmFrom: hr1, tmTo: hr2, tmBreak: hrB }));
        if ( !value ) bla = JSON.parse(JSON.stringify({ tmFrom: '', tmTo: '', tmBreak: '' }));
//            console.log(hr1+" "+hr2+" - "+bla);
        gTStaff.data.update(row.id, bla);
    });

    dsTStaff.removeAll();
    dsTStaff.load("../addProjTimeQy.php?t=allStaff").then(function(){
    });


    dhxWindow.attach(tStaffLayout);
//        tStaffLayout.getCell("lTbTStaff").attach(tbTSTimes);
    mtForm.getItem("send").disable();
    tStaffLayout.getCell("lTbTStaff").attach(mtForm);
    tStaffLayout.getCell("gTStaff").attach(gTStaff);

    dhx.ajax.get("../addProjTimeQy.php?t=tf").then(function (data) {
            var obj = JSON.parse(data);
            console.log(obj);
            mtForm.setValue(obj);
    }).catch(function (err) {
            console.log(err);
    });

    dhxWindow.show();

}

function selWorks(argument) {
    var wdLayout, tbWd, gWd;
    const dhxW1 = new dhx.Window({
        width: 400,
        height: 550,
        closable: true,
        movable: true,
        modal: true,
        title: "Work done selection"
    });

    wdLayout = new dhx.Layout(null, {
        type: "line",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lWd", html: "", height: "60px" },
                { id: "gLWd", html: "" },
              ]
            },
        ]
    });
    dhxW1.attach(wdLayout);

    dsWd.removeAll();
    dsWd.load("readDb.php?t=workDone").then(function(){
    });


    tbWd = new dhx.Toolbar(null, {
        css: "dhx_widget--bordered"
    });
    tbWd.data.load("toolbarsQy.php?t=wd_search").then(function(){  });
    tbWd.events.on("input", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gWd.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbWd.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'select' ) { 
            selWorkdone = '';
            firstRec = 1;
            dsWd.forEach(function (item, index, array) {
                wWordD = JSON.parse(JSON.stringify(item));
                if ( wWordD.selWorks == true ) {
                    if ( firstRec == 0 ) selWorkdone += ', ';
                    if ( firstRec == 1 ) firstRec = 0;
                    selWorkdone += wWordD.workDone;
                }
            });
            mtForm.getItem("workDone").setValue(selWorkdone);
            console.log(selWorkdone);
            gWd.destructor();
            tbWd.destructor();
            dhxW1.destructor(); 
        }
    });

    gWd = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "id", header: [{ text: "N." }], autoWidth: true, hidden: true },
            { width:45, id: "selWorks", type: "boolean" ,header: [{ text: "Sel." }], htmlEnable: true, align: "center", hidden: false},               
            { width: 0, minWidth: 100, id: "workDone", header: [{ text: "Works" }], autoWidth: true },
        ],
        editable: true,
        autoWidth: true,
        css: "alternate_row",
        selection: true,
        data: dsWd
    });
    gWd.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

//        selWorkdone = row.workDone;
//        mtForm.getItem("workDone").setValue(selWorkdone);

    });

    wdLayout.getCell("lWd").attach(tbWd);
    wdLayout.getCell("gLWd").attach(gWd);
    dhxW1.show();
};

function loadPermission() {
    dhx.ajax.get("../menuQy.php?t=perm&p=regPrjTms").then(function (data) {
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
