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
    var pLayout, tbProducts, tbPrices, tbSizes;
    var tbUsers, formProduct, gridPrices, cbTree;
    var gridSizes, cbSizeUnit, cbProdUnit;
    var wSelected = wSelPrice = wSelSize = 0;
    var gridProduct = '';

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { 
                type: "line",
                width: "700px",
                rows: [ 
                    { id: "hProduct", height: "55px",  },
                    { id: "lProduct", html: "" },
                ],
            },
            { type: "wide",
              rows: [
                 { id: "lOptions", html: "", height: "220px" },
                 { 
                    type: "none",
                    rows: [ 
                        { id: "hSizes", height: "55px",  },
                        { id: "lSizes", html: "" },
                    ]
                },
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

    tbProducts = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbProducts.events.on("input", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gridProduct.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbProducts.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditProduct(); }
        if ( id == 'add' )  { wSelected = 0; addEditProduct(); }
    });
    pLayout.getCell("hProduct").attach(tbProducts);

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

    tbSizes = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbSizes.data.load("toolbarsQy.php?t=s_aed").then(function(){
        tbSizes.disable(['edit', 'delete','add','addPrice']);
    });;
    tbSizes.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditSize(); }
        if ( id == 'addPrice' ) { addEditPrice(wSelSize); }
        if ( id == 'add' )  { wSelSize = 0; addEditSize(); }
    });
    pLayout.getCell("hSizes").attach(tbSizes);

    gridProduct = new dhx.Grid(null, {
        columns: [
            { id: "id", header: [{ text: "id" }], gravity: 1.5, hidden: true},
            { width: 100, id: "prodArticle", header: [{ text: "Article" }], gravity: 1.5, hidden: false},
            { id: "prodName", header: [{ text: "Designation" }], gravity: 1.5, hidden: false},
            { width: 100, id: "sfamName", header: [{ text: "Sub-family" }], gravity: 1.5, hidden: false},
            { width: 70, id: "unitcode", header: [{ text: "Unit" }], gravity: 1.5, hidden: false},
            { width: 50, id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true}
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridProduct.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbProducts.enable(['edit']);
        if ( canDelete == 1 ) tbProducts.enable(['delete']);
        wSelected = row.id;
        if(row.id !== ''){
            formProduct.clear();
            dhx.ajax.get("mntProductQy.php?t=product&r="+row.id).then(function (data) {
                obj = JSON.parse(data);
                formProduct.setValue(obj);
    //            mainForm.disable();
                formProduct.clear('validation');
            }).catch(function (err) {
                console.log(err);
            });
            
            if ( canCreate == 1 ) tbPrices.enable(['add']);
            if ( canCreate == 1 ) tbSizes.enable(['add']);
            loadPrices();
            loadSizes();
        }
    });
    loadProducts();

    function loadToolbar() {
        tbProducts.data.load("toolbarsQy.php?t=m_aeds").then(function(){
            tbProducts.disable(['edit', 'delete','add']);
            if ( canCreate == 1 ) tbProducts.enable(['add']);
        });
    }

    function loadProducts() {
        gridProduct.data.load("mntProductQy.php?t=products").then(function(){    });
    };
    function loadPrices() {
        gridPrices.data.load("mntProductQy.php?t=prices&c="+wSelected).then(function(){ });
    };
    function loadSizes() {
        gridSizes.data.load("mntProductQy.php?t=sizes&c="+wSelected).then(function(){ });
    };

    formProduct = new dhx.Form(null, {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 5,
      rows: [
        { type: "input", name: "prodId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true, hidden: true },
        { type: "input", name: "prodArticle", label: "Article", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "prodName", label: "Designation", labelPosition: "left", labelWidth: 100, readOnly: true },
        {      
            align: "start", 
            cols: [
                { type: "input", name: "prodType", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readOnly: true },
                { type: "input", name: "prodUnit", label: "Unit", labelPosition: "left", labelWidth: 70, width: "230px", readOnly: true },
            ]
        },
        {      
            align: "start", 
            cols: [
                 { type: "input", name: "prodWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true },        
                { id: "qtBuy", name: "qtBuy", type: "input", label: "Buy qt.", labelWidth: 70, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start", readOnly: true  },
                { id: "qtSell", name: "qtSell", type: "input", label: "Sell qt.", labelWidth: 70, width: "170px", labelPosition: "left", validation: "validInteger", gravity: "false", align: "start", readOnly: true  },
            ]
        }
      ]
    });


    gridPrices = new dhx.Grid(null, {
        columns: [
            { id: "prodPrcId", width:10, gravity: 1.5, hidden: true, header: [{ text: "Id"}] },
            { id: "dtFrom", width:110, gravity: 1.5, header: [{ text: "Valid from"}] },
            { id: "dtTo", width:110, gravity: 1.5, header: [{ text: "Valid until"}] },
            { id: "size", gravity: 1.5, header: [{ text: "Size"}] },
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

    gridSizes = new dhx.Grid(null, {
        columns: [
            { id: "psId", width:10, gravity: 1.5, hidden: true, header: [{ text: "Id"}] },
            { id: "sizeId", width:10, gravity: 1.5, hidden: true, header: [{ text: "Id"}] },
            { id: "sizeCode", gravity: 1.5, header: [{ text: "Size"}], align: "left" },
            { id: "psWeight", gravity: 1.5, header: [{ text: "Weight"}], align: "right" }, 
            { id: "psWidth", gravity: 1.5, header: [{ text: "Width"}] , align: "right"}, 
            { id: "psHeight", gravity: 1.5, header: [{ text: "Height"}], align: "right" }, 
            { id: "psLength", gravity: 1.5, header: [{ text: "Length"}], align: "right" }, 
            { id: "unitCode", width:60, gravity: 1.5, header: [{ text: "Unit."}] }
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridSizes.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbSizes.enable(['edit']);
        if ( canModify == 1 ) tbSizes.enable(['addPrice']);
        if ( canDelete == 1 ) tbSizes.enable(['delete']);
        wSelSize = row.sizeId;
    });

    pLayout.getCell("lProduct").attach(gridProduct);
    pLayout.getCell("lOptions").attach(formProduct);
    pLayout.getCell("lPrices").attach(gridPrices);
    pLayout.getCell("lSizes").attach(gridSizes);
   
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
                { type: "input", label: "Id", labelPosition: "left", labelWidth: 100, name: "prodId", width: "200px", hidden: true },
                { type: "input", label: "Article", labelPosition: "left", labelWidth: 100, name: "prodArticle", width: "300px" },
                { type: "input", label: "Designation", labelPosition: "left", labelWidth: 100, name: "prodName" },
                { type: "combo", name: "prodTypeId", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readonly: true },
                { type: "combo", name: "prodUnitId", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                { type: "input", name: "prodWeight", label: "Weight", labelPosition: "left", labelWidth: 100, width: "200px" },        
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
        cbProdType = form.getItem("prodTypeId").getWidget();
        cbProdType.data.load("commonQy.php?t=fam&s=p").then(function(){
        });

        cbProdUnit = form.getItem("prodUnitId").getWidget();
        cbProdUnit.data.load("commonQy.php?t=un").then(function(){
            dhx.ajax.get("mntProductQy.php?t=product&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbProdUnit.setValue(cbProdUnit.data.getId(obj.prodUnit));
                cbProdType.setValue(cbProdType.data.getId(obj.prodType));
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
                        const send = form.send("mntProductWr.php?t=f&r="+wSelected, "POST").then(function(data){
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
    function addEditPrice(argument=0) {
        const dhxWindow = new dhx.Window({
            width: 480,
            height: 400,
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
                { type: "combo", id: "pSize", name: "pSize", label: "Size", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                { type: "input", id: "amount", name: "amount", label: "Price", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
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

        cbSizeUnit = form.getItem("pSize").getWidget();

        dhx.ajax.get("mntProductQy.php?t=price&r="+wSelPrice+"&s="+argument).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            form.setValue(obj);
            cbSizeUnit.data.load("commonQy.php?t=sizes").then(function(){
                cbSizeUnit.setValue(obj.pSize);
            });
            form.getItem("pSize").disable();
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
                        const send = form.send("mntProductWr.php?t=fp&r="+wSelPrice+"&p="+wSelected, "POST").then(function(data){
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
    function addEditSize(argument) {
        const dhxWindow = new dhx.Window({
            width: 450,
            height: 480,
            closable: true,
            movable: true,
            modal: true,
            title: "Size"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "435px",
            padding: 5,
            rows: [
                { type: "input", name: "psId", label: "Id", labelPosition: "left", labelWidth: 100, width: "50px", hidden: true },
                { type: "combo", name: "sizeId", label: "Size", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                { id: "psWeight", name: "psWeight", type: "input", label: "Weight", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { id: "psWidth", name: "psWidth", type: "input", label: "Width", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { id: "psHeight", name: "psHeight", type: "input", label: "Height", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { id: "psLength", name: "psLength", type: "input", label: "Length", labelWidth: 100, width: "200px", labelPosition: "left", validation: "validNumeric", gravity: "false", align: "start"  },
                { type: "combo", name: "psUnitId", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });
        cbProdUnit = form.getItem("psUnitId").getWidget();
        cbProdUnit.data.load("commonQy.php?t=un").then(function(){
        });

        cbSizeUnit = form.getItem("sizeId").getWidget();
        cbSizeUnit.data.load("commonQy.php?t=sizes").then(function(){
            dhx.ajax.get("mntProductQy.php?t=size&r="+wSelSize).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbProdUnit.setValue(obj.psUnitId);
                cbSizeUnit.setValue(obj.sizeId);
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Size ",
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
                    header: "Size ",
                    text: "Confirm size creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntProductWr.php?t=fs&r="+wSelSize+"&p="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadSizes();
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