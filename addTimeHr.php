<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>
<div id="layout" style="height: 100vh;"></div>
<style>
    .day_mark2 {
        color: #000;
    }
    .day_mark2:after {
        background-color: #C5E8F9;
        opacity: 1;
        z-index: -3;
    }
    .day_mark4 {
        color: #000;
    }
    .day_mark4:after {
        background-color: #87D3FD;
        opacity: 1;
        z-index: -3;
    }
    .day_mark6 {
        color: #000;
    }
    .day_mark6:after {
        background-color: #39BAFF;
        opacity: 1;
        z-index: -3;
    }
    .day_mark8 {
        color: #fff;
    }
    .day_mark8:after {
        background-color: #048AD1;
        opacity: 1;
        z-index: -3;
    }

</style>

<?php
require_once('sidebar.php');
?>
<script>
    var pLayout;
    var pGridStaff, dsStaff, mainForm, dsNotes, cbRepTo, cbComPos;
    var tbStaff, tbDetails, cbCountry, tabDet, notesGrid, noteLayout, tbNotes;
    var timesGrid, timesLayout, tbTimes, dsTimes;
    var wSelected = wSelTime = 0;
    var calendar = null;
    var emp_times = {};

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbStaff", html: "", height: "55px" },
              	{ id: "staff", html: "" },
              ]
            },
            { type: "line",
              width: "620px",
              rows: [ 
                { id: "employee", html: "",  },
                { id: "showCal", height: "270px",  },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    dsStaff = new dhx.DataCollection();
    dsTimes = new dhx.DataCollection();

    function loadStaff() {
        dsStaff.removeAll();
        dsStaff.load("addTimeHrQy.php?t=staff").then(function(){
    //      console.log("done users read");
        });
    }

    tbStaff = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbStaff.data.load("toolbarsQy.php?t=j_search").then(function(){
    });;
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
        if ( id == 'edit' ) { addEditTimes(); }
        if ( id == 'add' )  { wSelected = 0; addEditTimes(); }
    });
	pLayout.getCell("lTbStaff").attach(tbStaff);

    pGridStaff = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 70, id: "staffNumber", header: [{ text: "Number" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 180, id: "staffPosition", header: [{ text: "Position" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsStaff
    });
    pGridStaff.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        if ( canCreate == 1 ) tbTimes.enable(['add']);

        loadTimes();
        loadTimesCal();
    });


    pLayout.getCell("staff").attach(pGridStaff);

    function loadTimesCal() {
        dhx.ajax.get("addTimeHrQy.php?t=ut&r="+wSelected).then(function (data) {
             emp_times = JSON.parse(data);
             showCalendar();
        }).catch(function (err) {
            console.log(err);
        });
    }

    function showCalendar() {
        if ( calendar != null ) {
            calendar.destructor();
            calendar = null;
        }
        calendar = new dhx.Calendar(null, {
            width: "100%",
            mark: function (date) {
                key = date.getFullYear() +
                "-" + ('0'+(date.getMonth() + 1)).substr(-2) + "-" +('0'+date.getDate()).substr(-2);
                if ( key in emp_times ) {
                    return emp_times[key];
                }
            },
        });
        calendar.events.on("change",(date)=>{ 
            console.log(date.getFullYear() +
                "-" + ('0'+(date.getMonth() + 1)).substr(-2) + "-" +('0'+date.getDate()).substr(-2));
        });
        pLayout.getCell("showCal").attach(calendar);
    }


// rates 
    timesGrid = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 0, minWidth: 150, id: "projId", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 100, id: "date", header: [{ text: "Date" }], autoWidth: true, align: "left" },
            { width: 60, id: "TmFrom", header: [{ text: "From" }], autoWidth: true, align: "left" },
            { width: 60, id: "TmTo", header: [{ text: "To" }], autoWidth: true, align: "left" },
            { width: 60, id: "TmBreak", header: [{ text: "Break" }], autoWidth: true, align: "left" },
            { width: 60, id: "numHR", header: [{ text: "Total" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
        ],
        selection:"row",
        adjust: "true", 
        data: dsTimes
    });
    timesGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelTime = row.Id;
        if ( canModify == 1 ) tbTimes.enable(['edit']);
        if ( canDelete == 1 ) tbTimes.enable(['delete']);
    });
    timesLayout = new dhx.Layout(null, {
        type: "line",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbRate", html: "", height: "55px" },
                { id: "gRates", html: "" },
              ]
            },
        ]
    });
    tbTimes = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbTimes.data.load("toolbarsQy.php?t=tm_aed").then(function(){
        tbTimes.disable(['edit', 'delete','add']);
    });;
    tbTimes.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditTime(); }
        if ( id == 'add' )  { wSelTime = 0; addEditTime(); }
        if ( id == 'delete' ) { deleteTimes(); }
    });
    function loadTimes() {
        dsTimes.removeAll();
        dsTimes.load("addTimeHrQy.php?t=times&r="+wSelected).then(function(){
        });
    }

    mainLayout.getCell("workplace").attach(pLayout);
    pLayout.getCell("employee").attach(timesLayout);
    timesLayout.getCell("lTbRate").attach(tbTimes);
    timesLayout.getCell("gRates").attach(timesGrid);

    function addEditTime(argument) {
        const dhxWindow = new dhx.Window({
            width: 600,
            height: 400,
            closable: true,
            movable: true,
            modal: true,
            title: "Time registration"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
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
        cbProject = form.getItem("projId").getWidget();
        cbProject.data.load("commonQy.php?t=prjs").then(function(){
            if ( wSelTime != 0 ) {
                dhx.ajax.get("addTimeHrQy.php?t=r&id="+wSelTime).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbProject.setValue(obj.projId);
                }).catch(function (err) {
                        console.log(err);
                }); 
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
                        const send = form.send("addTimeHrWr.php?t=f&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadTimes();
                            loadTimesCal();
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
    function deleteTimes() {
        wMessage = {
            header: "Delete ", text: "Confirm time deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
        };   
        dhx.confirm(wMessage).then(function(answer){
            if (answer) {
                dhx.ajax.get("addTimeHrWr.php?t=d&r="+wSelTime).then(function (data) {
                    loadTimes();
                    loadTimesCal();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });         
    }

    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=addTimeHr").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;     
            loadStaff();        
        }).catch(function (err) {
            console.log(err);
        });
    };

</script>