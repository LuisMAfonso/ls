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
    var pGridCustomers, dsUsers, mainForm, dsContacts;
    var tbUsers, tbDetails, cbCountry, tabDet, cntGrid, cntLayout, tbContacts, cbCType, cbBAct;
    var wSelected = wSelCnt = 0;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbCompany", html: "", height: "55px" },
              	{ id: "companies", html: "" },
              ]
            },
            { type: "line",
              width: "620px",
              rows: [ 
                { id: "company", html: "",  },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    var f_company = {
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
                        { id: "custId", name: "custId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "custName", name: "custName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "custAddress", name: "custAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "custZipcode", name: "custZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "custCity", name: "custCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "custCode", name: "custCode", type: "input", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "custEmail", name: "custEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "e-mail cliente", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "custPhone", name: "custPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
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
                        { id: "custType", name: "custType", type: "input", label: "Type", labelWidth: "70px", width: "300px",  placeholder: "customer type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "custBusAct", name: "custBusAct", type: "input", label: "Buss.Activ.", labelWidth: "70px", width: "300px",  placeholder: "bussiness activity", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                }
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_company);
    dsUsers = new dhx.DataCollection();
    dsContacts = new dhx.DataCollection();
    loadUsers();

    function loadUsers() {
        dsUsers.removeAll();
        dsUsers.load("mntCustQy.php?t=companies").then(function(){
    //      console.log("done users read");
        });
    }

    tbUsers = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbUsers.data.load("toolbarsQy.php?t=a_c_r").then(function(){
        tbUsers.disable(['edit', 'delete']);
    });;
	tbUsers.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridCustomers.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbUsers.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditUser(); }
        if ( id == 'add' )  { wSelected = 0; addEditUser(); }
    });
	pLayout.getCell("lTbCompany").attach(tbUsers);

    pGridCustomers = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "custName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 190, id: "custCity", header: [{ text: "City" }], autoWidth: true },
            { width: 90, id: "custType", header: [{ text: "Type" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsUsers
    });
    pGridCustomers.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        tbUsers.enable(['edit', 'delete']);

        dhx.ajax.get("mntCustQy.php?t=customer&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
            loadContacts();
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("companies").attach(pGridCustomers);

    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "General Info"},
            { id: "contacts", tab: "Contacts"}
        ]
    });
    pLayout.getCell("company").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

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

        dhx.ajax.get("mntCustQy.php?t=contact&r="+row.Id).then(function (data) {
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
        if ( id == 'edit' ) { addEditContact(); }
        if ( id == 'add' )  { wSelCnt = 0; addEditContact(); }
    });

    mainLayout.getCell("workplace").attach(pLayout);
    tabDet.getCell("contacts").attach(cntLayout);
    cntLayout.getCell("lTbContact").attach(tbContacts);
    cntLayout.getCell("gContacts").attach(cntGrid);

    function loadContacts() {
        dsContacts.removeAll();
        dsContacts.load("mntCustQy.php?t=contacts&r="+wSelected).then(function(){
            console.log("done contacts read");
        });
    }

    function addEditUser(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 635,
            closable: true,
            movable: true,
            modal: true,
            title: "Customers"
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
                            { id: "custId", name: "custId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "custName", name: "custName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custAddress", name: "custAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "custZipcode", name: "custZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "custCity", name: "custCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custCountry", name: "custCountry", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custEmail", name: "custEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "e-mail cliente", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custPhone", name: "custPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
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
                            { id: "custTypeId", name: "custTypeId", type: "combo", label: "Type", labelWidth: "70px", width: "300px",  placeholder: "customer type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            }
                       ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custBusActId", name: "custBusActId", type: "combo", label: "Buss.Activ.", labelWidth: "70px", width: "300px",  placeholder: "bussiness activity", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
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
        cbCType = form.getItem("custTypeId").getWidget();
        cbCType.data.load("commonQy.php?t=ty").then(function(){
        });
        cbBAct = form.getItem("custBusActId").getWidget();
        cbBAct.data.load("commonQy.php?t=ba").then(function(){
        });

        cbCountry = form.getItem("custCountry").getWidget();
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("mntCustQy.php?t=customer&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbCountry.setValue(cbCountry.data.getId(obj.custCountry));
                cbCType.setValue(cbCType.data.getId(obj.custType));
                cbBAct.setValue(cbBAct.data.getId(obj.custBusAct));
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
                        const send = form.send("mntCustWr.php?t=f&r="+wSelected, "POST").then(function(data){
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

</script>