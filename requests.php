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
    var pGridRequest, dsRequest, mainForm, pGridDetails;
    var tbRequest, cbProject, cbSupplier, cbSFam;
    var statusLogGrid, dsStatusLog;
    var reqDetLayout, tbReqDet, dsReqDetails, gReqDetails, formHeader;
    var wSelected = wSelDet = 0;
    var wSerType = '';

    pLayout = new dhx.Layout(null, {
        type: "space",
        rows: [
            { type: "line",
              rows: [ 
              	{ id: "ltbRequest", html: "", height: "55px" },
              	{ id: "requests", html: "" },
              ]
            },
            { type: "wide",
              cols: [ 
                { id: "reqDetails", html: "" },
                { id: "statusLog", html: "", width: "250px", },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    loadPermission();

    dsRequest = new dhx.DataCollection();
    dsStatusLog = new dhx.DataCollection();
    dsReqDetails = new dhx.DataCollection();


    function loadRequests() {
        dsRequest.removeAll();
        dsRequest.load("requestsQy.php?t=requests").then(function(){
    //      console.log("done users read");
        });
    }

    tbRequest = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbRequest.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridRequest.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbRequest.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'reqDet' && wSerType == 's' ) { requestDetailsService(); }
        if ( id == 'reqDet' && wSerType == 'p' ) { requestDetailsProduct(); }
        if ( id == 'edit' ) { addEditRequest(); }
        if ( id == 'add' )  { wSelected = 0; addEditRequest(); }
        if ( id == 'delete' ) {  
            wMessage = {
                header: "Delete ", text: "Confirm request deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
            dhx.confirm(wMessage).then(function(answer){
                if (answer) {
                    console.log(answer);
                    dhx.ajax.get("requestsWr.php?t=del&r="+wSelected).then(function (data) {
                        wSelected = 0;
                        tbRequest.disable(['add','edit', 'delete','reqDet']); 
                        loadRequests();
                }).catch(function (err) {
                            console.log(err);
                    });
                }
            });         
        }
    });
	pLayout.getCell("ltbRequest").attach(tbRequest);

    pGridRequest = new dhx.Grid(null, {
        columns: [
            { width: 45, id: "Id", header: [{ text: "Id" }], autoWidth: true, align: "right" },
            { width: 90, id: "reqDate", header: [{ text: "Date" }], autoWidth: true },
            { width: 90, id: "reqType", header: [{ text: "Type" }], autoWidth: true },
            { width: 90, id: "reqStatusName", header: [{ text: "Status" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "suppName", header: [{ text: "Supplier" }], autoWidth: true, align: "left" },
            { width: 0, minWidth: 150, id: "projName", header: [{ text: "Project" }], autoWidth: true, align: "left" },
            { width: 180, id: "sfamName", header: [{ text: "Sub-family" }], autoWidth: true },
            { width: 45, id: "sfamIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsRequest
    });
    pGridRequest.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id+" - "+row.reqTypeId);
 
        wSelected = row.Id;
        wSerType = row.reqTypeId;
        if ( canModify == 1 ) tbRequest.enable(['edit','reqDet']);
        if ( canDelete == 1 ) tbRequest.enable(['delete']);
        loadStatusLog();
        loadReqDetails();
    });
    pGridDetails = new dhx.Grid(null, {
        columns: [
            { width: 45, id: "Id", header: [{ text: "Id" }], autoWidth: true, align: "right", hidden: true },
            { width: 90, id: "reqCode", header: [{ text: "Code" }], autoWidth: true },
            { width: 190, id: "reqName", header: [{ text: "Description" }], autoWidth: true },
            { width: 70, id: "reqQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right" , type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 90, id: "reqPrice", header: [{ text: "Price" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 45, id: "sfamIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
            { width: 0, id: "reqNotes", header: [{ text: "Notes" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsReqDetails
    });

    function noteTemplate(value, row, col) {
        return `${row.rslNotes}</br>`;
    }

    statusLogGrid = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 90, id: "rslDate", header: [{ text: "Date" }], autoWidth: true },
            { width: 0, id: "rslStatusName", header: [{ text: "Status" }], autoWidth: true },
            { width: 45, id: "rslNote", header: [{ text: "N." }], align: "center", htmlEnable: true, tooltip: true, tooltipTemplate: noteTemplate  },
        ],
        selection:"row",
        adjust: "true", 
        data: dsStatusLog
    });

    pLayout.getCell("requests").attach(pGridRequest);
    pLayout.getCell("reqDetails").attach(pGridDetails);
    pLayout.getCell("statusLog").attach(statusLogGrid);

    function loadStatusLog() {
        dsStatusLog.removeAll();
        dsStatusLog.load("requestsQy.php?t=statusLogs&r="+wSelected).then(function(){
    //      console.log("done users read");
        });
    }

// request details Service
    function requestDetailsService() {
        const dhxWindow = new dhx.Window({
            width: 900,
            height: 650,
            closable: true,
            movable: true,
            modal: true,
            title: "Request Details Service"
        });
        reqDetLayout = new dhx.Layout(null, {
            type: "wide",
            rows: [
                { id: "reqHeader", html: "", height: "110px" },
                { type: "line",
                  rows: [ 
                    { id: "lTbReqDet", html: "", height: "55px" },
                    { id: "reqDetails", html: "" },
                  ]
                },
            ]
        });
        tbReqDet = new dhx.Toolbar(null, {
          css: "dhx_widget--bordered"
        });
        tbReqDet.data.load("toolbarsQy.php?t=rd_aed").then(function(){
            tbReqDet.disable(['edit', 'delete']);
        });;
        tbReqDet.events.on("click", function(id,e){
            console.log(id);
            if ( id == 'edit' ) { requestDetailsServiceAdd(); }
            if ( id == 'add' )  { wSelDet = 0; requestDetailsServiceAdd(); }
            if ( id == 'delete' ) {  
                wMessage = {
                    header: "Delete ", text: "Confirm request service deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
                };   
                dhx.confirm(wMessage).then(function(answer){
                    if (answer) {
                        console.log(answer);
                        dhx.ajax.get("requestsWr.php?t=dels&r="+wSelDet).then(function (data) {
                            tbReqDet.disable(['edit', 'delete']); 
                            loadReqDetailsService();
                    }).catch(function (err) {
                                console.log(err);
                        });
                    }
                });         
            }
        });

        gReqDetails = new dhx.Grid(null, {
            columns: [
                { width: 45, id: "Id", header: [{ text: "Id" }], autoWidth: true, align: "right", hidden: true },
                { width: 90, id: "reqsCode", header: [{ text: "Code" }], autoWidth: true },
                { width: 150, id: "reqsName", header: [{ text: "Description" }], autoWidth: true },
                { width: 70, id: "reqsQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 90, id: "reqsPrice", header: [{ text: "Price" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 45, id: "sfamIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
                { width: 0, id: "reqsNotes", header: [{ text: "Notes" }], autoWidth: true },
            ],
            selection:"row",
            adjust: "true", 
            data: dsReqDetails
        });
        gReqDetails.events.on("cellClick", function(row,column){
            console.log(row.Id+" - "+column.id);

            wSelDet = row.Id;
            if ( canModify == 1 ) tbReqDet.enable(['edit']);
            if ( canDelete == 1 ) tbReqDet.enable(['delete']);
        });

        formHeader = new dhx.Form(null, {
          css: "dhx_widget--bg_white dhx_widget--bordered",
          padding: 5,
          rows: [
            {      
                align: "start", 
                cols: [
                    { type: "input", name: "reqId", label: "Req.Id", labelPosition: "left", labelWidth: 70, width: "170px", readOnly: true },
                    { type: "input", name: "reqDate", label: "Date", labelPosition: "left", labelWidth: 45, width: "230px", readOnly: true },
                    { type: "input", name: "reqType", label: "Type", labelPosition: "left", labelWidth: 45, width: "150px", readOnly: true },
                    { type: "input", name: "reqStatus", label: "status", labelPosition: "left", labelWidth: 60, width: "250px", readOnly: true },
                ]
            },
            {      
                align: "start", 
                cols: [
                    { type: "input", name: "suppName", label: "Supplier", labelPosition: "left", labelWidth: 70, width: "370px", readOnly: true },
                    { type: "input", name: "projName", label: "Project", labelPosition: "left", labelWidth: 70, width: "390px", readOnly: true },
               ]
            },
          ]
        });        
        reqDetLayout.getCell("reqHeader").attach(formHeader);
        reqDetLayout.getCell("lTbReqDet").attach(tbReqDet);
        reqDetLayout.getCell("reqDetails").attach(gReqDetails);

        dhx.ajax.get("requestsQy.php?t=reqHeader&r="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            formHeader.setValue(obj);
        }).catch(function (err) {
            console.log(err);
        });
        loadReqDetailsService();

        dhxWindow.attach(reqDetLayout);
        dhxWindow.show();
    }

    function loadReqDetails() {
        dsReqDetails.removeAll();
        dsReqDetails.load("requestsQy.php?t=reqDetail&r="+wSelected+"&rq="+wSerType).then(function(){
    //      console.log("done users read");
        });
    }

    function loadReqDetailsService() {
        dsReqDetails.removeAll();
        dsReqDetails.load("requestsQy.php?t=reqDetailsS&r="+wSelected).then(function(){
    //      console.log("done users read");
        });
    }
    function requestDetailsServiceAdd(argument) {
        const dhxWindow = new dhx.Window({
            width: 785,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Request Info Service"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "665px",
            rows: [
                { type: "input", name: "reqsId", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "reqsCode", required: false, label: "Code", labelWidth: "70px", labelPosition: "left", width: "190px" },
                { type: "input", name: "reqsName", required: true, label: "Description", labelWidth: "70px", labelPosition: "left", width: "300px" },
                { type: "input", name: "reqsQuant", label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "reqsPrice", required: false, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "combo", name: "reqsSfamId", label: "Sub-family", labelWidth: "70px", width: "300px", labelPosition: "left" },
                { type: "textarea", name: "reqsNotes", required: false, label: "Note", labelWidth: "70px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        cbSFam = form.getItem("reqsSfamId").getWidget();
        cbSFam.data.load("commonQy.php?t=fam&s="+wSerType).then(function(){
            dhx.ajax.get("requestsQy.php?t=reqDetailS&r="+wSelDet).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbSFam.setValue(obj.subfamId);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Request ",
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
                    header: "Request ",
                    text: "Confirm request creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("requestsWr.php?t=rqDetS&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadReqDetailsService();
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

// end request detail Services

// request details Product
    function requestDetailsProduct() {
        const dhxWindow = new dhx.Window({
            width: 900,
            height: 650,
            closable: true,
            movable: true,
            modal: true,
            title: "Request Details Product"
        });
        reqDetLayout = new dhx.Layout(null, {
            type: "wide",
            rows: [
                { id: "reqHeader", html: "", height: "110px" },
                { type: "line",
                  rows: [ 
                    { id: "lTbReqDet", html: "", height: "55px" },
                    { id: "reqDetails", html: "" },
                  ]
                },
            ]
        });
        tbReqDet = new dhx.Toolbar(null, {
          css: "dhx_widget--bordered"
        });
        tbReqDet.data.load("toolbarsQy.php?t=rd_aed").then(function(){
            tbReqDet.disable(['edit', 'delete']);
        });;
        tbReqDet.events.on("click", function(id,e){
            console.log(id);
            if ( id == 'edit' ) { requestDetailsProductAdd(); }
            if ( id == 'add' )  { wSelDet = 0; requestDetailsProductAdd(); }
            if ( id == 'delete' ) {  
                wMessage = {
                    header: "Delete ", text: "Confirm request product deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
                };   
                dhx.confirm(wMessage).then(function(answer){
                    if (answer) {
                        console.log(answer);
                        dhx.ajax.get("requestsWr.php?t=delp&r="+wSelDet).then(function (data) {
                            tbReqDet.disable(['edit', 'delete']); 
                            loadReqDetailsProduct();
                    }).catch(function (err) {
                                console.log(err);
                        });
                    }
                });         
            }
        });

        gReqDetails = new dhx.Grid(null, {
            columns: [
                { width: 45, id: "Id", header: [{ text: "Id" }], autoWidth: true, align: "right", hidden: true },
                { width: 90, id: "reqpArticle", header: [{ text: "Code" }], autoWidth: true },
                { width: 150, id: "reqpName", header: [{ text: "Description" }], autoWidth: true },
                { width: 70, id: "reqpQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 90, id: "reqpPrice", header: [{ text: "Price" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 45, id: "sfamIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
                { width: 0, id: "reqpNotes", header: [{ text: "Notes" }], autoWidth: true },
            ],
            selection:"row",
            adjust: "true", 
            data: dsReqDetails
        });
        gReqDetails.events.on("cellClick", function(row,column){
            console.log(row.Id+" - "+column.id);

            wSelDet = row.Id;
            if ( canModify == 1 ) tbReqDet.enable(['edit']);
            if ( canDelete == 1 ) tbReqDet.enable(['delete']);
        });

        formHeader = new dhx.Form(null, {
          css: "dhx_widget--bg_white dhx_widget--bordered",
          padding: 5,
          rows: [
            {      
                align: "start", 
                cols: [
                    { type: "input", name: "reqId", label: "Req.Id", labelPosition: "left", labelWidth: 70, width: "170px", readOnly: true },
                    { type: "input", name: "reqDate", label: "Date", labelPosition: "left", labelWidth: 45, width: "230px", readOnly: true },
                    { type: "input", name: "reqType", label: "Type", labelPosition: "left", labelWidth: 45, width: "150px", readOnly: true },
                    { type: "input", name: "reqStatus", label: "status", labelPosition: "left", labelWidth: 60, width: "250px", readOnly: true },
                ]
            },
            {      
                align: "start", 
                cols: [
                    { type: "input", name: "suppName", label: "Supplier", labelPosition: "left", labelWidth: 70, width: "370px", readOnly: true },
                    { type: "input", name: "projName", label: "Project", labelPosition: "left", labelWidth: 70, width: "390px", readOnly: true },
               ]
            },
          ]
        });        
        reqDetLayout.getCell("reqHeader").attach(formHeader);
        reqDetLayout.getCell("lTbReqDet").attach(tbReqDet);
        reqDetLayout.getCell("reqDetails").attach(gReqDetails);

        dhx.ajax.get("requestsQy.php?t=reqHeader&r="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            formHeader.setValue(obj);
        }).catch(function (err) {
            console.log(err);
        });
        loadReqDetailsProduct();

        dhxWindow.attach(reqDetLayout);
        dhxWindow.show();
    }
   
    function loadReqDetailsProduct() {
        dsReqDetails.removeAll();
        dsReqDetails.load("requestsQy.php?t=reqDetailsP&r="+wSelected).then(function(){
    //      console.log("done users read");
        });
    }
    function requestDetailsProductAdd(argument) {
        const dhxWindow = new dhx.Window({
            width: 785,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Request Info Product"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "665px",
            rows: [
                { type: "input", name: "reqpId", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "reqpArticle", required: false, label: "Code", labelWidth: "70px", labelPosition: "left", width: "190px" },
                { type: "input", name: "reqpName", required: true, label: "Description", labelWidth: "70px", labelPosition: "left", width: "300px" },
                { type: "input", name: "reqpQuant", label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "reqpPrice", required: false, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "combo", name: "reqpSfamId", label: "Sub-family", labelWidth: "70px", width: "300px", labelPosition: "left" },
                { type: "textarea", name: "reqpNotes", required: false, label: "Note", labelWidth: "70px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        cbSFam = form.getItem("reqpSfamId").getWidget();
        cbSFam.data.load("commonQy.php?t=fam&s="+wSerType).then(function(){
            dhx.ajax.get("requestsQy.php?t=reqDetailP&r="+wSelDet).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbSFam.setValue(obj.subfamId);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Request ",
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
                    header: "Request ",
                    text: "Confirm request creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("requestsWr.php?t=rqDetP&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadReqDetailsProduct();
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


// end request detail Product 

    function addEditRequest(argument) {
        const dhxWindow = new dhx.Window({
            width: 685,
            height: 460,
            closable: true,
            movable: true,
            modal: true,
            title: "Request Info"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "665px",
            rows: [
                { type: "input", name: "reqId", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "datepicker", name: "reqDate", label: "Date", labelPosition: "left", labelWidth: "70px", width: "250px", dateFormat: "%Y-%m-%d" },
                { id: "projId", name: "projId", type: "combo", label: "Project", labelWidth: "70px", width: "500px",  labelPosition: "left", maxlength: "200", gravity: "false", required: true },
                { id: "suppId", name: "suppId", type: "combo", label: "Supplier", labelWidth: "70px", width: "500px",  labelPosition: "left", maxlength: "200", gravity: "false", required: true },
                { type: "combo", name: "reqType", label: "Type", labelPosition: "left", labelWidth: "70px", width: "300px", required: true, data: [ {value: "Services", id: "s"}, {value: "Products", id: "p"}] },
                { type: "combo", name: "sfamId", label: "Sub-family", labelWidth: "70px", width: "300px", labelPosition: "left" },
                { type: "textarea", name: "reqNote", required: false, label: "Note", labelWidth: "70px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        cbSFam = form.getItem("sfamId").getWidget();
        cbSupplier = form.getItem("suppId").getWidget();
        cbProject = form.getItem("projId").getWidget();
        cbProject.data.load("commonQy.php?t=prjs").then(function(){
            dhx.ajax.get("requestsQy.php?t=request&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbProject.setValue(obj.projId);
                cbSFam.data.load("commonQy.php?t=fam&s="+obj.reqType).then(function(){
                    cbSFam.setValue(obj.subfamId);
                });
                cbSupplier.data.load("commonQy.php?t=sup").then(function(){
                    cbSupplier.setValue(obj.suppId);
                });
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'reqType' && wSelected == 0 ) {
                cbSFam.data.parse({});
                cbSFam.data.load("commonQy.php?t=fam&s="+new_value).then(function(){
                });            
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Request ",
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
                    header: "Request ",
                    text: "Confirm request creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("requestsWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadRequests();
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
        tbRequest.data.load("toolbarsQy.php?t=r_aed_s").then(function(){
        tbRequest.disable(['add','edit', 'delete','reqDet']); 
        if ( canCreate == 1 ) tbRequest.enable(['add']);
    });

    }
    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=requests").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;             
            loadToolbar();
            loadRequests();
        }).catch(function (err) {
            console.log(err);
        });
    };    

</script>