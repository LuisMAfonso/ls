<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>
<div id="layout" style="height: 100vh;"></div>
<style>
    .row_header .dhx_grid-cell {
      font-weight: bold; 
    }
    .imp_error_hard {
        background: #f5b7b1;
    }
    .imp_error_soft {
        background: #fad7a0;
    }
</style>
<?php
require_once('sidebar.php');
?>
<script>
    var pLayout;
    var pGridEstimateDet, dsEstimateDet, dvEtimateItems, cbType, gridTotals;
    var gridEstimations, dsEstimations, tbEstimations, cbProject, cbCode, cbSize, cbUnit;
    var tbEstimateDet, dsTotals, impGrid, dsImp;
    var wSelected = 0;
    var wLineType = '';
    var estimationId = 0;
    var groupLine = 0;
    var isGrouped = 0;
    var obj = '';
    var wMessage = '';
    var projName = '';
    var wMargin = 0;
    var impName = '';

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line", id: "lyEstimation", collapsable: true,
              rows: [ 
                { id: "lTbEstimations", html: "", height: "55px" },
                { id: "estimations", html: "" },
              ]
            },
            { type: "line", id: "estimate",
              rows: [ 
              	{ id: "lTbEstimate", html: "", height: "55px" },
              	{ id: "estimateDet", html: "" },
              ]
            },
            { type: "wide",
              width: "300px",
              rows: [ 
                { id: "estimateItems", html: "" },
                { id: "estimateTotal", html: "", height: "244px" },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);
    pLayout.getCell("estimate").hide();
    pLayout.events.on("afterExpand", function(id){
        if ( id == 'lyEstimation' ) {
            pLayout.getCell("estimate").hide();
            pLayout.getCell("lyEstimation").config.header=' ';
            pLayout.paint();
        }
    });

    dsEstimations = new dhx.DataCollection();
    dsEstimateDet = new dhx.DataCollection();
    dsTotals = new dhx.DataCollection();

    loadEstimations();

    function loadEstimations() {
        dsEstimations.removeAll();
        dsEstimations.load("estimateQy.php?t=estimations").then(function(){
        });
    }

    function loadEstimateDet() {
    
        pLayout.getCell("lyEstimation").collapse();            
        pLayout.getCell("lyEstimation").config.header=projName;
        pLayout.getCell("estimate").show();
        pLayout.paint();

        setMargin = JSON.parse('{ "value": "'+wMargin+'%" }');
        tbEstimateDet.data.update("margin",setMargin);

        dsEstimateDet.removeAll();
        dsEstimateDet.load("estimateQy.php?t=estimate&eId="+estimationId+"&g="+isGrouped).then(function(){
    //      console.log("done estimateDet read");
            pGridEstimateDet.data.forEach(function(item, index, array) {
//                console.log("This is an item of dataCollection: ", item);
                if (item.lineType == 'n') {
                    wSpan = '{ "row": "'+item.id+'", "column": "estDesign", "colspan": "6" } ';
                    wSpan = JSON.parse(wSpan);
                    console.log(wSpan);
                    pGridEstimateDet.addSpan(wSpan);
                }
            });
        });
        dsTotals.load("estimateQy.php?t=totals&eId="+estimationId).then(function(){    });
    }

    tbEstimateDet = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbEstimateDet.data.load("toolbarsQy.php?t=m_ed").then(function(){
        tbEstimateDet.disable(['edit', 'delete', 'note','import']);
    });;
    tbEstimateDet.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { 
            if ( wLineType == 'h' ) add_h_line(0); 
            if ( wLineType == 'l' ) add_l_line(0); 
            if ( wLineType == 's' ) add_s_line(0); 
            if ( wLineType == 'e' ) add_e_line(0); 
            if ( wLineType == 'p' ) add_p_line(0); 
            if ( wLineType == 'c' ) add_c_line(0);
        }
        if ( id == 'note' )    { add_edit_note(); }
        if ( id == 'import' )  { import_file(); }
        if ( id == 'delete' )  { delete_line(); }
        if ( id == 'print' )   { 
            window.open("estimatePDF.php?g="+isGrouped+"&eId="+estimationId,'_blank');
        }
        if ( id == 'group' ) { 
            isGrouped = 0; 
            if ( tbEstimateDet.getState("group") ) isGrouped = 1; 
            loadEstimateDet();
        }
        if ( id == 'imargin' && canModify == true ) {
            change_margin();
        }
    });
	pLayout.getCell("lTbEstimate").attach(tbEstimateDet);


    gridTotals = new dhx.Grid(null, {
        columns: [
            { width: 0, id: "id", header: [{ text: "" }], autoWidth: true, align: "left" },
            { width: 100, id: "value", header: [{ text: "Amount" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 100, id: "valueM", header: [{ text: "w/ Margin" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
        ],
        rowCss: function(row) { return row.lineBold == 1 ? "row_header" : "" },
        selection: false,
        adjust: "true",
        data: dsTotals
    });

// gris estimations
    tbEstimations = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbEstimations.data.load("toolbarsQy.php?t=e_aed").then(function(){
        tbEstimations.disable(['edit', 'delete','view']);
    });;
    tbEstimations.events.on("input", function (event, arguments) {
        console.log(arguments);
        const value = arguments.toString().toLowerCase();
        gridEstimations.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbEstimations.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'view' ) { loadEstimateDet(); }
        if ( id == 'edit' ) { addEstimation(); }
        if ( id == 'add' )  { estimationId = 0; addEstimation(); }
        if ( id == 'delete' ) {  
            wMessage = {
                header: "Delete ", text: "Confirm estimation deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
            dhx.confirm(wMessage).then(function(answer){
                if (answer) {
                    console.log(answer);
                    dhx.ajax.get("estimateWr.php?t=del&r="+estimationId).then(function (data) {
                        estimationId = 0;
                        tbEstimations.disable(['edit', 'delete','view']);
                        loadEstimations();
                    }).catch(function (err) {
                            console.log(err);
                    });
                }
            });         
        }
    });
    pLayout.getCell("lTbEstimations").attach(tbEstimations);

    gridEstimations = new dhx.Grid(null, {
        columns: [
            { width: 30, id: "estId", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 0, minWidth: 150, id: "estName", header: [{ text: "Estimate name" }], autoWidth: true, align: "left" },
            { width: 90, id: "estStatus", header: [{ text: "Est.Status" }], autoWidth: true },
            { width: 90, id: "estDate", header: [{ text: "Est.Opened" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "projName", header: [{ text: "Project name" }], autoWidth: true, align: "left" },
            { width: 90, id: "projStatus", header: [{ text: "Proj.Status" }], autoWidth: true },
            { width: 120, id: "projCity", header: [{ text: "City" }], autoWidth: true },
            { width: 90, id: "margin", header: [{ text: "Margin" }], autoWidth: true, align: "right", type: "number", format: "# ###", },
        ],
        selection: "row",
        adjust: "true",
        data: dsEstimations
    });
    gridEstimations.events.on("cellClick", function(row,column){
        console.log(row.estId+" - "+column.id);

        estimationId = row.estId;
        projName = row.projName;
        wMargin = row.margin;
        tbEstimations.enable(['edit', 'delete','view']);

    });
    gridEstimations.events.on("cellDblClick", function(row,column,e){
        loadEstimateDet();
    });

    pLayout.getCell("estimations").attach(gridEstimations);

// grid estimation details  

    function noteTemplate(value, row, col) {
        return `${row.notes}</br><br>
                <p style="color: yellow;">Customer: ${row.notesCust}</p>`;
    }
  
    pGridEstimateDet = new dhx.Grid(null, {
        columns: [
            { width: 30, id: "estimateDetId", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 30, id: "isPack", header: [{ text: "isPack" }], autoWidth: true, hidden: true },
            { width: 30, id: "groupLine", header: [{ text: "gl" }], autoWidth: true, hidden: true },
            { width: 30, id: "lineType", header: [{ text: "lt" }], autoWidth: true, hidden: true },
            { width: 40, id: "icon", header: [{ text: "I." }], align: "center", htmlEnable: true  },
            { width: 70, id: "estReference", header: [{ text: "Ref." }], autoWidth: true },
            { width: 0, id: "estDesign", header: [{ text: "Designation" }], autoWidth: true, align: "left", minWidth: 150, htmlEnable: true },
            { width: 50, id: "estQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right" },
            { width: 45, id: "unitLine", header: [{ text: "Un." }], autoWidth: true, align: "left" },
            { width: 90, id: "estItemValueL", header: [{ text: "Labour" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 90, id: "estItemValueP", header: [{ text: "Parts" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 90, id: "estLineValue", header: [{ text: "S.Total" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," } },
            { width: 95, id: "estLineTotal", header: [{ text: "Total" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," } },
            { width: 70, id: "prodSize", header: [{ text: "Size" }], autoWidth: true },
            { width: 40, id: "iNote", header: [{ text: "N." }], align: "center", htmlEnable: true, tooltip: true, tooltipTemplate: noteTemplate  },
            { width: 40, id: "costOnly", header: [{ text: "C." }], align: "center", htmlEnable: true  },
        ],
        rowCss: function(row) { return row.lineBold == 1 ? "row_header" : "" },
        selection: "row",
        adjust: "true",
//        dragMode: "target",
        dragItem: "row",
        tooltip: false,
        data: dsEstimateDet
    });
    pGridEstimateDet.events.on("cellClick", function(row,column){
        console.log(row.estimateDetId+" - "+column.id);
        wSelected = row.estimateDetId;
        wLineType = row.lineType;
        console.log(wSelected+" - "+wLineType);

        tbEstimateDet.disable(['import']);
        if ( canModify ) {
            tbEstimateDet.enable(['edit']);
            tbEstimateDet.enable(['note']);
            if ( wLineType == 'h' ) tbEstimateDet.enable(['import']);
            impName = 'ip_'+estimationId+"_"+wSelected;
        }
        if ( canDelete ) tbEstimateDet.enable(['delete']);
    });
    pGridEstimateDet.events.on("cellDblClick", function(row,column){
        console.log(row.estimateDetId+" - "+column.id);
        wSelected = row.estimateDetId;
        wLineType = row.lineType;

        if ( canModify && row.estimateDetId != 0 ) {
            if ( wLineType == 'l' ) add_l_line(0); 
            if ( wLineType == 's' ) add_s_line(0); 
            if ( wLineType == 'e' ) add_e_line(0); 
            if ( wLineType == 'p' ) add_p_line(0); 
            if ( wLineType == 'c' ) add_c_line(0); 
            if ( wLineType == 't' ) add_t_line(0); 
        };
    });
    pGridEstimateDet.events.on("afterRowDrop", function(data, events) {
        gLine = 0;
        console.log(data.start+" - "+data.source+" - "+data.target+" - "+gLine);
        if ( data.start == 'h' ) add_h_line();
        if ( data.start == 'l' ) add_l_line(data.target);
        if ( data.start == 's' ) add_s_line(data.target);
        if ( data.start == 'e' ) add_e_line(data.target);
        if ( data.start == 'p' ) add_p_line(data.target);
        if ( data.start == 'c' ) add_c_line(data.target);
        if ( data.start == 'k' ) add_k_line(data.target);
        if ( data.start == 'n' ) add_n_line(data.target);
        if ( data.start == 't' ) add_t_line(data.target);
        if ( !isNaN(data.start) ) {
            item = pGridEstimateDet.data.getItem(data.start);
            itemDest = pGridEstimateDet.data.getItem(data.target);
            console.log(data.start+" -> "+data.target+" : "+itemDest.lineType);
            dhx.ajax.get("estimateWr.php?t=mvline&s="+data.start+"&gl="+data.target).then(function (data) {
                console.log(data);
                loadEstimateDet();
            });
        }
    });
    pGridEstimateDet.events.on("beforeRowDrop", function(data, events){
        console.log(data.start+"  - "+data.source+" - "+data.target);
        if ( !isNaN(data.start) ) {
            item = pGridEstimateDet.data.getItem(data.start);
            itemDest = pGridEstimateDet.data.getItem(data.target);
            console.log(data.start+":"+item.lineType+" - "+data.source+" - "+data.target+" - "+itemDest.isPack);
    //        console.log(item.isPack);
            if ( item.lineType == 'k' && itemDest.isPack > 0 ) return false;
            if ( item.lineType == 'k' && itemDest.lineType == 'k' ) return false;
    //        if ( data.start == 'c' ) return false;
            return true;
        } else {
            itemDest = pGridEstimateDet.data.getItem(data.target);
            console.log(data.start+" - "+data.source+" - "+data.target);
            if ( data.start == 'k' ) {
            console.log(data.start+" - "+data.source+" - "+data.target+" - "+itemDest.isPack);
                if ( itemDest.isPack > 0 ) return false;
                if ( itemDest.lineType == 'k' ) return false;
            }
            return true;
        }
    });

    dvEtimateItems = new dhx.DataView(null, {
        itemsInRow: 2,
        gap: 20,
        template: dvEtimateTamplate,
        dragMode: "source",
        dragCopy: true,
    });
    dvEtimateItems.data.load("estimateQy.php?t=estimateItems");
    function dvEtimateTamplate(item) {
        let template = "<div class='dvItems_container'>";
        template += "<div class='dvItems_icon'>" + item.icon + "</div>";
        template += "<div class='dvItem_text'>" + item.value + "</div>";
        template += "</div>";
        return template;
    }

    pLayout.getCell("estimateDet").attach(pGridEstimateDet);
    pLayout.getCell("estimateItems").attach(dvEtimateItems);
    pLayout.getCell("estimateTotal").attach(gridTotals);

    function delete_line() {
        if ( wLineType == 'h' || wLineType == 'k' ) {
            wMessage = {
                header: "Delete ", text: "Confirm grouped lines deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
        } else {
            wMessage = {
                header: "Delete ", text: "Confirm line deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
        };  
        dhx.confirm(wMessage).then(function(answer){
            if (answer) {
                dhx.ajax.get("estimateWr.php?t=dl&id="+wSelected+"&l="+wLineType+"&e="+estimationId).then(function (data) {
                    loadEstimateDet();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });         
    }

    function change_margin() {
        const dhxWindow = new dhx.Window({
            width: 390,
            height: 210,
            closable: true,
            movable: true,
            modal: true,
            title: "Set parts margin"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 390,
            rows: [
                { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "input", name: "margin", required: true, label: "Margin", labelWidth: "70px", labelPosition: "left", width: "180px", },
                 {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                wMargin = form.getItem("margin").getValue();
                const config = {
                    header: "User ", text: "Confirm margin update?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=mar&id="+estimationId, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            setMargin = JSON.parse('{ "value": "'+wMargin+'%" }');
                            tbEstimateDet.data.update("margin",setMargin);
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        if ( estimationId != 0 ) {
            dhx.ajax.get("estimateQy.php?t=mar&id="+estimationId).then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        }
        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function add_edit_note() {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 360,
            closable: true,
            movable: true,
            modal: true,
            title: "Add / edit note"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "textarea", name: "estNote", required: false, label: "Note", labelWidth: "100px", labelPosition: "top", },
               { type: "checkbox", name: "estNoteCust", label: "Show customer?", labelWidth: "110px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ", text: "Confirm note update?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=n&id="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        if ( wSelected != 0 ) {
            dhx.ajax.get("estimateQy.php?t=n&id="+wSelected).then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        }
        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function import_file() {
        const dhxWindow = new dhx.Window({
            width: 500,
            height: 400,
            closable: true,
            movable: true,
            modal: true,
            title: "File importation"
        });
        urlTarget = "uploaderFile.php?t=est&id="+impName;
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 500,
            rows: [
                { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "fileType", required: true, label: "File type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "simpleVault", name: "impFile", required: false, label: "File", labelWidth: "100px", labelPosition: "top", target: urlTarget, autosend: true, },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ", text: "Confirm file importation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateImp.php?t=f&id="+impName, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
//                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                            show_imported_lines();
                        });
                    };
                });         
            };
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'fileType' ) {
                form.getItem("impFile").enable();
            }
        });

        impType = form.getItem("fileType").getWidget();
        impType.data.load("commonQy.php?t=impFiles").then(function(){ 
            form.getItem("impFile").disable();
        });


        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function show_imported_lines() {
        const dhxWindow = new dhx.Window({
            width: 800,
            height: 500,
            closable: true,
            movable: true,
            modal: true,
            footer: true,
            title: "Imported lines"
        });
        dhxWindow.footer.data.add({
            type: "spacer",
        });

        dhxWindow.footer.data.add({
            type: "button",
            view: "link",
            size: "medium",
            color: "primary",
            value: "decline",
            id: "decline"
        });

        dhxWindow.footer.events.on("click", function (id) {
            if (id === "decline") {
                dhxWindow.destructor();
            }
            if (id === "accept") {
                dhx.ajax.get("estimateWr.php?t=impf&eId="+estimationId+"&gl="+wSelected).then(function (data) {
                    console.log(data);
                    loadEstimateDet();
                    dhxWindow.destructor();
                }).catch(function (err) {
                        console.log(err);
                });               
            }
        });

        dhxWindow.footer.data.add({
            type: "button",
            view: "flat",
            size: "medium",
            color: "primary",
            value: "accept",
            id: "accept",
        });        

        impGrid = new dhx.Grid(null, {
            columns: [
                { width: 0, id: "id", header: [{ text: "" }], autoWidth: true, align: "left", hidden: true },
                { width: 70, id: "estReference", header: [{ text: "Ref." }], autoWidth: true, mark: function (cell, data, row, column ) { return row.prodError == 1 ? "imp_error_hard" : "" } },
                { width: 0, id: "estDesign", header: [{ text: "Designation" }], autoWidth: true, align: "left", minWidth: 150, htmlEnable: true },
                { width: 50, id: "estQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right" },
                { width: 80, id: "estItemValue", header: [{ text: "Price" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 55, id: "labourGroup", header: [{ text: "Size" }], autoWidth: true, align: "left", mark: function (cell, data, row, column ) {return row.sizeError == 1 ? "imp_error_hard" : row.sizeError == 2 ? "imp_error_soft" : "" } },
                { width: 80, id: "labourAmount", header: [{ text: "Amount" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
                { width: 70, id: "withInstall", header: [{ text: "Labour" }], autoWidth: true },
            ],
//            rowCss: function(row) { return row.sizeError == 0 ? "my_сustom_сlass" : "" },
            selection: false,
            adjust: "true",
            data: dsImp
        });
        impGrid.data.load("estimateQy.php?t=impLines&eId="+estimationId+"&lk="+wSelected);

        dhxWindow.attach(impGrid);
        dhxWindow.show();
    }

    function add_h_line(argument) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Group line"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", required: true, label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=h&e="+estimationId, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        if ( wSelected != 0 ) {
            dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        }
        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    }

    function add_l_line(gLine) {
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
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
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
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
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
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=l&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });
        if ( wSelected != 0 ) {
            dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        }

        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    }

    function add_s_line(gLine) {
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
                { type: "combo", name: "estReference", required: true, label: "Service type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "combo", name: "unitLine", readOnly: true, label: "Unit", labelWidth: "100px", labelPosition: "left", width: "280px", },
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        cbUnit = form.getItem("unitLine").getWidget();
        cbUnit.data.load("commonQy.php?t=unit").then(function(){ });
        cbType = form.getItem("estReference").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=s").then(function(){
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbUnit.data.load("commonQy.php?t=unit").then(function(){
                        cbUnit.setValue(obj.unitLine);
                    });
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' && wSelected == 0 ) {
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
                    header: "User ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "User ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=s&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function add_e_line(gLine) {
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
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
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
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' && wSelected == 0 ) {
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
                        wSelected = 0;
                        loadEstimateDet();
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
                        const send = form.send("estimateWr.php?t=f&l=e&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });
        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    };
    function add_p_line(gLine) {
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
                { type: "combo", name: "estCode", required: true, label: "Product", labelWidth: "100px", labelPosition: "left", readOnly: false},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                {
                    align: "start",
                    cols: [
                        { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                        { type: "input", name: "unitLine", readOnly: true, label: "Unit", labelWidth: "100px", labelPosition: "left", width: "180px", },
                    ]
                },                
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
                { type: "combo", name: "prodSize", readOnly: true, label: "Size", labelWidth: "100px", labelPosition: "left", width: "280px", },
                {
                    align: "start",
                    cols: [
                        { type: "checkbox", name: "withInstall", label: "Installation", labelWidth: "100px", labelPosition: "left", width: "180px", },
                        { type: "input", name: "labourAmount", label: "Labour", labelWidth: "100px", labelPosition: "left", width: "200px", },
                    ]
                },                
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
        cbSize = form.getItem("prodSize").getWidget();
        cbType.data.load("commonQy.php?t=fam&s=p").then(function(){ 
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=p&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                    cbCode.data.load("commonQy.php?t=prod&s="+obj.estReference).then(function(){
                        cbCode.setValue(obj.estCode);
                    });
                    cbSize.data.load("commonQy.php?t=size&s="+obj.estCode).then(function(){
                        cbSize.setValue(obj.prodSize);
                    });
                    form.getItem("estReference").disable();
                    form.getItem("estCode").disable();
                    form.getItem("unitLine").disable();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' && wSelected == 0 ) {
                cbCode.data.parse({});
                cbCode.clear();
                cbCode.data.load("commonQy.php?t=prod&s="+new_value).then(function(){
                });
            }
            if ( name == 'estCode'  && wSelected == 0 ) {
                dhx.ajax.get("estimateQy.php?t=prod&v="+new_value).then(function (data) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    form.setValue(obj);
                    cbSize.data.parse({});
                    cbSize.clear();
                    cbSize.data.load("commonQy.php?t=size&s="+new_value).then(function(){
                    });
                });
            }
            if ( name == 'withInstall'  && wSelected != 0 ) {
                if ( new_value == 0 ) {
                    form.setValue({"labourAmount": "0"});
                }
            }
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Product ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "Product ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=p&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
//                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function add_c_line(gLine) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Consumables"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Consumable type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "combo", name: "estCode", required: true, label: "Consumable", labelWidth: "100px", labelPosition: "left", readOnly: false},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "unitLine", readOnly: true, label: "Unit", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
                { type: "checkbox", name: "costOnlyLine", label: "Cost only", labelWidth: "100px", labelPosition: "left", width: "250px",  },                {
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
        cbType.data.load("commonQy.php?t=fam&s=c").then(function(){ 
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=c&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                    cbType.setValue(obj.estReference);
                    cbCode.data.load("commonQy.php?t=csmb&s="+obj.estReference).then(function(){
                        cbCode.setValue(obj.estCode);
                    });
                    form.getItem("estReference").disable();
                    form.getItem("estCode").disable();
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' && wSelected == 0 ) {
                cbCode.data.parse({});
                cbCode.clear();
                cbCode.data.load("commonQy.php?t=csmb&s="+new_value).then(function(){
                });
            }
            if ( name == 'estCode' && wSelected == 0 ) {
                dhx.ajax.get("estimateQy.php?t=csmb&v="+new_value).then(function (data) {
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
                    header: "Consumable ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "Consumable ", text: "Confirm line creation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=c&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
//                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    }
    function add_k_line(gLine) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Packs"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Pack", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
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
        cbType.data.load("commonQy.php?t=pack").then(function(){
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' ) {
                dhx.ajax.get("estimateQy.php?t=pack&l=k&v="+new_value).then(function (data) {
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
                    header: "Pack ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "Pack ", text: "Confirm adding pack?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=k&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });
        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    };
    function add_n_line(gLine) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Notes"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "textarea", name: "estNote", required: true, label: "Note", labelWidth: "100px", labelPosition: "left", },
                {
                    align: "end",
                    cols: [
                        { type: "button", name: "cancel", view: "link", text: "Cancel", },
                        { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                    ]
                },
            ]
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Note ", text: "Confirm cancelation?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        wSelected = 0;
                        loadEstimateDet();
                        form.destructor();
                        dhxWindow.destructor();
                    }
                });         
            };
            if ( name == 'send' ) {
                const config = {
                    header: "Note ", text: "Confirm adding note?", buttons: ["no", "yes"], buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=f&l=n&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });
        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    };
    function add_t_line(gLine) {
        const dhxWindow = new dhx.Window({
            width: 640,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Transport"
        });
        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            width: 640,
            rows: [
                { type: "input", name: "id", label: "Id", labelWidth: "100px", labelPosition: "left", hidden: true },
                { type: "combo", name: "estReference", required: true, label: "Transport type", labelWidth: "100px", labelPosition: "left", readOnly: true},
                { type: "input", name: "estDesign", required: true, label: "Designation", labelWidth: "100px", labelPosition: "left", },
                { type: "input", name: "estQuant", required: true, label: "Quantity", labelWidth: "100px", labelPosition: "left", width: "180px", },
                { type: "input", name: "estItemValue", required: true, label: "Price", labelWidth: "100px", labelPosition: "left", width: "200px",  },
                { type: "input", name: "stLineVAT", required: true, label: "VAT", labelWidth: "100px", labelPosition: "left", width: "250px",  },
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
        cbType.data.load("commonQy.php?t=fam&s=t").then(function(){
            if ( wSelected != 0 ) {
                dhx.ajax.get("estimateQy.php?t=r&id="+wSelected).then(function (data) {
                    var obj = JSON.parse(data);
                    console.log(obj);
                    form.setValue(obj);
                }).catch(function (err) {
                        console.log(err);
                });
            }
        });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'estReference' && wSelected == 0 ) {
                dhx.ajax.get("estimateQy.php?t=ref&l=t&v="+new_value).then(function (data) {
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
                        wSelected = 0;
                        loadEstimateDet();
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
                        const send = form.send("estimateWr.php?t=f&l=t&e="+estimationId+"&gl="+gLine, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            wSelected = 0;
                            loadEstimateDet();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });
        dhxWindow.events.on("afterHide", function(position, events){
            wSelected = 0;
            loadEstimateDet();
            form.destructor();
            dhxWindow.destructor();
        });       
        dhxWindow.attach(form);
        dhxWindow.show();
    };

    function addEstimation(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 300,
            closable: true,
            movable: true,
            modal: true,
            title: "Estimation"
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
                            { id: "estId", name: "estId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "estName", name: "estName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "estimative name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "estProjectId", name: "estProjectId", type: "combo", label: "Project", labelWidth: "70px", width: "530px",  placeholder: "project", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
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
        cbProject = form.getItem("estProjectId").getWidget();
        cbProject.data.load("commonQy.php?t=prjs").then(function(){
            console.log("cbProject");
            dhx.ajax.get("estimateQy.php?t=estimation&r="+estimationId).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
                var obj = form.getValue();
                cbProject.setValue(obj.estProjectId);
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Estimation ",
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
                    header: "Estimation ",
                    text: "Confirm estimation creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("estimateWr.php?t=fe&r="+estimationId, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            estimationId = 0;
                            tbEstimations.disable(['edit', 'delete','view']);
                            loadEstimations();
                            form.destructor();
                            dhxWindow.destructor();
                        });
                    };
                });         
            };
        });

        dhxWindow.attach(form);
        dhxWindow.show();
    };

    function isNumber(value) {
      return typeof value === 'number';
    }

    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=estimate").then(function (data) {
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