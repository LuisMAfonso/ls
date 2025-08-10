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
    var pLayout, tbConsumables, tbPrices;
    var tbUsers, formConsumable, gridPrices, cbTree;
    var wSelected = wSelPrice = 0;
    var gridConsumable = '';

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { 
                type: "line",
                width: "700px",
                rows: [ 
                    { id: "hConsumable", height: "55px",  },
                    { id: "lConsumable", html: "" },
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

    tbConsumables = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbConsumables.events.on("input", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gridConsumable.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbConsumables.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditProduct(); }
        if ( id == 'add' )  { wSelected = 0; addEditProduct(); }
    });
    pLayout.getCell("hConsumable").attach(tbConsumables);

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

    gridConsumable = new dhx.Grid(null, {
        columns: [
            { id: "id", header: [{ text: "id" }], gravity: 1.5, hidden: true},
            { width: 100, id: "csmbArticle", header: [{ text: "Article" }], gravity: 1.5, hidden: false},
            { id: "csmbName", header: [{ text: "Designation" }], gravity: 1.5, hidden: false},
            { width: 100, id: "sfamName", header: [{ text: "Sub-family" }], gravity: 1.5, hidden: false},
            { width: 70, id: "unitcode", header: [{ text: "Unit" }], gravity: 1.5, hidden: false},
            { width: 50, id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true}
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridConsumable.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbConsumables.enable(['edit']);
        if ( canDelete == 1 ) tbConsumables.enable(['delete']);
        wSelected = row.id;
        if(row.id !== ''){
            formConsumable.clear();
            dhx.ajax.get("mntConsumQy.php?t=consumable&r="+row.id).then(function (data) {
                obj = JSON.parse(data);
                formConsumable.setValue(obj);
    //            mainForm.disable();
                formConsumable.clear('validation');
            }).catch(function (err) {
                console.log(err);
            });
            
            if ( canCreate == 1 ) tbPrices.enable(['add']);
            loadPrices();
        }
    });
    loadProducts();

    function loadToolbar() {
        tbConsumables.data.load("toolbarsQy.php?t=m_aeds").then(function(){
            tbConsumables.disable(['edit', 'delete','add']);
            if ( canCreate == 1 ) tbConsumables.enable(['add']);
        });
    }
    function loadProducts() {
        gridConsumable.data.load("mntConsumQy.php?t=consumables").then(function(){    });
    };
    function loadPrices() {
        gridPrices.data.load("mntConsumQy.php?t=prices&c="+wSelected).then(function(){ });
    };

    formConsumable = new dhx.Form(null, {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 5,
      rows: [
        { type: "input", name: "csmbId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true },
        { type: "input", name: "csmbArticle", label: "Article", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "csmbName", label: "Designation", labelPosition: "left", labelWidth: 100, readOnly: true },
        { type: "input", name: "csmbType", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readOnly: true },
        { type: "input", name: "csmbUnit", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "csmbWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true },        
        {      
            align: "start", 
            cols: [
                { id: "qtBuy", name: "qtBuy", type: "input", label: "Buy qt.", labelWidth: 100, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start", readOnly: true
                },
                { id: "qtSell", name: "qtSell", type: "input", label: "Sell qt.", labelWidth: 100, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start", readOnly: true
                },
            ]
        }
      ]
    });


    gridPrices = new dhx.Grid(null, {
        columns: [
            { id: "csmbPrcId", width:10, gravity: 1.5, hidden: true, header: [{ text: "Id"}] },
            { id: "dtFrom", width:110, gravity: 1.5, header: [{ text: "Valide from"}] },
            { id: "dtTo", width:110, gravity: 1.5, header: [{ text: "Valide until"}] },
            { id: "amount", gravity: 1.5, header: [{ text: "Price"}] },
            { id: "priceBS", width:130, gravity: 1.5, header: [{ text: "Qt. Buy/Sell"}] }
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

    pLayout.getCell("lConsumable").attach(gridConsumable);
    pLayout.getCell("lOptions").attach(formConsumable);
    pLayout.getCell("lPrices").attach(gridPrices);
   
    function addEditProduct(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 435,
            closable: true,
            movable: true,
            modal: true,
            title: "Products"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            padding: 5,
            rows: [
                { type: "input", name: "csmbId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", hidden: true },
                { type: "input", name: "csmbArticle", label: "Article", labelPosition: "left", labelWidth: 100, width: "300px" },
                { type: "input", name: "csmbName", label: "Designation", labelPosition: "left", labelWidth: 100 },
                { type: "combo", name: "csmbTypeId", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readonly: true },
                { type: "combo", name: "csmbUnitId", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                { type: "input", name: "csmbWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px" },        
                {      
                    align: "start", 
                    cols: [
                        { id: "qtBuy", name: "qtBuy", type: "input", label: "Buy qt.", labelWidth: 100, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start"
                        },
                        { id: "qtSell", name: "qtSell", type: "input", label: "Sell qt.", labelWidth: 100, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start"
                        },
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
        cbCsmbType = form.getItem("csmbTypeId").getWidget();
        cbCsmbType.data.load("commonQy.php?t=fam&s=c").then(function(){
        });

        cbCsmbUnit = form.getItem("csmbUnitId").getWidget();
        cbCsmbUnit.data.load("commonQy.php?t=un").then(function(){
            dhx.ajax.get("mntConsumQy.php?t=consumable&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbCsmbUnit.setValue(cbCsmbUnit.data.getId(obj.csmbUnit));
                cbCsmbType.setValue(cbCsmbType.data.getId(obj.csmbType));
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Consumable ",
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
                    header: "Consumable ",
                    text: "Confirm Consumable creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntConsumWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadProducts();
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
                { type: "input", name: "prodPrcId", label: "Id", labelPosition: "left", labelWidth: 100, width: "150px", hidden: true },
                { type: "datepicker", name: "dtFrom", label: "Valid from", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { type: "datepicker", name: "dtTo", label: "Valid until", labelPosition: "left", labelWidth: 100, timePicker: false, dateFormat: "%Y-%m-%d" },
                { id: "amount", name: "amount", type: "input", label: "Price", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { type: "combo", name: "priceBS", label: "Price for", labelPosition: "left", labelWidth: 100, width: "300px", data: [ {value: "Buy quantity", id: "0"}, {value: "Sell quantity", id: "1"}] },
                {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });

        dhx.ajax.get("mntConsumQy.php?t=price&r="+wSelPrice).then(function (data) {
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
                        const send = form.send("mntConsumWr.php?t=fp&r="+wSelPrice+"&p="+wSelected, "POST").then(function(data){
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
        dhx.ajax.get("menuQy.php?t=per&p=Consumables").then(function (data) {
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