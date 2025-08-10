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
    var pLayout, tbTools, tbPrices;
    var tbUsers, formTool, gridPrices, cbTree;
    var wSelected = wSelPrice = 0;
    var gridTool = '';

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { 
                type: "line",
                width: "700px",
                rows: [ 
                    { id: "hTool", height: "55px",  },
                    { id: "lTool", html: "" },
                ],
            },
            { type: "wide",
              rows: [
                 { id: "lOptions", html: "", height: "65%" },
                 { 
                    type: "none",
                    rows: [ 
                        { id: "hPrices", height: "55px",  },
                        { id: "lPrices", html: "" },
                    ]
                },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    tbTools = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbTools.events.on("input", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gridTool.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbTools.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditTool(); }
        if ( id == 'add' )  { wSelected = 0; addEditTool(); }
    });
    pLayout.getCell("hTool").attach(tbTools);

    tbPrices = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbPrices.data.load("toolbarsQy.php?t=t_aed").then(function(){
        tbPrices.disable(['edit', 'delete','add']);
    });;
    tbPrices.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditPrice(); }
        if ( id == 'add' )  { wSelPrice = 0; addEditPrice(); }
    });
    pLayout.getCell("hPrices").attach(tbPrices);

    gridTool = new dhx.Grid(null, {
        columns: [
            { id: "id", header: [{ text: "id" }], gravity: 1.5, hidden: true},
            { width: 50, id: "toolId", header: [{ text: "Id" }], gravity: 1.5, hidden: false},
            { id: "toolName", header: [{ text: "Designation" }], gravity: 1.5, hidden: false},
            { width: 100, id: "sfamName", header: [{ text: "Sub-family" }], gravity: 1.5, hidden: false},
            { width: 200, id: "toolTypeName", header: [{ text: "Type" }], gravity: 1.5, hidden: false},
            { width: 50, id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true}
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridTool.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbTools.enable(['edit']);
        if ( canDelete == 1 ) tbTools.enable(['delete']);
        wSelected = row.id;
        if(row.id !== ''){
            formTool.clear();
            dhx.ajax.get("mntToolsQy.php?t=tool&r="+row.id).then(function (data) {
                obj = JSON.parse(data);
                formTool.setValue(obj);
    //            mainForm.disable();
                formTool.clear('validation');
            }).catch(function (err) {
                console.log(err);
            });
            
            if ( canCreate == 1 ) tbPrices.enable(['add']);
            loadPrices();
        }
    });
    loadTools();

    function loadToolbar() {
        tbTools.data.load("toolbarsQy.php?t=m_aeds").then(function(){
        tbTools.disable(['edit', 'delete','add']);
        if ( canCreate == 1 ) tbTools.enable(['add']);
    });
 
    }
    function loadTools() {
        gridTool.data.load("mntToolsQy.php?t=tools").then(function(){    });
    };
    function loadPrices() {
        gridPrices.data.load("mntToolsQy.php?t=prices&c="+wSelected).then(function(){ });
    };

    formTool = new dhx.Form(null, {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 5,
      rows: [
        { type: "input", name: "toolId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true },
        { type: "input", name: "toolName", label: "Designation", labelPosition: "left", labelWidth: 100, readOnly: true },
        { type: "input", name: "toolSerialNumber", label: "Serial number", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "toolLabelCode", label: "Label code", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "toolEquipmentType", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readOnly: true },
        { type: "input", name: "toolType", label: "Type", labelPosition: "left", labelWidth: 100, width: "450px", readOnly: true },
        { type: "input", name: "toolWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true }
      ]
    });


    gridPrices = new dhx.Grid(null, {
        columns: [
            { id: "toolPrcId", width:10, gravity: 1.5, hidden: true, header: [{ text: "Id"}] },
            { id: "dtFrom", width:110, gravity: 1.5, header: [{ text: "Valide from"}] },
            { id: "dtTo", width:110, gravity: 1.5, header: [{ text: "Valide until"}] },
            { id: "amount", gravity: 1.5, header: [{ text: "Price"}] },
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridPrices.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbPrices.enable(['edit']);
        if ( canDelete == 1 ) tbPrices.enable(['delete']);
        wSelPrice = row.prodPrcId;
    });

    pLayout.getCell("lTool").attach(gridTool);
    pLayout.getCell("lOptions").attach(formTool);
    pLayout.getCell("lPrices").attach(gridPrices);
   
    function addEditTool(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 435,
            closable: true,
            movable: true,
            modal: true,
            title: "Tool"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            padding: 5,
            rows: [
                { type: "input", name: "toolId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", hidden: true },
                { type: "input", name: "toolName", label: "Designation", labelPosition: "left", labelWidth: 100 },
                { type: "input", name: "toolSerialNumber", label: "Serial number", labelPosition: "left", labelWidth: 100, width: "300px" },
                { type: "input", name: "toolLabelCode", label: "Label code", labelPosition: "left", labelWidth: 100, width: "300px" },
                { type: "combo", name: "toolEquipmentTypeId", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readonly: true },
                { type: "combo", name: "toolTypeId", label: "Type", labelPosition: "left", labelWidth: 100, width: "400px", readonly: true },
                { type: "input", name: "toolWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px" },        
                {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });
        cbToolEquipType = form.getItem("toolEquipmentTypeId").getWidget();
        cbToolEquipType.data.load("commonQy.php?t=fam&s=e").then(function(){
        });

        cbToolType = form.getItem("toolTypeId").getWidget();
        cbToolType.data.load("commonQy.php?t=tl").then(function(){
            dhx.ajax.get("mntToolsQy.php?t=tool&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbToolType.setValue(cbToolType.data.getId(obj.toolType));
                cbToolEquipType.setValue(cbToolEquipType.data.getId(obj.toolEquipmentType));
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Product ",
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
                    header: "Product ",
                    text: "Confirm product creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntToolsWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadTools();
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
    function addEditPrice(argument) {
        const dhxWindow = new dhx.Window({
            width: 480,
            height: 350,
            closable: true,
            movable: true,
            modal: true,
            title: "Price"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "435px",
            padding: 5,
            rows: [
                { type: "input", name: "toolPrcId", label: "Id", labelPosition: "left", labelWidth: 100, width: "150px", hidden: true },
                { type: "datepicker", name: "dtFrom", label: "Valid from", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { type: "datepicker", name: "dtTo", label: "Valid until", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { id: "amount", name: "amount", type: "input", label: "Price", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                 {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });

        dhx.ajax.get("mntToolsQy.php?t=price&r="+wSelPrice).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            form.setValue(obj);
            var obj = form.getValue();
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
                        const send = form.send("mntToolsWr.php?t=fp&r="+wSelPrice+"&p="+wSelected, "POST").then(function(data){
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
        dhx.ajax.get("menuQy.php?t=per&p=products").then(function (data) {
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