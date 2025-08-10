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
    var pGrid, dsProdUnit;
    var tbGrid;
    var wSelected = 0;

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

    dsProdUnit = new dhx.DataCollection();
    loadPermission()
    
    function loadGrid() {
        dsProdUnit.removeAll();
        dsProdUnit.load("tbProdUnitQy.php?t=grid").then(function(){
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
            { width: 80, id: "code", header: [{ text: "Code" }], align: "left", htmlEnable: true  },
            { width: 0, id: "descript", header: [{ text: "Description" }], autoWidth: true, align: "left" },
        ],
        selection:"row",
        adjust: "true",
        data: dsProdUnit
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
                        const send = form.send("tbProdUnitWr.php?t=f", "POST").then(function(data){
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

        dhx.ajax.get("tbProdUnitQy.php?t=r&id="+wSelected).then(function (data) {
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
        dhx.ajax.get("menuQy.php?t=per&p=tbProdUnit").then(function (data) {
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