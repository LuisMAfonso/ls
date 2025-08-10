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
    var pGridUsers, dsUsers, pGridGroups, dsGroups;
    var tbUsers;
    var wSelected = 0;
    var cbStaff;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbUsers", html: "", height: "55px" },
              	{ id: "users", html: "" },
              ]
            },
            { id: "groups", html: "", width: "300px" },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    dsUsers = new dhx.DataCollection();
    loadUsers();

    function loadUsers() {
        dsUsers.removeAll();
        dsUsers.load("userMntQy.php?t=users").then(function(){
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
         if (!arguments) {
                pGridUsers.data.filter();
            } else {
                pGridUsers.data.filter({
                    by: "UserName",
                    match: arguments,
                    compare: function (arguments, match) { return new RegExp(match, "i").test(arguments) }
                });
            }  	
    });
    tbUsers.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditUser(); }
        if ( id == 'add' )  { wSelected = 0; addEditUser(); }
    });
	pLayout.getCell("lTbUsers").attach(tbUsers);

    dsGroups = new dhx.DataCollection();
	
    pGridUsers = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 100, id: "UserId", header: [{ text: "User" }], autoWidth: true },
            { width: 0, id: "UserName", header: [{ text: "User name" }], autoWidth: true, align: "left" },
            { width: 190, id: "Email", header: [{ text: "Email" }], autoWidth: true },
            { width: 100, id: "DateOpen", header: [{ text: "Start date" }], autoWidth: true },
            { width: 100, id: "DateClose", header: [{ text: "Close date" }], autoWidth: true },
            { width: 60, id: "IsAdmin", header: [{ text: "Admin" }], align: "center",
            htmlEnable: true,
            template: function (text, row, col) {
                return "<img src='images/chk" + (text ? 1 : 0) + ".gif'></div>";
                }
            },
            { width: 60, id: "avatar", header: [{ text: "Avatar" }], align: "center",
            htmlEnable: true,
            template: function (text, row, col) {
                return "<img style='border-radius: 50%;' src='images/imgUsers/"+text+"'>";
                }
            },
        ],
        selection:"row",
        adjust: "true",
        data: dsUsers
    });
    pGridUsers.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        tbUsers.enable(['edit', 'delete']);

        dsGroups.removeAll();
	    dsGroups.load("userMntQy.php?t=groups&u="+row.UserId).then(function(){
	    	console.log("done groups read");
	    });
    });

    pGridGroups = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, id: "Group", header: [{ text: "Group" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true",
        data: dsGroups
    });

    pLayout.getCell("users").attach(pGridUsers);
    pLayout.getCell("groups").attach(pGridGroups);

    function addEditUser(argument) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Users"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                {
                    type: "fieldset",
                    name: "personal",
                    label: "Personal info",
                    rows: [
                        { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                        { type: "input", name: "name", required: true, label: "Name", labelPosition: "left", labelWidth: "80px", },
                        { 
                            align: "between",
                            cols: [
                                {
                                    rows: [
                                    { type: "datepicker", name: "birthday", label: "Birthdate", labelPosition: "left", labelWidth: "80px", dateFormat: "%Y-%m-%d", width: "220px", },
                                    { type: "combo", name: "staffId", label: "Staff", labelPosition: "left", labelWidth: "80px", width: "360px", }
                                    ],
                                },
                                { type: "spacer", width: "20px", },
                                { type: "avatar", name: "avatar", label: "Photo", icon: "dxi dxi-person", fieldName: "file",
                                    alt: "Employee photo", labelPosition: "left", target: "uploader.php", value: ""
                                },
                            ]
                        },
                       ]
                },
                {
                    type: "fieldset",
                    name: "account",
                    label: "Account info",
                    rows: [
                        { type: "input", name: "userId", required: true, label: "UserId", labelWidth: "80px", labelPosition: "left", },
                        { type: "input", name: "email", required: true, label: "Email", labelWidth: "80px", labelPosition: "left", validation: "email", },
                        { type: "input", inputType: "password", name: "password", required: true,
                            label: "Password", labelWidth: "80px", labelPosition: "left" },
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
        cbStaff   = form.getItem("staffId").getWidget();
        form.getItem("avatar").events.on("afterShow", value => {
            console.log("afterShow", value);
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ",
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
                    text: "Confirm user creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("userMntWr.php?t=f", "POST").then(function(data){
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

        if ( wSelected != 0 ) {
            dhx.ajax.get("userMntQy.php?t=r&id="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                if ( obj.id > 0 ) { 
                    form.setProperties("password", { required: false,  });
                    form.getItem("password").disable();
                    form.setProperties("userId", { required: false,  });
                    form.getItem("userId").disable();
                    cbStaff.data.load("commonQy.php?t=rt").then(function(){
                      cbStaff.setValue(obj.staffId);
                    });
                }
//                var obj = form.getValue();
//                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        } else {
            cbStaff.data.load("commonQy.php?t=rt").then(function(){   });
        }
        dhxWindow.attach(form);
        dhxWindow.show();

    }

</script>