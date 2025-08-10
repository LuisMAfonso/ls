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
    var pGridProject, dsStaff, mainForm, dsProjects, cbRepTo, cbComPos;
    var tbStaff, tbDetails, cbCountry, tabDet, gTStaff, dsTStaff, tbTSTimes;
    var timesGrid, timesLayout, tbTimes, dsTimes, pGridStaff, form;
    var wSelected = wSelTime = 0;
    var calendar = null;
    var emp_times = {};

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "wide",
              rows: [ 
                { id: "lTbProject", html: "", height: "55px" },
                { id: "projects", html: "" },
              	{ id: "staff", html: "", height: "250px" },
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

    dsProjects = new dhx.DataCollection();
    dsStaff = new dhx.DataCollection();
    dsTimes = new dhx.DataCollection();
    dsTStaff = new dhx.DataCollection();
    loadProjects();

    function loadProjects() {
        dsProjects.removeAll();
        dsProjects.load("addProjTimeQy.php?t=projects").then(function(){
    //      console.log("done users read");
        });
    }

    function loadStaff() {
        dsStaff.removeAll();
        dsStaff.load("addProjTimeQy.php?t=staff&p="+wSelected).then(function(){
    //      console.log("done users read");
        });
    }

    tbStaff = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbStaff.events.on("input", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridProject.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbStaff.events.on("click", function(id,e){
        console.log(id);
    });
	pLayout.getCell("lTbProject").attach(tbStaff);

    function loadToolbar() {
        tbStaff.data.load("toolbarsQy.php?t=j_search").then(function(){
        });
    }

    pGridProject = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "projName", header: [{ text: "Number" }], autoWidth: true },
            { width: 150, minWidth: 150, id: "projCity", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 45, id: "icon", header: [{ text: "S." }], align: "center", htmlEnable: true  },
            { width: 70, id: "tTime", header: [{ text: "Hours" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
        ],
        selection:"row",
        adjust: "true", 
        data: dsProjects
    });
    pGridProject.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        tbTimes.disable(['edit','delete','add','addG']);
        if ( canCreate == 1 ) tbTimes.enable(['add','addG']);

        wSelected = row.Id;
        wSelStaff = 0;
        wSelTime = 0;
        loadStaff();
        loadTimes();
        loadTimesCal();
    });

    pGridStaff = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "N." }], autoWidth: true },
            { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true },
            { width: 150, minWidth: 150, id: "posName", header: [{ text: "Position" }], autoWidth: true, align: "left" },
            { width: 70, id: "tTime", header: [{ text: "Hours" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
        ],
        selection:"row",
        adjust: "true", 
        data: dsStaff
    });
    pGridStaff.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelStaff = row.Id;
        wSelTime = 0;

        loadTimes();
        loadTimesCal();
    });

    pLayout.getCell("projects").attach(pGridProject);
    pLayout.getCell("staff").attach(pGridStaff);

    function loadTimesCal() {
        dhx.ajax.get("addProjTimeQy.php?t=ut&r="+wSelStaff+"&p="+wSelected).then(function (data) {
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
            { width: 60, id: "TmTo", header: [{ text: "Until" }], autoWidth: true, align: "left" },
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
    tbTimes.data.load("toolbarsQy.php?t=tp_aed").then(function(){
        tbTimes.disable(['edit','delete','add','addG']);
    });
    tbTimes.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditTime(); }
        if ( id == 'add' )  { 
            wSelCnt = 0; 
            addEditTime(); 
        }
        if ( id == 'addG' )  { 
            wSelCnt = 0; 
            addMultiTime(); 
        }
    });
    function loadTimes() {
        dsTimes.removeAll();
        dsTimes.load("addProjTimeQy.php?t=times&r="+wSelStaff+"&p="+wSelected).then(function(){
        });
    }

    mainLayout.getCell("workplace").attach(pLayout);
    pLayout.getCell("employee").attach(timesLayout);
    timesLayout.getCell("lTbRate").attach(tbTimes);
    timesLayout.getCell("gRates").attach(timesGrid);

    function addMultiTime(argument) {
        const dhxWindow = new dhx.Window({
            width: 800,
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
                    { id: "lTbTStaff", html: "", height: "60px" },
                    { id: "gTStaff", html: "" },
                  ]
                },
            ]
        });

        form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            cols: [
                { type: "datepicker", name: "date", label: "Date", labelPosition: "left", labelWidth: "40px", width: "180px", dateFormat: "%Y-%m-%d" },
                { type: "timepicker", name: "TmFrom", label: "From", labelPosition: "left", labelWidth: "45px", width: "135px", timeFormat: 24,  },
                { type: "timepicker", name: "TmTo", label: "To", labelPosition: "left", labelWidth: "25px", width: "115px", timeFormat: 24,  },
                { type: "timepicker", name: "TmBreak", label: "Break", labelPosition: "left", labelWidth: "45px", width: "135px", timeFormat: 24,  },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        form.events.on("click", function(name,e){
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        form.destructor();
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
                    wWorks = '';
                    allSel = '[';
                    firstRec = 1;
                    dsTStaff.forEach(function (item, index, array) {
//                        console.log(JSON.stringify(item)+" - "+JSON.stringify(index)+" : "+JSON.stringify(array)); 
                        wItem = JSON.parse(JSON.stringify(item));
                        if ( wItem.selStaff == true ) {
                            if ( firstRec == 0 ) allSel += ', ';
                            if ( firstRec == 1 ) firstRec = 0;
                            allSel += '{ "id": "'+wItem.id+'", "tmFrom": "'+wItem.tmFrom+'", "tmTo": "'+wItem.tmTo+'" , "tmBreak": "'+wItem.tmBreak+'", "wd": "'+wWorks+'" } ';
                        }
                    });
                    allSel += ']';
                    console.log(allSel);
                    wDate = form.getItem("date").getValue();
                    dhx.ajax.post("addProjTimeWr.php?t=mWr&p="+wSelected+"&d="+wDate, allSel).then(function (data) {
                        console.log(data);
                    }).catch(function (err) {
                        console.log(err);
                    });
                    loadStaff();
                    loadTimes();
                    loadTimesCal();
                    form.destructor();
                    gTStaff.destructor();
                    dhxWindow.destructor(); 
                    };
                });         
            };
        });

        gTStaff = new dhx.Grid(null, {
            columns: [
                { width: 40, id: "id", header: [{ text: "N." }], autoWidth: true, hidden: true },
                { width:50, id: "selStaff", type: "boolean" ,header: [{ text: "Sel." }], htmlEnable: true, align: "center", hidden: false},               
                { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true },
                { width: 160, minWidth: 150, id: "posName", header: [{ text: "Position" }], autoWidth: true, align: "left" },
                { width: 60, id: "tmFrom", header: [{ text: "From" }], autoWidth: true, align: "left" },
                { width: 60, id: "tmTo", header: [{ text: "To" }], autoWidth: true, align: "left" },
                { width: 60, id: "tmBreak", header: [{ text: "Break" }], autoWidth: true, align: "left" },
            ],
            editable: true,
            autoWidth: true,
            css: "alternate_row",
            selection: true,
            data: dsTStaff
        });
        gTStaff.events.on("afterEditEnd", (value, row, column) => {
            console.log(value+" "+row.id+" "+column.id);

            hr1 =  form.getItem("TmFrom").getValue();
            hr2 =  form.getItem("TmTo").getValue();
            hrB =  form.getItem("TmBreak").getValue();
//            hr = ((hr2.hour*60+hr2.minute)-(hr1.hour*60+hr1.minute))/60;
            if ( value ) bla = JSON.parse(JSON.stringify({ tmFrom: hr1, tmTo: hr2, tmBreak: hrB }));
            if ( !value ) bla = JSON.parse(JSON.stringify({ tmFrom: '', tmTo: '', tmBreak: '' }));
//            console.log(hr1+" "+hr2+" - "+bla);
            gTStaff.data.update(row.id, bla);
        });

        dsTStaff.removeAll();
        dsTStaff.load("addProjTimeQy.php?t=allStaff").then(function(){
        });


        dhxWindow.attach(tStaffLayout);
//        tStaffLayout.getCell("lTbTStaff").attach(tbTSTimes);
        tStaffLayout.getCell("lTbTStaff").attach(form);
        tStaffLayout.getCell("gTStaff").attach(gTStaff);

        dhx.ajax.get("addProjTimeQy.php?t=tf").then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
        }).catch(function (err) {
                    console.log(err);
        });

        dhxWindow.show();

    }

    function addEditTime(argument) {
        const dhxWindow = new dhx.Window({
            width: 600,
            height: 450,
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
                { type: "input", id: "projName", name: "projName", label: "Project", labelWidth: "50px", width: "500px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },                
                { id: "staffId", name: "staffId", type: "combo", label: "Staff", labelWidth: "50px", width: "500px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
                { type: "datepicker", name: "date", label: "Date", labelPosition: "left", labelWidth: "50px", width: "250px", dateFormat: "%Y-%m-%d" },
                { type: "timepicker", name: "TmFrom", label: "From", labelPosition: "left", labelWidth: "50px", width: "250px", timeFormat: 24,  },
                { type: "timepicker", name: "TmTo", label: "To", labelPosition: "left", labelWidth: "50px", width: "250px", timeFormat: 24,  },
                { type: "timepicker", name: "TmBreak", label: "Break", labelPosition: "left", labelWidth: "50px", width: "250px", timeFormat: 24,  },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });

        cbStaff = form.getItem("staffId").getWidget();
        cbStaff.data.load("commonQy.php?t=rt").then(function(){
            dhx.ajax.get("addProjTimeQy.php?t=r&id="+wSelTime+"&p="+wSelected+"&s="+wSelStaff).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbStaff.setValue(obj.staffId);
            }).catch(function (err) {
                    console.log(err);
            });
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
                        const send = form.send("addProjTimeWr.php?t=f&p="+wSelected, "POST").then(function(data){
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

    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=addProjTime").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;  
            loadToolbar();          
        }).catch(function (err) {
            console.log(err);
        });
    };

</script>