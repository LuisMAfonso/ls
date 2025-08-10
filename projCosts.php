<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>
<div id="layout" style="height: 100vh;"></div>

<?php
require_once('sidebar.php');
?>
<style>
    .column--estimate {
        background: #f9e79f;        
    }
    .column--positive {
        background: #d5f5e3;        
    }
    .column--negative {
        background: #f5b7b1;        
    }
</style>
<script>
    var pLayout;
    var pGridprojects, dsProjects;
    var tbProjects, cbCountry, tbRequests;
    var gRequests, dsRequests, gLabour, dsLabour, gTotals, dsTotals;
    var wSelected = wSelLabour = wSelRequest = 0;

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "line",
        cols: [
            {
                type: "space",
                rows: [
                    { type: "line",
                      rows: [ 
                      	{ id: "ltbProjects", html: "", height: "55px" },
                      	{ id: "projects", html: "" },
                      ]
                    },
                    { type: "line", height: "255px", 
                      rows: [ 
                        { id: "projTotal", html: "", },
                      ]
                    },
                ]
            },
            {
                type: "space",
                rows: [
                    { type: "line",
                      rows: [ 
                        { id: "lTbRequests", html: "", height: "55px" },
                        { id: "requests", html: "" },
                      ]
                    },
                    { type: "line", height: "300px", 
                      rows: [ 
                        { id: "labours", html: "", },
                      ]
                    },
                ]
            }
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    dsProjects = new dhx.DataCollection();
    dsRequests = new dhx.DataCollection();
    dsLabour = new dhx.DataCollection();
    dsTotals = new dhx.DataCollection();
    loadprojects();

    function loadprojects() {
        dsProjects.removeAll();
        dsProjects.load("projCostsQy.php?t=projects").then(function(){
    //      console.log("done users read");
        });
    }

    tbProjects = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbProjects.data.load("toolbarsQy.php?t=j_search").then(function(){
    });;
	tbProjects.events.on("input", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridprojects.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbProjects.events.on("click", function(id,e){
        console.log(id);
    });
	pLayout.getCell("ltbProjects").attach(tbProjects);

    pGridprojects = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "projName", header: [{ text: "Number" }], autoWidth: true },
            { width: 150, minWidth: 150, id: "projCity", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 45, id: "icon", header: [{ text: "S." }], align: "center", htmlEnable: true  },
        ],
        selection:"row",
        adjust: "true", 
        data: dsProjects
    });
    pGridprojects.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        if ( canModify == 1 ) tbProjects.enable(['edit']);
        if ( canDelete == 1 ) tbProjects.enable(['delete']);

        showRequests();
        showLabour();
        showTotals();
    });
    pLayout.getCell("projects").attach(pGridprojects);

    tbRequests = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbRequests.data.load("toolbarsQy.php?t=r_ae").then(function(){
    });;
    tbRequests.events.on("click", function(id,e){
        console.log(id);
    });
    pLayout.getCell("lTbRequests").attach(tbRequests);

    gRequests = new dhx.Grid(null, {
        columns: [
            { width: 10, id: "reqId", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 45, id: "icon", header: [{ text: "S." }], align: "center", autoWidth: true, htmlEnable: true  },
            { width: 0, minWidth: 100,id: "suppName", header: [{ text: "Supplier" }], autoWidth: true },
            { width: 100, id: "reqDate", header: [{ text: "Date" }], autoWidth: true, align: "left" },
            { width: 130, id: "sfamName", header: [{ text: "Tipo" }], autoWidth: true, align: "left" },
            { width: 90, id: "reqStatus", header: [{ text: "Status" }], autoWidth: true, align: "left" },
            { width: 100, id: "amount", header: [{ text: "Value" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsRequests 
    });
    gRequests.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelRequest = row.Id;
        if ( canModify == 1 ) tbRequests.enable(['edit']);

    });
    pLayout.getCell("requests").attach(gRequests);

    gLabour = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "N." }], autoWidth: true },
            { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true },
            { width: 150, minWidth: 150, id: "posName", header: [{ text: "Position" }], autoWidth: true, align: "left" },
            { width: 70, id: "tTime", header: [{ text: "Hours" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
            { width: 90, id: "tValue", header: [{ text: "Value" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsLabour
    });
    gLabour.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelLabour = row.Id;

    });
    pLayout.getCell("labours").attach(gLabour);

    gTotals = new dhx.Grid(null, {
        columns: [
            { width: 45, id: "icon", header: [{ text: "S." }], align: "center", htmlEnable: true  },
            { width: 0, minWidth: 100, id: "tType", header: [{ text: "Type" }], autoWidth: true, align: "left" },
            { width: 70, id: "qt", header: [{ text: "Qt." }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," } },
            { width: 90, id: "vReq", header: [{ text: "Request" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" }, 
            { width: 90, id: "vAcc", header: [{ text: "Accepted" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
            { width: 90, id: "vDel", header: [{ text: "Delivered" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
            { width: 90, id: "vEst", header: [{ text: "Estimated" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum", mark: () => ("column--estimate"), },
           { width: 90, id: "vProf", header: [{ text: "Profit" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum",  mark: function (cell, data, row, col) { return cell >= 0 ? "column--positive" : "column--negative" } },
        ],
        selection:"row",
        adjust: "true", 
        data: dsTotals
    });
    pLayout.getCell("projTotal").attach(gTotals);
    
    function showRequests() {
        dsRequests.removeAll();
        dsRequests.load("projCostsQy.php?t=requests&p="+wSelected).then(function(){
        });
    }
    function showLabour() {
        dsLabour.removeAll();
        dsLabour.load("projCostsQy.php?t=labour&p="+wSelected).then(function(){
        });
    }
    function showTotals() {
        dsTotals.removeAll();
        dsTotals.load("projCostsQy.php?t=totals&p="+wSelected).then(function(){
        });
    }

    mainLayout.getCell("workplace").attach(pLayout);

    function addEditUser(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "contomers"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            rows: [
              {
                name: "fieldset1",
                type: "fieldset",
                label: "Info",        
                rows:[
                    {      
                    align: "start", 
                        cols: [
                            { id: "contId", name: "contId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "contName", name: "contName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "contomer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "contAddress", name: "contAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "contZipcode", name: "contZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "contCity", name: "contCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "contCountry", name: "contCountry", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "contEmail", name: "contEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "e-mail cliente", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "contPhone", name: "contPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            }
                       ]
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
        });
        cbCountry = form.getItem("contCountry").getWidget();
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("projCostsQy.php?t=contact&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbCountry.setValue(cbCountry.data.getId(obj.contCountry));
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });;

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "contomer ",
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
                    text: "Confirm contomer creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("projCostsWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadprojects();
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
        dhx.ajax.get("menuQy.php?t=per&p=projCosts").then(function (data) {
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