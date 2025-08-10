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
    var pGridSuppliers, dsSuppliers, mainForm, dsContacts;
    var tbSuppliers, tbDetails, cbCountry, tabDet, cntGrid, cntLayout, tbContacts, cbSType, cbBAct;
    var supLayout, supGrid, tbSupply, dsSupply, cbFam, cbSFam;
    var wSelected = wSelCnt = 0;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbsupplier", html: "", height: "55px" },
              	{ id: "suppliers", html: "" },
              ]
            },
            { type: "line",
              width: "620px",
              rows: [ 
                { id: "supplier", html: "",  },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    var f_supplier = {
        css: "bg-promo",
        align: "start",
        width: "615px",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Supplier info",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "suppId", name: "suppId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "supplier name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "suppName", name: "suppName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "supplier name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppAddress", name: "suppAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "suppZipcode", name: "suppZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "suppCity", name: "suppCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppCode", name: "suppCode", type: "input", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppEmail", name: "suppEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "e-mail cliente", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppPhone", name: "suppPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "vatNumber", name: "vatNumber", type: "input", label: "VAT number", labelWidth: "70px", width: "300px",  placeholder: "vat number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppType", name: "suppType", type: "input", label: "Type", labelWidth: "70px", width: "300px",  placeholder: "supplier type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "suppBusAct", name: "suppBusAct", type: "input", label: "Buss.Activ.", labelWidth: "70px", width: "300px",  placeholder: "bussiness activity", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                }
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_supplier);
    dsSuppliers = new dhx.DataCollection();
    dsContacts = new dhx.DataCollection();
    dsSupply = new dhx.DataCollection();
    loadSuppliers();

    function loadSuppliers() {
        dsSuppliers.removeAll();
        dsSuppliers.load("mntSuppQy.php?t=suppliers").then(function(){
          console.log(dsSuppliers.getLength());
        });
    }

    tbSuppliers = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbSuppliers.data.load("toolbarsQy.php?t=a_c_r").then(function(){
        tbSuppliers.disable(['edit', 'delete']);
    });;
	tbSuppliers.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridSuppliers.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbSuppliers.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditSuppliers(); }
        if ( id == 'add' )  { wSelected = 0; addEditSuppliers(); }
    });
	pLayout.getCell("lTbsupplier").attach(tbSuppliers);

    pGridSuppliers = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "suppName", header: [{ text: "Name" }], autoWidth: true, align: "left"  },
            { width: 190, id: "suppCity", header: [{ text: "City" }], autoWidth: true },
            { width: 90, id: "suppType", header: [{ text: "Type" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsSuppliers
    });
    pGridSuppliers.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        tbSuppliers.enable(['edit', 'delete']);

        dhx.ajax.get("mntSuppQy.php?t=supplier&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
            loadContacts();
            loadSupply();
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("suppliers").attach(pGridSuppliers);

    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "General Info"},
            { id: "contacts", tab: "Contacts"},
            { id: "supply", tab: "Supply"}
        ]
    });
    pLayout.getCell("supplier").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

// contacs
    cntGrid = new dhx.Grid(null, {
        columns: [
            { width: 0, minWidth: 100, id: "contName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 120, id: "contRole", header: [{ text: "Role" }], autoWidth: true },
            { width: 190, id: "contEmail", header: [{ text: "Email" }], autoWidth: true },
            { width: 100, id: "contPhone", header: [{ text: "Phone" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsContacts
    });
    cntGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelCnt = row.Id;
        tbContacts.enable(['edit', 'delete']);

        dhx.ajax.get("mntSuppQy.php?t=contact&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
        }).catch(function (err) {
            console.log(err);
        });
    });

    cntLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbContact", html: "", height: "55px" },
                { id: "gContacts", html: "" },
              ]
            },
        ]
    });

    tbContacts = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbContacts.data.load("toolbarsQy.php?t=a_aed").then(function(){
        tbContacts.disable(['edit', 'delete']);
    });;
    tbContacts.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { searchContact(); }
        if ( id == 'add' )  { wSelCnt = 0; searchContact(); }
    });

// supply

    supGrid = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, id: "supFam", header: [{ text: "Family" }], autoWidth: true, align: "left" },
            { width: 50, id: "ficon", header: [{ text: "I." }], align: "center", htmlEnable: true  },
            { width: 0, id: "supSFam", header: [{ text: "Sub-Family" }], autoWidth: true, align: "left" },
            { width: 50, id: "sficon", header: [{ text: "I." }], align: "center", htmlEnable: true  },
        ],
        selection:"row",
        adjust: "true", 
        data: dsSupply
    });
    supGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelSup = row.Id;
        tbSupply.enable(['edit', 'delete']);

        dhx.ajax.get("mntSuppQy.php?t=contact&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
        }).catch(function (err) {
            console.log(err);
        });
    });

    supLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbSupply", html: "", height: "55px" },
                { id: "gSupply", html: "" },
              ]
            },
        ]
    });

    tbSupply = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbSupply.data.load("toolbarsQy.php?t=b_aed").then(function(){
        tbSupply.disable(['edit', 'delete']);
    });;
    tbSupply.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditSupply(); }
        if ( id == 'add' )  { wSelSup = 0; addEditSupply(); }
    });


    mainLayout.getCell("workplace").attach(pLayout);
    tabDet.getCell("contacts").attach(cntLayout);
    tabDet.getCell("supply").attach(supLayout);
    cntLayout.getCell("lTbContact").attach(tbContacts);
    cntLayout.getCell("gContacts").attach(cntGrid);
    supLayout.getCell("lTbSupply").attach(tbSupply);
    supLayout.getCell("gSupply").attach(supGrid);

    function loadContacts() {
        dsContacts.removeAll();
        dsContacts.load("mntSuppQy.php?t=contacts&r="+wSelected).then(function(){
//            console.log("done contacts read");
        });
    }
    function loadSupply() {
        dsSupply.removeAll();
        dsSupply.load("mntSuppQy.php?t=supply&r="+wSelected).then(function(){
//            console.log("done contacts read");
        });
    }

    function addEditSupply() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 260,
            closable: true,
            movable: true,
            modal: true,
            title: "Supply"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "famId", required: true, label: "Family", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "combo", name: "sfamId", required: true, label: "Sub-family", labelWidth: "100px", labelPosition: "left", readOnly: true },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbFam = form.getItem("famId").getWidget();
        cbSFam = form.getItem("sfamId").getWidget();
        cbFam.data.load("commonQy.php?t=family").then(function(){
            if ( wSelSup != 0 ) {
                dhx.ajax.get("mntSuppQy.php?t=r&id="+wSelSup).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbFam.setValue(obj.famId);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'famId' ) {
                cbSFam.data.parse({});
                cbSFam.clear();
                cbSFam.data.load("commonQy.php?t=sfam&s="+new_value).then(function(){
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Supply ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Supply ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntSuppWr.php?t=sup&r="+wSelSup+"&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadSupply();
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

    function addEditSuppliers(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 635,
            closable: true,
            movable: true,
            modal: true,
            title: "Suppliers"
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
                            { id: "suppId", name: "suppId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "suppName", name: "suppName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "supplier name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppAddress", name: "suppAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "suppZipcode", name: "suppZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "suppCity", name: "suppCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppCountry", name: "suppCountry", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppEmail", name: "suppEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "e-mail cliente", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppPhone", name: "suppPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            }
                       ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "vatNumber", name: "vatNumber", type: "input", label: "VAT number", labelWidth: "70px", width: "300px",  placeholder: "vat number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            }
                       ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppTypeId", name: "suppTypeId", type: "combo", label: "Type", labelWidth: "70px", width: "300px",  placeholder: "supplier type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            }
                       ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "suppBusActId", name: "suppBusActId", type: "combo", label: "Buss.Activ.", labelWidth: "70px", width: "300px",  placeholder: "bussiness activity", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
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
        cbSType = form.getItem("suppTypeId").getWidget();
        cbSType.data.load("commonQy.php?t=sty").then(function(){
        });
        cbBAct = form.getItem("suppBusActId").getWidget();
        cbBAct.data.load("commonQy.php?t=ba").then(function(){
        });

        cbCountry = form.getItem("suppCountry").getWidget();
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("mntSuppQy.php?t=supplier&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbCountry.setValue(cbCountry.data.getId(obj.suppCountry));
                cbSType.setValue(cbSType.data.getId(obj.suppType));
                cbBAct.setValue(cbBAct.data.getId(obj.suppBusAct));
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Supplier ",
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
                    text: "Confirm supplier creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntSuppWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadSuppliers();
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

    function searchContact() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 260,
            closable: true,
            movable: true,
            modal: true,
            title: "Contacts"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "Id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                {
                    align: "between",
                    cols: [
                        { type: "combo", name: "contId", required: true, label: "Contact", width: "490px", labelWidth: "100px", labelPosition: "left"},
                        { type: "button", name: "search", view: "flat", icon: "mdi mdi-account-search-outline", text: "", submit: true, },
                    ]
                },
                { type: "combo", name: "roleId", required: true, label: "Role", labelWidth: "100px", labelPosition: "left", readOnly: true },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbCNT = form.getItem("contId").getWidget();
        cbRole = form.getItem("roleId").getWidget();
        cbRole.data.load("commonQy.php?t=roles").then(function(){
        });
        cbCNT.data.load("commonQy.php?t=cnts").then(function(){
            dhx.ajax.get("mntCntQy.php?t=cnt&id="+wSelCnt).then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbCNT.setValue(obj.contId);
                cbRole.setValue(obj.roleId);
            }).catch(function (err) {
                    console.log(err);
            });
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'search' ) {
                alert('Open Contacts');
            }
            if ( name == 'cancel' ) {
                const config = {
                    header: "Supply ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
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
                    header: "Supply ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntCntWr.php?t=cnt&s="+wSelected+"&o=su", "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadContacts();
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

</script>