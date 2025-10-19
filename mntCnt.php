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
    var pGridContacts, dsContacts, mainForm, dsContacts;
    var tbContacts, tbDetails, cbCountry, tabDet, cntGrid, cntLayout, tbContacts;
    var wSelected = wSelCnt = 0;

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbContact", html: "", height: "55px" },
              	{ id: "contacts", html: "" },
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

    var f_contact = {
        css: "bg-promo",
        align: "start",
        width: "615px",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "contomer info",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "contId", name: "contId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "contact id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "contName", name: "contName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "contact name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "contAddress", name: "contAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "contZipcode", name: "contZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "contCity", name: "contCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "contCode", name: "contCode", type: "input", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "contEmail", name: "contEmail", type: "input", label: "E-Mail", labelWidth: "70px", width: "450px",  placeholder: "contact email", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "contPhone", name: "contPhone", type: "input", label: "Phone #", labelWidth: "70px", width: "300px",  placeholder: "contact phone number", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                }
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_contact);
    dsContacts = new dhx.DataCollection();
    loadContacts();

    function loadContacts() {
        dsContacts.removeAll();
        dsContacts.load("mntCntQy.php?t=contacts").then(function(){
    //      console.log("done users read");
        });
    }

    tbContacts = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbContacts.data.load("toolbarsQy.php?t=a_c_r").then(function(){
        tbContacts.disable(['add','edit', 'delete']);
        if ( canCreate == 1 ) tbContacts.enable(['add']);
    });;
	tbContacts.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridContacts.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbContacts.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditContact(); }
        if ( id == 'add' )  { wSelected = 0; addEditContact(); }
        if ( id == 'delete' ) { deleteContact(); }
    });
	pLayout.getCell("lTbContact").attach(tbContacts);

    pGridContacts = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "contName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 190, id: "contCity", header: [{ text: "City" }], autoWidth: true },
            { width: 80, id: "contOrigin", header: [{ text: "Origin" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsContacts
    });
    pGridContacts.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        if ( canModify == 1 ) tbContacts.enable(['edit']);
        if ( canDelete == 1 ) tbContacts.enable(['delete']);

        dhx.ajax.get("mntCntQy.php?t=contact&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("contacts").attach(pGridContacts);

    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "General Info"},
        ]
    });
    pLayout.getCell("company").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

    mainLayout.getCell("workplace").attach(pLayout);

    function deleteContact() {
        wMessage = {
            header: "Delete ", text: "Confirm Contact deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
        };   
        dhx.confirm(wMessage).then(function(answer){
            if (answer) {
                dhx.ajax.get("mntCntWr.php?t=d&r="+wSelected).then(function (data) {
                    loadContacts();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });         
    }
    function addEditContact(argument) {
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
            dhx.ajax.get("mntCntQy.php?t=contact&r="+wSelected).then(function (data) {
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
                        const send = form.send("mntCntWr.php?t=f&r="+wSelected, "POST").then(function(data){
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

    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=mntCnt").then(function (data) {
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