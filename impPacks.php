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
    var pLayout, tbPacks, tbPackArt, cbProdUnit;
    var tbUsers, formProduct, gridPackArt, cbTree, cbCode, cbType;
    var wSelected = wSelDet = 0;
    var wDetType = '';
    var gridPacks = '';
    var impType = '';

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { 
                type: "line",
                rows: [ 
                    { id: "hPack", height: "55px",  },
                    { id: "lPack", html: "" },
                ],
            },
            { type: "wide",
              width: "600px",
              rows: [
                 { 
                    type: "none",
                    rows: [ 
                        { id: "hPackArt", height: "55px",  },
                        { id: "lPackArt", html: "" },
                    ]
                },
                { id: "lOptions", html: "", height: "300px" },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    tbPacks = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbPacks.data.load("toolbarsQy.php?t=ip_aeds").then(function(){
        tbPacks.disable(['edit', 'delete','add']);
        if ( canCreate == 1 ) tbPacks.enable(['add']);
        const opt = tbPacks.getState();
        impType = opt['iType'];
        loadPacks();
    });
    tbPacks.events.on("inputChange", function (event, arguments) {
//        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gridPacks.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbPacks.events.on("click", function(id,e){
        const opt = tbPacks.getState();
        console.log(id+" - "+id.substring(0,3)+" -> "+opt.iType);
        if ( id != 'iType' && id != 'edit' && id != 'add' ) {
            impType = opt.iType;
            console.log(impType);
            loadPacks();
        }
//        if ( id == 'iType' ) { impType = opt['iType']; }
        if ( id == 'edit' ) { addEditPack(); }
        if ( id == 'add' )  { wSelected = 0; addEditPack(); }
    });
    pLayout.getCell("hPack").attach(tbPacks);

    tbPackArt = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbPackArt.data.load("toolbarsQy.php?t=k_aed").then(function(){
        tbPackArt.disable(['edit', 'delete','add_l', 'add_p', 'add_e', 'add_s', 'add_c']);
    });;
    tbPackArt.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'delete' ) { delete_line(); }
        if ( id == 'edit' ) {  
            if ( wDetType == 'l' )  { add_l_line(); }
            if ( wDetType == 's' )  { add_s_line(); }
            if ( wDetType == 'e' )  { add_e_line(); }
            if ( wDetType == 'p' )  { add_p_line(); }
        }
        if ( id == 'add_l' )  { wSelDet = 0; add_l_line(); }
        if ( id == 'add_s' )  { wSelDet = 0; add_s_line(); }
        if ( id == 'add_e' )  { wSelDet = 0; add_e_line(); }
        if ( id == 'add_p' )  { wSelDet = 0; add_p_line(); }
    });
    pLayout.getCell("hPackArt").attach(tbPackArt);

    gridPacks = new dhx.Grid(null, {
        columns: [
            { id: "id", header: [{ text: "id" }], gravity: 1.5, hidden: true},
            { width: 50, id: "Icon", header: [{ text: "Icon" }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true},
            { width: 130, id: "ipCode", header: [{ text: "Code" }], gravity: 1.5, hidden: false},
            { id: "ipDesig", header: [{ text: "Designation" }], gravity: 1.5, hidden: false},
            { width: 170, id: "sfamName", header: [{ text: "Sub-family" }], gravity: 1.5, hidden: false},
            { width: 50, id: "ipUnit", header: [{ text: "Un." }], gravity: 1.5, hidden: false},
         ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridPacks.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbPacks.enable(['edit']);
        if ( canDelete == 1 ) tbPacks.enable(['delete']);
        wSelected = row.id;
        if(row.id !== ''){
            formProduct.clear();
            dhx.ajax.get("impPacksQy.php?t=pack&r="+row.id+"&ip="+impType).then(function (data) {
                obj = JSON.parse(data);
                formProduct.setValue(obj);
    //            mainForm.disable();
                formProduct.clear('validation');
            }).catch(function (err) {
                console.log(err);
            });
            
            if ( canCreate == 1 ) tbPackArt.enable(['add_l', 'add_p', 'add_e', 'add_s', 'add_c']);
            loadPackDetails();
        }
    });

    function loadPacks() {
        gridPacks.data.load("impPacksQy.php?t=packs&ip="+impType).then(function(){    });
    };
    function loadPackDetails() {
        gridPackArt.data.load("impPacksQy.php?t=detail&c="+wSelected).then(function(){
        });
    }

    formProduct = new dhx.Form(null, {
      css: "dhx_widget--bg_white dhx_widget--bordered",
      padding: 5,
      rows: [
        { type: "input", name: "bkId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true },
        { type: "input", name: "bkCode", label: "Article", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "bkDesig", label: "Designation", labelPosition: "left", labelWidth: 100, readOnly: true },
        { type: "input", name: "sfamName", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readOnly: true },
        { type: "input", name: "BomKitDesig", label: "BOM / KIT", labelPosition: "left", labelWidth: 100, width: "300px", readOnly: true },
        { type: "input", name: "bkUnit", label: "Unit", labelPosition: "left", labelWidth: 100, width: "200px", readOnly: true }
      ]
    });


    gridPackArt = new dhx.Grid(null, {
        columns: [
            { id: "bkdId", header: [{ text: "id" }], gravity: 1.5, hidden: true},
            { width: 50, id: "typeIcon", header: [{ text: "Type" }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true},
            { width: 50, id: "Icon", header: [{ text: "Sub." }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true},
            { id: "bkDesig", header: [{ text: "Designation" }], gravity: 1.5, hidden: false},
            { width: 100, id: "bkdQuant", header: [{ text: "Quantity" }], gravity: 1.5, hidden: false},
        ],
        autoWidth: true,
        css: "alternate_row",
        selection: "row"
    });
    gridPackArt.events.on("cellClick", function(row,column,e){
        if ( canModify == 1 ) tbPackArt.enable(['edit']);
        if ( canDelete == 1 ) tbPackArt.enable(['delete']);
        wSelDet = row.id;
        if(row.id !== '') {
            wDetType = row.famType;
//            console.log(wDetType);
        }
    });

    pLayout.getCell("lPack").attach(gridPacks);
    pLayout.getCell("lOptions").attach(formProduct);
    pLayout.getCell("lPackArt").attach(gridPackArt);
   
    function addEditPack(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 435,
            movable: true,
            modal: true,
            title: "Pack"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            padding: 5,
            rows: [
                { type: "input", name: "bkId", label: "Id", labelPosition: "left", labelWidth: 100, width: "200px", hidden: true },
                { type: "input", name: "bkCode", label: "Code", labelPosition: "left", labelWidth: 100, width: "340px" },
                { type: "input", name: "bkDesig", label: "Designation", labelPosition: "left", labelWidth: 100 },
                { type: "combo", name: "bkType", label: "Sub-family", labelPosition: "left", labelWidth: 100, width: "400px", readonly: true },
                { type: "combo", name: "BomKit", label: "BOM / KIT", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true, data: [ {value: "BOM", id: "b"}, {value: "KIT", id: "k"}] },
                { type: "combo", name: "bkUnit", label: "Unit", labelPosition: "left", labelWidth: 100, width: "300px", readonly: true },
                {
                  align: "end",
                  cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                  ]
                }
            ]
        });

        cbProdUnit = form.getItem("bkUnit").getWidget();
        cbProdUnit.data.load("commonQy.php?t=un").then(function(){ });

        cbProdType = form.getItem("bkType").getWidget();
        cbProdType.data.load("commonQy.php?t=fam&s=k").then(function(){
            dhx.ajax.get("impPacksQy.php?t=pack&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbProdType.setValue(cbProdType.data.getId(obj.sfamName));
                cbProdUnit = form.getItem("bkUnit").getWidget();
                cbProdUnit.data.load("commonQy.php?t=un").then(function(){
                    cbProdUnit.setValue(obj.bkUnitId);
                });
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Pack ",
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
                    header: "Packs ",
                    text: "Confirm pack creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("impPacksWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadPacks();
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

    function delete_line() {
        const config = {
            header: "Delete ", text: "Confirm line elimination?", buttons: ["no", "yes"], buttonsAlignment: "center"
        };     
        dhx.confirm(config).then(function(answer){
            if (answer) {
                dhx.ajax.get("impPacksWr.php?t=del&id="+wSelDet).then(function (data) {
                    loadPackDetails();
                }).catch(function (err) {
                        console.log(err);
                });
            };
        });         
    }
    function add_l_line() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Labour line"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Labour type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbType = form.getItem("estReference").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=l").then(function(){
            if ( wSelDet != 0 ) {
                dhx.ajax.get("impPacksQy.php?t=r&id="+wSelDet).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' ) {
                dhx.ajax.get("estimateQy.php?t=ref&l=l&v="+new_value).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    form.setValue(obj);
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Labour ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Labour ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("impPacksWr.php?t=d&l=l&e="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
//                            console.log(data);
                            loadPackDetails();
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

    function add_s_line() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Service line"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Servive type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbType = form.getItem("estReference").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=s").then(function(){
            if ( wSelDet != 0 ) {
                dhx.ajax.get("impPacksQy.php?t=r&id="+wSelDet).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' ) {
                dhx.ajax.get("estimateQy.php?t=ref&l=s&v="+new_value).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    form.setValue(obj);
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Service ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Service ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("impPacksWr.php?t=d&l=s&e="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadPackDetails();
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
    function add_e_line() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Equipment"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Equipment type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbType = form.getItem("estReference").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=e").then(function(){ 
            if ( wSelDet != 0 ) {
                dhx.ajax.get("impPacksQy.php?t=r&id="+wSelDet).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' ) {
                dhx.ajax.get("estimateQy.php?t=ref&l=e&v="+new_value).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    form.setValue(obj);
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Equipment ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Equipment ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("impPacksWr.php?t=d&l=e&e="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
//                            console.log(data);
                            loadPackDetails();
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
    function add_p_line() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Product"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Product type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "combo", name: "estCode", required: true, label: "Code", labelWidth: "100px", labelPosition: "left", readOnly: false},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });

        cbType = form.getItem("estReference").getWidget();
        cbCode = form.getItem("estCode").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=p").then(function(){ 
            if ( wSelDet != 0 ) {
                dhx.ajax.get("impPacksQy.php?t=p&id="+wSelDet).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                    cbCode.data.load("commonQy.php?t=prod&s="+obj.estReference).then(function(){
                        cbCode.setValue(obj.estCode);
                    });
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' ) {
                cbCode.data.parse({});
                cbCode.clear();
                cbCode.data.load("commonQy.php?t=prod&s="+new_value).then(function(){
                });
            }
            if ( name == 'estCode' ) {
                dhx.ajax.get("estimateQy.php?t=prod&v="+new_value).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    form.setValue(obj);
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Equipment ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Equipment ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("impPacksWr.php?t=p&l=p&e="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
//                            console.log(data);
                            loadPackDetails();
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
        dhx.ajax.get("menuQy.php?t=per&p=packs").then(function (data) {
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