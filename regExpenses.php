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
    var pGridExpenses, dsExpenses, mainForm, dsContacts;
    var tbExpenses, tbDetails, cbCountry, tabDet, cntGrid, cntLayout, tbContacts, cbCType, cbBAct;
    var wSelected = wSelCnt = 0;
    var dtFrom = new Date().toISOString().slice(0,8)+"01";
    var dtTo = new Date().toISOString().slice(0,10);
    var cbProject, cbExpType, cbStaff;

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
    dsExpenses = new dhx.DataCollection();
    dsContacts = new dhx.DataCollection();

    tbExpenses = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbExpenses.data.load("toolbarsQy.php?t=exp_daed").then(function(){
        tbExpenses.disable(['edit', 'delete']);

        tbExpenses.data.update("dtFrom", { value: dtFrom });
        tbExpenses.data.update("dtTo", { value: dtTo });
    });
	tbExpenses.events.on("inputChange", function (event, arguments) {
		console.log(event+" "+arguments);
        if ( event == 'dtFrom' ) {
            dtFrom = arguments;
            loadExpenses();
        }
        if ( event == 'dtTo' ) {
            dtTo = arguments;
            loadExpenses();
        }
    });
    tbExpenses.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditExpense(); }
        if ( id == 'add' )  { wSelected = 0; addEditExpense(); }
    });
	pLayout.getCell("lTbCompany").attach(tbExpenses);

    pGridExpenses = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "Id", header: [{ text: "Id" }], autoWidth: true },
            { width: 150, minWidth: 100, id: "expTypeName", header: [{ text: "Type" }], autoWidth: true, align: "left" },
            { width: 90, id: "expDate", header: [{ text: "Date" }], autoWidth: true, align: "left" },
            { width: 90, id: "user", header: [{ text: "User" }], autoWidth: true, align: "left" },
            { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Staff" }], autoWidth: true },
            { width: 80, id: "expValue", header: [{ text: "Amount" }], autoWidth: true, align: "right", type: "number", numberMask: { maxDecLength: 2, minDecLength: 2, decSeparator: ".", groupSeparator: "," }, },
            { width: 40, id: "image", header: [{ text: "image" }], autoWidth: true, hidden: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsExpenses
    });
    pGridExpenses.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id+" "+canModify+" "+canDelete);

        wSelected = row.Id;
        if ( canModify == 1 ) tbExpenses.enable(['edit']);
        if ( canDelete == 1 ) tbExpenses.enable(['delete']);

        if ( row.ext == 'pdf' ) {
            tabDet.getCell("image").attachHTML('<head><meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" /><meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Expires" content="0" /></head><body><embed src="images/imgUsers/expense/'+row.image+'" width="100%" height="100%"/></body>');
        } else {
            tabDet.getCell("image").attachHTML('<head><meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" /><meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Expires" content="0" /></head><body><img src="images/imgUsers/expense/'+row.image+'" width="100%" height="100%"/></body>');
        }

        dhx.ajax.get("regExpensesQy.php?t=expense&r="+wSelected).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
            mainForm.clear('validation');        
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("companies").attach(pGridExpenses);

    tabDet = new dhx.Tabbar(null, { 
        views: [
            { id: "image", tab: "Document"},
            { id: "info", tab: "General Info"}
        ]
    });
    pLayout.getCell("details").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

    mainLayout.getCell("workplace").attach(pLayout);

    loadPermission()

    function addEditExpense(argument) {
        const dhxWindow = new dhx.Window({
            width: 570,
            height: 600,
            closable: true,
            movable: true,
            modal: true,
            title: "Expense registration"
        });
        urlTarget = "uplExpense.php?id="+wSelected;

        const form = new dhx.Form(null, {
            css: "dhx_widget--bordered",
            padding: 10,
            rows: [
                { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
                { type: "datepicker", name: "expDate", label: "Date", labelPosition: "left", labelWidth: "50px", width: "200px", dateFormat: "%Y-%m-%d" },
                { id: "expId", name: "expId", type: "combo", label: "Exp.Type", labelWidth: "50px", width: "380px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
                { id: "projId", name: "projId", type: "combo", label: "Project", labelWidth: "50px", width: "400px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
                { id: "staffId", name: "staffId", type: "combo", label: "Staff", labelWidth: "50px", width: "350px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
                { type: "input", name: "expValue", label: "Value", labelPosition: "left", labelWidth: "50px", width: "200px", validation: "numeric",  },
                { type: "textarea", name: "workDone", required: false, label: "Reason", labelPosition: "left", labelWidth: "50px" },
                { type: "simpleVault", name: "impFile", required: true, label: "File", labelWidth: "50px", labelPosition: "left", target: urlTarget, autosend: false, },
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
        cbExpType = form.getItem("expId").getWidget();
        cbProject = form.getItem("projId").getWidget();
        cbProject.data.load("commonQy.php?t=prjs").then(function(){
            dhx.ajax.get("regExpensesQy.php?t=exp&id="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                console.log(obj);
                form.setValue(obj);
                cbProject.setValue(obj.projId);
                cbExpType.data.load("commonQy.php?t=fam&s=x").then(function(){
                     cbExpType.setValue(obj.expId);
                });
                cbStaff.data.load("commonQy.php?t=rt").then(function(){
                     cbStaff.setValue(obj.staffId);
                });
            }).catch(function (err) {
                console.log(err);
            });
        });
        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Expense registration ",
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
                    header: "Expense registration ",
                    text: "Confirm expense registration?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("regExpensesWr.php?t=exp", "POST").then(function(data){
    //                            message = JSON.parse(data);
                            console.log(data);
                            loadExpenses();
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

    function loadExpenses() {
        dsExpenses.removeAll();
        dsExpenses.load("regExpensesQy.php?t=expenses&df="+dtFrom+"&dt="+dtTo).then(function(){
    //      console.log("done users read");
            if ( canCreate == 1 ) tbExpenses.enable(['add']);
        });
        tabDet.getCell("image").attachHTML('');
    }
    function loadPermission() {
        dhx.ajax.get("menuQy.php?t=per&p=regExpenses").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;     
            loadExpenses();      
        }).catch(function (err) {
            console.log(err);
        });
    };

</script>