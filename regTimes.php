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
    var pGridTimes, dsTimes, mainForm, dsDetails;
    var tbTimes, gridDet;
    var wSelected = wSelCnt = 0;
    var dtFrom = new Date().toISOString().slice(0,8)+"01";
    var dtTo = new Date().toISOString().slice(0,10);
    var byWhat = 'proj';

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
                { id: "details", html: "",  },
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
            label: "Expense info",        
            rows:[
               { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "input", name: "expDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px" },
                { id: "expTypeName", name: "expTypeName", type: "input", label: "Exp.Type", labelWidth: "50px", width: "380px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
                { id: "projName", name: "projName", type: "input", label: "Project", labelWidth: "50px", width: "400px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
                { id: "staffName", name: "staffName", type: "input", label: "Staff", labelWidth: "50px", width: "350px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
                { type: "input", name: "expValue", label: "Value", labelPosition: "left", labelWidth: "50px", width: "200px", validation: "numeric",  },
                { type: "textarea", name: "workDone", required: false, label: "Reason", labelPosition: "left", labelWidth: "50px" },
            ]
          },
          {
            name: "fieldset2",
            type: "fieldset",
            label: "Creation info",        
            rows:[
               { type: "input", name: "createStamp", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", readOnly: true },
                { id: "userName", name: "userName", type: "input", label: "User", labelWidth: "50px", width: "380px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true },
            ]
          }

        ] 
    };
    mainForm = new dhx.Form(null, f_company);
    dsTimes = new dhx.DataCollection();
    dsDetails = new dhx.DataCollection();

    tbTimes = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbTimes.data.load("toolbarsQy.php?t=rt_sp").then(function(){
//        tbTimes.disable(['export']);

        tbTimes.data.update("dtFrom", { value: dtFrom });
        tbTimes.data.update("dtTo", { value: dtTo });
    });
	tbTimes.events.on("inputChange", function (event, arguments) {
		console.log(event+" "+arguments);
        if ( event == 'dtFrom' ) {
            dtFrom = arguments;
            loadTimes();
        }
        if ( event == 'dtTo' ) {
            dtTo = arguments;
            loadTimes();
        }
    });
    tbTimes.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'proj' || id == 'staff' ) { 
            byWhat = id; 
            loadTimes();
        }
        if ( id == 'export' ) {
            window.open("regTimesQy.php?t=export&r="+byWhat,'_blank');
        }
    });
	pLayout.getCell("lTbCompany").attach(tbTimes);

    pGridTimes = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "name", header: [{ text: "Proj / Staff" }], autoWidth: true },
            { width: 150, minWidth: 150, id: "posCity", header: [{ text: "City / Pos." }], autoWidth: true, align: "left" },
            { width: 70, id: "tTime", header: [{ text: "Hours" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
            { width: 90, id: "tValue", header: [{ text: "Value" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsTimes
    });
    pGridTimes.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id+" "+canModify+" "+canDelete);

//        tbTimes.enable(['export']);
        wSelected = row.Id;
        loadDetails();
    });

    gridDet = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "name", header: [{text: "Proj / Staff"}], autoWidth: true },
            { width: 100, id: "date", header: [{ text: "Date" }], autoWidth: true, align: "left" },
            { width: 70, id: "tTime", header: [{ text: "Hours" }], align: "right", autoWidth: true, type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, footer: [{ text: ({ sum }) => sum }], summary: "sum" },
            { width: 150, minWidth: 150, id: "workDone", header: [{ text: "Work done" }], autoWidth: true, align: "left" },
        ],
        selection:"row",
        adjust: "true", 
        data: dsDetails
    });

    pLayout.getCell("companies").attach(pGridTimes);
    pLayout.getCell("details").attach(gridDet);

    mainLayout.getCell("workplace").attach(pLayout);

    loadPermission()

    function loadTimes() {
        dsTimes.removeAll();
        dsDetails.removeAll();
        dsTimes.load("regTimesQy.php?t="+byWhat+"&df="+dtFrom+"&dt="+dtTo).then(function(){
    //      console.log("done users read");
        });
    }
    function loadDetails() {
        dsDetails.removeAll();
        dsDetails.load("regTimesQy.php?t=det"+byWhat+"&df="+dtFrom+"&dt="+dtTo+"&r="+wSelected).then(function(){
    //      console.log("done users read");
        });
    }
    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=regTimes").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;     
            loadTimes();      
        }).catch(function (err) {
            console.log(err);
        });
    };

</script>