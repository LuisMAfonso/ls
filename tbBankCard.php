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
    var pGrid, dsBankCard;
    var tbGrid;
    var wSelected = 0;
    var cbStaff;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lyToolbar", html: "", height: "55px" },
              	{ id: "lyGrid", html: "" },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    dsBankCard = new dhx.DataCollection();
    loadPermission()
    
    function loadGrid() {
        dsBankCard.removeAll();
        dsBankCard.load("tbBankCardQy.php?t=grid").then(function(){
            if ( canCreate == 1 ) tbGrid.enable(['add']);
        });
    }
 
    tbGrid = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbGrid.data.load("toolbarsQy.php?t=m_aed").then(function(){
        tbGrid.disable(['edit', 'delete','add']);
    });;
    tbGrid.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditUser(); }
        if ( id == 'add' )  { wSelected = 0; addEditUser(); }
    });
	pLayout.getCell("lyToolbar").attach(tbGrid);

    pGrid = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 150, id: "cardNumber", header: [{ text: "Card Number" }], autoWidth: true, align: "left" },
            { width: 0, id: "bank", header: [{ text: "Bank" }], autoWidth: true, align: "left" },
            { width: 200, id: "bankAccount", header: [{ text: "Account" }], autoWidth: true, align: "left" },
            { width: 90, id: "cardLimit", header: [{ text: "C.Limit" }], autoWidth: true, align: "right" },
            { width: 250, id: "staffName", header: [{ text: "Assign to" }], autoWidth: true, align: "left" },
        ],
        selection:"row",
        adjust: "true",
        data: dsBankCard
    });
    pGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        if ( canModify == 1 ) tbGrid.enable(['edit']);
        if ( canDelete == 1 ) tbGrid.enable(['delete']);

    });

    pLayout.getCell("lyGrid").attach(pGrid);

    function addEditUser(argument) {
        const dhxWindow = new dhx.Window({
            width: 600,
            height: 460,
            closable: true,
            movable: true,
            modal: true,
            title: "Card information"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "cardNumber", label: "Card number", labelPosition: "left", labelWidth: "80px", width: "350px", required: true },
                { type: "input", name: "bank", label: "Bank", labelPosition: "left", labelWidth: "80px", },
                { type: "input", name: "bankAccount", label: "Account", labelPosition: "left", labelWidth: "80px", },
                { type: "input", name: "cardLimit", label: "Card limit", labelPosition: "left", labelWidth: "80px", width: "250px" },
                { type: "combo", name: "staffId", id: "assignto", label: "Assign to", labelWidth: "80px", width: "500px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
                 {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                }
            ]
        });
        cbStaff = form.getItem("staffId").getWidget();
        cbStaff.data.load("commonQy.php?t=rt").then(function(){
            if ( wSelected != 0 ) {
                dhx.ajax.get("tbBankCardQy.php?t=r&id="+wSelected).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbStaff.setValue(obj.staffId);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Card information ",
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
                    header: "Card information ",
                    text: "Confirm card creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("tbBankCardWr.php?t=f", "POST").then(function(data){
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

        dhx.ajax.get("tbBankCardQy.php?t=r&id="+wSelected).then(function (data) {
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
    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=tbBankCard").then(function (data) {
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