<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>
<div id="layout" style="height: 100vh;"></div>

<?php
require_once('sidebar.php');
?>
<script>
    var pLayout;
    var pGridStaff, dsStaff, mainForm, dsNotes, cbRepTo, cbComPos;
    var tbStaff, tbDetails, cbCountry, tabDet, notesGrid, noteLayout, tbNotes;
    var ratesGrid, rateLayout, tbRates, dsRates;
    var wSelected = wSelCnt = wSelRate= 0;

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
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    loadPermission();

    var f_staff = {
        css: "bg-promo",
        align: "start",
        width: "615px",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Customer info",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "staffId", name: "staffId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "staffNumber", name: "staffNumber", type: "input", label: "Number", labelWidth: "70px", width: "250px",  placeholder: "employee number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "staffName", name: "staffName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "employee name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "staffAddress", name: "staffAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "staffZipcode", name: "staffZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "staffCity", name: "staffCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "staffCountry", name: "staffCountry", type: "input", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "staffEmail", name: "staffEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "employee e-mail", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                    align: "between",
                    cols: [
                        {
                        rows: [
                            {
                            align: "start", 
                                cols: [
                                    { id: "staffPhone", name: "staffPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "employee phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                                    }
                               ]
                            },
                            {
                            align: "start", 
                                cols: [
                                    { id: "staffVatNumber", name: "staffVatNumber", type: "input", label: "VAT number", labelWidth: "70px", width: "300px",  placeholder: "employee VAT number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                                    }
                               ]
                            },
                            {
                            align: "start", 
                                cols: [
                                    { id: "staffIDnumber", name: "staffIDnumber", type: "input", label: "ID number", labelWidth: "70px", width: "300px",  placeholder: "employee ID number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                                    }
                               ]
                            },
                            {
                            align: "start", 
                                cols: [
                                    { id: "staffDriveLicense", name: "staffDriveLicense", type: "input", label: "Driver lic.#", labelWidth: "70px", width: "300px",  placeholder: "employee drivers license", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                                    }
                               ]
                            },
                            {
                            align: "start", 
                                cols: [
                                    { id: "staffPosition", name: "staffPosition", type: "input", label: "Position", labelWidth: "70px", width: "300px",  placeholder: "employee position", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                                    }
                               ]
                            },
                        ]
                        },
                        {
                            type: "fieldset",
                            name: "fAvatar",
                            width: "40%",
                            label: "Photo",
                            labelAlignment: "right",
                            align: "center",
                            rows: [
                                { type: "avatar", name: "staffAvatar", size: 180, }
                            ]
                        },
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "staffReportTo", name: "staffReportTo", type: "input", label: "Report to", labelWidth: "70px", width: "300px",  placeholder: "employee report to", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                }
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_staff);
    dsStaff = new dhx.DataCollection();
    dsNotes = new dhx.DataCollection();
    dsRates = new dhx.DataCollection();
    loadUsers();

    function loadUsers() {
        dsStaff.removeAll();
        dsStaff.load("mntStaffQy.php?t=staff").then(function(){
    //      console.log("done users read");
        });
    }

    tbStaff = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbStaff.events.on("inputChange", function (event, arguments) {
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
        if ( id == 'edit' )   { addEditStaff(); }
        if ( id == 'add' )    { wSelected = 0; addEditStaff(); }
        if ( id == 'delete' ) { deleteStaff(); }
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
        if ( canModify == 1 ) tbStaff.enable(['edit']);
        if ( canDelete == 1 ) tbStaff.enable(['delete']);
        if ( canCreate == 1 ) tbRates.enable(['add']);
        if ( canCreate == 1 ) tbNotes.enable(['add']);

        dhx.ajax.get("mntStaffQy.php?t=employee&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
            loadNotes();
            loadRates();
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("staff").attach(pGridStaff);

    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "Employee Info"},
            { id: "rates", tab: "Rates"},
            { id: "notes", tab: "Notes"}
        ]
    });
    pLayout.getCell("employee").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

// notes 
    notesGrid = new dhx.Grid(null, {
        columns: [
            { width: 0, minWidth: 100, id: "noteId", header: [{ text: "Id" }], autoWidth: true, hidden: true, align: "left" },
            { width: 40, id: "noteIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
            { width: 0, id: "noteText", header: [{ text: "Text" }], autoWidth: true },
            { width: 125, id: "notedate", header: [{ text: "Date" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsNotes
    });
    notesGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelCnt = row.noteId;
        tbNotes.enable(['edit', 'delete']);

        dhx.ajax.get("mntStaffQy.php?t=notes&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
        }).catch(function (err) {
            console.log(err);
        });
    });
    noteLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbNote", html: "", height: "55px" },
                { id: "gNotes", html: "" },
              ]
            },
        ]
    });
    tbNotes = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbNotes.data.load("toolbarsQy.php?t=n_aed").then(function(){
        tbNotes.disable(['edit', 'delete','add']);
    });;
    tbNotes.events.on("inputChange", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        notesGrid.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbNotes.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditNote(); }
        if ( id == 'add' )  { wSelCnt = 0; addEditNote(); }
    });
    function loadNotes() {
        dsNotes.removeAll();
        dsNotes.load("mntStaffQy.php?t=notes&r="+wSelected).then(function(){
            console.log("done notes read");
        });
    }

// rates 
    ratesGrid = new dhx.Grid(null, {
        columns: [
            { width: 0, minWidth: 100, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true, align: "left" },
            { width: 0, minWidth: 100, id: "dtFrom", header: [{ text: "From" }], autoWidth: true, align: "left" },
            { width: 0, minWidth: 100, id: "dtTo", header: [{ text: "Until" }], autoWidth: true, align: "left" },
            { width: 100, id: "hourCost", header: [{ text: "Hour cost" }], autoWidth: true, align: "right" },
            { width: 100, id: "hourRate", header: [{ text: "Hour rate" }], autoWidth: true, align: "right" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsRates
    });
    ratesGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelRate = row.Id;
        if ( canModify == 1 ) tbRates.enable(['edit']);
        if ( canDelete == 1 ) tbRates.enable(['delete']);
    });
    rateLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbRate", html: "", height: "55px" },
                { id: "gRates", html: "" },
              ]
            },
        ]
    });
    tbRates = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbRates.data.load("toolbarsQy.php?t=t_aed").then(function(){
        tbRates.disable(['edit', 'delete','add']);
    });
    tbRates.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditRate(); }
        if ( id == 'add' )  { wSelRate = 0; addEditRate(); }
    });
    function loadRates() {
        dsRates.removeAll();
        dsRates.load("mntStaffQy.php?t=rates&r="+wSelected).then(function(){
            console.log("done rates read");
        });
    }

    mainLayout.getCell("workplace").attach(pLayout);
    tabDet.getCell("notes").attach(noteLayout);
    noteLayout.getCell("lTbNote").attach(tbNotes);
    noteLayout.getCell("gNotes").attach(notesGrid);
    tabDet.getCell("rates").attach(rateLayout);
    rateLayout.getCell("lTbRate").attach(tbRates);
    rateLayout.getCell("gRates").attach(ratesGrid);


    function deleteStaff() {
        wMessage = {
            header: "Delete ", text: "Confirm Staff deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
        };   
        dhx.confirm(wMessage).then(function(answer){
            if (answer) {
                dhx.ajax.get("mntStaffWr.php?t=d&r="+wSelected).then(function (data) {
                    loadUsers();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });         
    }
    function addEditRate(argument) {
        const dhxWindow = new dhx.Window({
            width: 350,
            height: 360,
            closable: true,
            movable: true,
            modal: true,
            title: "Rate"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "330px",
            rows: [
                { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true 
                },
                { type: "datepicker", name: "dtFrom", label: "Date from", labelPosition: "left", labelWidth: "70px", width: "250px", dateFormat: "%Y-%m-%d" , required: true
                },
                { type: "datepicker", name: "dtTo", label: "Date to", labelPosition: "left", labelWidth: "70px", width: "250px", dateFormat: "%Y-%m-%d" , required: true
                },
                { type: "input", name: "hourCost", label: "Hour cost", labelWidth: "70px", labelPosition: "left", width: "180px", 
                },
                { type: "input", name: "hourRate", required: true, label: "Hour rate", labelWidth: "70px", labelPosition: "left", width: "180px", 
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
        if ( wSelRate != 0 ) {
            dhx.ajax.get("mntStaffQy.php?t=rate&r="+wSelRate).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        };

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Rate ",
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
                    header: "Rate ",
                    text: "Confirm rate creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntStaffWr.php?t=r&r="+wSelRate+"&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadRates();
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

    function addEditStaff(argument) {
        const dhxWindow = new dhx.Window({
            width: 685,
            height: 740,
            closable: true,
            movable: true,
            modal: true,
            title: "Employee"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            rows: [
              {
                name: "fieldset1",
                type: "fieldset",
                label: "Employee info",        
                rows:[
                    {      
                    align: "start", 
                        cols: [
                            { id: "staffId", name: "staffId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "staffNumber", name: "staffNumber", type: "input", label: "Number", labelWidth: "70px", width: "250px",  placeholder: "employee number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "staffName", name: "staffName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "employee name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "staffAddress", name: "staffAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "staffZipcode", name: "staffZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "staffCity", name: "staffCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "staffCountryId", name: "staffCountryId", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "staffEmail", name: "staffEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "employee e-mail", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                        align: "between",
                        cols: [
                            {
                            rows: [
                                {
                                align: "start", 
                                    cols: [
                                        { id: "staffPhone", name: "staffPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "employee phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                                        }
                                   ]
                                },
                                {
                                align: "start", 
                                    cols: [
                                        { id: "staffVatNumber", name: "staffVatNumber", type: "input", label: "VAT number", labelWidth: "70px", width: "300px",  placeholder: "employee VAT number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                                        }
                                   ]
                                },
                                {
                                align: "start", 
                                    cols: [
                                        { id: "staffIDnumber", name: "staffIDnumber", type: "input", label: "ID number", labelWidth: "70px", width: "300px",  placeholder: "employee ID number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                                        }
                                   ]
                                },
                                {
                                align: "start", 
                                    cols: [
                                        { id: "staffDriveLicense", name: "staffDriveLicense", type: "input", label: "Driver lic.#", labelWidth: "70px", width: "300px",  placeholder: "employee drivers license", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                                        }
                                   ]
                                },
                                {
                                align: "start", 
                                    cols: [
                                        { id: "staffPositionId", name: "staffPositionId", type: "combo", label: "Position", labelWidth: "70px", width: "300px",  placeholder: "employee position", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                                        }
                                   ]
                                },
                            ]
                            },
                            {
                                type: "fieldset",
                                name: "fAvatar",
                                width: "40%",
                                label: "Photo",
                                labelAlignment: "right",
                                align: "center",
                                rows: [
                                    { type: "avatar", name: "staffAvatar", size: 180, icon: "dxi dxi-person", fieldName: "file", alt: "Employee photo", labelPosition: "left", target: "uploader.php?t=employee" }
                                ]
                            },
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "staffReportToId", name: "staffReportToId", type: "combo", label: "Report to", labelWidth: "70px", width: "300px",  placeholder: "employee report to", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                            }
                       ]
                    },
                    {
                        align: "end",
                        cols: [
                            { type: "button", name: "cancel", view: "link", text: "Cancel", },
                            { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                        ]
                    }
                ]
              }
            ]
        });
        cbRepTo = form.getItem("staffReportToId").getWidget();
        cbRepTo.data.load("commonQy.php?t=rt").then(function(){
        });
        cbComPos = form.getItem("staffPositionId").getWidget();
        cbComPos.data.load("commonQy.php?t=cp").then(function(){
        });
        cbCountry = form.getItem("staffCountryId").getWidget();
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("mntStaffQy.php?t=employee&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbCountry.setValue(cbCountry.data.getId(obj.staffCountry));
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Customer ",
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
                    header: "User ",
                    text: "Confirm customer creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntStaffWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadUsers();
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

    function loadToolbar() {
        tbStaff.data.load("toolbarsQy.php?t=a_c_r").then(function(){
        tbStaff.disable(['add','edit', 'delete']);
        if ( canCreate == 1 ) tbStaff.enable(['add']);
    });

    }
    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=staff").then(function (data) {
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