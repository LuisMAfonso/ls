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
    var pGridFamilies, dsFamilies, pGridsubFam, dssubFam;
    var tbFamilies, tbSubfam;
    var wSelected = 0;
    var wSubSel = 0;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbFamilies", html: "", height: "55px" },
              	{ id: "Families", html: "" },
              ]
            },
            { type: "line",
              rows: [ 
                { id: "lTbSubfam", html: "", height: "55px" },
                { id: "Subfam", html: "" },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    loadPermission();

    dsFamilies = new dhx.DataCollection();
    loadFamilies();

    function loadFamilies() {
        dsFamilies.removeAll();
        dsFamilies.load("tbFamilyQy.php?t=families").then(function(){
    //      console.log("done Families read");
        });
    }

    tbFamilies = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbFamilies.data.load("toolbarsQy.php?t=m_aed").then(function(){
        tbFamilies.disable(['edit', 'delete','add']);
    });;
    tbFamilies.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditFamily(); }
        if ( id == 'add' )  { wSelected = 0; addEditFamily(); }
    });
	pLayout.getCell("lTbFamilies").attach(tbFamilies);

    tbSubfam = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbSubfam.data.load("toolbarsQy.php?t=m_aed").then(function(){
        tbSubfam.disable(['edit', 'delete','add']);
    });;
    tbSubfam.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditSubFamily(); }
        if ( id == 'add' )  { wSubSel = 0; addEditSubFamily(); }
        if ( id == 'delete' ) {  
            wMessage = {
                header: "Delete ", text: "Confirm Sub-family deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
            dhx.confirm(wMessage).then(function(answer){
                if (answer) {
                    console.log(answer);
                    dhx.ajax.get("tbFamilyWr.php?t=del&r="+wSubSel).then(function (data) {
                        wSubSel = 0;
                        tbSubfam.disable(['edit', 'delete','add']);
                        loadSubFamily();
                }).catch(function (err) {
                            console.log(err);
                    });
                }
            });         
        }
    });
    pLayout.getCell("lTbSubfam").attach(tbSubfam);

    dssubFam = new dhx.DataCollection();
	
    pGridFamilies = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, id: "family", header: [{ text: "Family" }], autoWidth: true, align: "left" },
            { width: 60, id: "icon", header: [{ text: "Icon" }], align: "center", htmlEnable: true  },
        ],
        selection:"row",
        adjust: "true",
        data: dsFamilies
    });
    pGridFamilies.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
//        tbFamilies.enable(['edit', 'delete']);
        if ( canCreate == 1 ) tbSubfam.enable(['add']);
        loadSubFamily();
    });

    function loadSubFamily() {
        dssubFam.removeAll();
        dssubFam.load("tbFamilyQy.php?t=subFam&u="+wSelected).then(function(){
//          console.log("done subFam read");
        });
    }

    pGridsubFam = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, id: "subfam", header: [{ text: "Sub-family" }], autoWidth: true, align: "left" },
            { width: 60, id: "icon", header: [{ text: "Icon" }], align: "center", htmlEnable: true  },
        ],
        selection:"row",
        adjust: "true",
        data: dssubFam
    });
    pGridsubFam.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSubSel = row.Id;
        if ( canModify == 1 ) tbSubfam.enable(['edit']);
        if ( canDelete == 1 ) tbSubfam.enable(['delete']);

    });

    pLayout.getCell("Families").attach(pGridFamilies);
    pLayout.getCell("Subfam").attach(pGridsubFam);

    function addEditFamily() {

    }

    function addEditSubFamily(argument) {
        const dhxWindow = new dhx.Window({
            width: 540,
            height: 260,
            closable: true,
            movable: true,
            modal: true,
            title: "Sub-family"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "sfamName", required: true, label: "Sub-family", labelPosition: "left", labelWidth: "80px", },
                { type: "input", name: "sfamIcon", required: true, label: "Icon", labelPosition: "left", labelWidth: "80px", },
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
                    header: "Sub-family ",
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
                    header: "Sub-family ",
                    text: "Confirm Sub-family creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("tbFamilyWr.php?t=f&f="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadSubFamily();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhx.ajax.get("tbFamilyQy.php?t=r&id="+wSubSel).then(function (data) {
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
        dhx.ajax.get("menuQy.php?t=per&p=tbFamily").then(function (data) {
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