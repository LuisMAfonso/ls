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
    var pGrid, dsProdSize, dsPSprice;
    var tbGrid, pspGrid, tbPSprice, cbUnit;
    var wSelected = wSelPSP = 0;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lyToolbar", html: "", height: "55px" },
              	{ id: "lyGrid", html: "" },
              ]
            },
            { type: "line",
              rows: [ 
                { id: "lyToolbarPsp", html: "", height: "55px" },
                { id: "lyGridPsp", html: "" },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    dsProdSize = new dhx.DataCollection();
    dsPSprice = new dhx.DataCollection();
    loadPermission()
    
    function loadGrid() {
        dsProdSize.removeAll();
        dsProdSize.load("tbProdSizeQy.php?t=grid").then(function(){
            if ( canCreate == 1 ) tbGrid.enable(['add']);
        });
    }
    function loadPrices() {
        dsPSprice.removeAll();
        dsPSprice.load("tbProdSizeQy.php?t=prices&id="+wSelected).then(function(){  });
    }

    tbGrid = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbGrid.data.load("toolbarsQy.php?t=m_aed").then(function(){
        tbGrid.disable(['edit', 'delete','add']);
    });;
    tbGrid.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditSize(); }
        if ( id == 'add' )  { wSelected = 0; addEditSize(); }
    });
	pLayout.getCell("lyToolbar").attach(tbGrid);

    tbPSprice = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbPSprice.data.load("toolbarsQy.php?t=t_aed").then(function(){
        tbPSprice.disable(['edit', 'delete','add']);
    });;
    tbPSprice.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditPrice(); }
        if ( id == 'add' )  { wSelPSP = 0; addEditPrice(); }
    });
    pLayout.getCell("lyToolbarPsp").attach(tbPSprice);

    pGrid = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 100, id: "code", header: [{ text: "Code" }], align: "left", htmlEnable: true  },
            { width: 0, id: "descript", header: [{ text: "Description" }], autoWidth: true, align: "left" },
        ],
        selection:"row",
        adjust: "true",
        data: dsProdSize
    });
    pGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        loadPrices();
        if ( canModify == 1 ) tbGrid.enable(['edit']);
        if ( canDelete == 1 ) tbGrid.enable(['delete']);
        if ( canModify == 1 ) tbPSprice.enable(['add']);

    });
    pLayout.getCell("lyGrid").attach(pGrid);

    pspGrid = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { id: "dtFrom", width:110, gravity: 1.5, header: [{ text: "Valid from"}] },
            { id: "dtTo", width:110, gravity: 1.5, header: [{ text: "Valid until"}] },
            { id: "pspAmount", gravity: 1.5, header: [{ text: "Price"}], align: "right" },
            { id: "pspQuant", gravity: 1.5, header: [{ text: "Quantity"}], align: "right"  },
            { id: "pspUnit", width:70, gravity: 1.5, header: [{ text: "Unit"}] },
        ],
        selection:"row",
        adjust: "true",
        data: dsPSprice
    });
    pspGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelPSP = row.Id;
        if ( canModify == 1 ) pspGrid.enable(['edit']);
        if ( canDelete == 1 ) pspGrid.enable(['delete']);

    });
    pLayout.getCell("lyGridPsp").attach(pspGrid);

    function addEditSize(argument) {
        const dhxWindow = new dhx.Window({
            width: 540,
            height: 260,
            closable: true, 
            movable: true,
            modal: true,
            title: "Notes types"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "code", required: false, label: "Code", labelPosition: "left", labelWidth: "120px", },
                { type: "input", name: "descript", required: true, label: "Description", labelPosition: "left", labelWidth: "80px", },
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
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Note type ",
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
                    header: "Note type ",
                    text: "Confirm note type creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("tbProdSizeWr.php?t=f", "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadGrid();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhx.ajax.get("tbProdSizeQy.php?t=r&id="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            form.setValue(obj);
        }).catch(function (err) {
                console.log(err);
        });
        dhxWindow.attach(form);
        dhxWindow.show();

    }
    function addEditPrice() {
        const dhxWindow = new dhx.Window({
            width: 480,
            height: 400,
            closable: true,
            movable: true,
            modal: true,
            title: "Instalation price"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "435px",
            padding: 5,
            rows: [
                { type: "input", name: "pspId", label: "Id", labelPosition: "left", labelWidth: 100, width: "150px", hidden: true },
                { type: "datepicker", name: "dtFrom", label: "Valid from", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { type: "datepicker", name: "dtTo", label: "Valid until", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { type: "input", id: "pspAmount", name: "pspAmount", label: "Amount", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { type: "input", id: "pspQuant", name: "pspQuant", label: "Quantity", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { type: "combo", name: "pspUnit", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", },
                {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });

        cbUnit = form.getItem("pspUnit").getWidget();
        cbUnit.data.load("commonQy.php?t=un").then(function(){  });

        dhx.ajax.get("tbProdSizeQy.php?t=price&id="+wSelPSP).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            form.setValue(obj);
            cbUnit.data.load("commonQy.php?t=un").then(function(){
                cbUnit.setValue(obj.pspUnit);
            });
            console.log(obj);
        }).catch(function (err) {
                console.log(err);
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Price ",
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
                    header: "Price ",
                    text: "Confirm price creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("tbProdSizeWr.php?t=fp&r="+wSelPSP+"&v="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadPrices();
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
        dhx.ajax.get("menuQy.php?t=per&p=tbProdSize").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;     
            loadGrid();        
        }).catch(function (err) {
            console.log(err);
        });
    };
</script>