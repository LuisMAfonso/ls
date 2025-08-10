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
    var pGridCustomers, dsMachines, mainForm, dsRequests;
    var tbMachine, tbDetails, cbSupplier, tabDet, cntGrid, cntLayout, tbRequests;
    var cbFam, cbSFam;
    var fam = subFam = '';
    var wSelected = wSelCnt = 0;

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
              	{ id: "lTbMachine", html: "", height: "55px" },
              	{ id: "machines", html: "" },
              ]
            },
            { type: "line",
              width: "620px",
              rows: [ 
                { id: "machine", html: "",  },
              ]
            },
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);

    var f_machine = {
        css: "bg-promo",
        align: "start",
        width: "615px",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Machine details",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "machineId", name: "machineId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "machine Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "macName", name: "macName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "machine name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macPlate", name: "macPlate", type: "input", label: "Plate Nr.", labelWidth: "70px", width: "330px",  placeholder: "plate number", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macOwner", name: "macOwner", type: "input", label: "Owner", labelWidth: "70px", width: "230px",  placeholder: "owner", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macSupplier", name: "macSupplier", type: "input", label: "Supplier", labelWidth: "70px", width: "530px",  placeholder: "supplier", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macSerialNumber", name: "macSerialNumber", type: "input", label: "Serial Nr.", labelWidth: "70px", width: "450px",  placeholder: "serial number", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macLabelCode", name: "macLabelCode", type: "input", label: "Label code", labelWidth: "70px", width: "300px",  placeholder: "label code", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "famName", name: "famName", type: "input", label: "Family", labelWidth: "70px", width: "200px",  placeholder: "family", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "sfamName", name: "sfamName", type: "input", label: "S.family", labelWidth: "60px", width: "350px",  placeholder: "sub.family", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                }            
            ]
          },
          {
            name: "fieldset2",
            type: "fieldset",
            label: "Machine maintenance",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "macMaintenanceDesc", name: "macMaintenanceDesc", type: "input", label: "With?", labelWidth: "40px", width: "120px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true } ,   
                        { id: "macMaintUnit", name: "macMaintUnit", type: "input", label: "Unit", labelWidth: "50px", width: "110px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true } ,   
                        { id: "macMaintFirst", name: "macMaintFirst", type: "input", label: "First", labelWidth: "50px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }  ,   
                        { id: "macMaintNext", name: "macMaintNext", type: "input", label: "Next", labelWidth: "50px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }       
                    ]
                }
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_machine);
    dsMachines = new dhx.DataCollection();
    dsRequests = new dhx.DataCollection();
    loadUsers();

    function loadUsers() {
        dsMachines.removeAll();
        dsMachines.load("mntMachineQy.php?t=machines").then(function(){
    //      console.log("done users read");
        });
    }

    tbMachine = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbMachine.data.load("toolbarsQy.php?t=t_aeds").then(function(){
        tbMachine.disable(['edit', 'delete']);
    });;
	tbMachine.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridCustomers.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbMachine.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditMachine(); }
        if ( id == 'add' )  { wSelected = 0; addEditMachine(); }
    });
	pLayout.getCell("lTbMachine").attach(tbMachine);

    pGridCustomers = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], align: "right", autoWidth: true },
            { width: 50, id: "Icon", header: [{ text: "I." }], gravity: 1.5, align: "center", hidden: false, htmlEnable: true},
            { width: 0, id: "macName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 120, id: "macPlate", header: [{ text: "Plate Nr" }], autoWidth: true },
            { width: 140, id: "sfamName", header: [{ text: "Equip. Family" }], autoWidth: true },
            { width: 70, id: "macOwner", header: [{ text: "Owner" }], autoWidth: true },
        ],
        selection:"row",
        adjust: "true", 
        data: dsMachines
    });
    pGridCustomers.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelected = row.Id;
        fam = subFam = '';
        tbMachine.enable(['edit', 'delete']);

        dhx.ajax.get("mntMachineQy.php?t=machine&r="+row.Id).then(function (data) {
            obj = JSON.parse(data);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
            loadRequests();
        }).catch(function (err) {
            console.log(err);
        });
    });


    pLayout.getCell("machines").attach(pGridCustomers);

    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "Machine Info"},
            { id: "requests", tab: "Requests"}
        ]
    });
    pLayout.getCell("machine").attach(tabDet);
    tabDet.getCell("info").attach(mainForm);

    cntGrid = new dhx.Grid(null, {
        columns: [
            { width: 50, id: "Id", header: [{ text: "Id" }], align: "right", autoWidth: true, hidden: true,  },
            { width: 0, id: "projName", header: [{ text: "Project" }], autoWidth: true, align: "left" },
            { width: 90, id: "fromDate", header: [{ text: "From" }], autoWidth: true },
            { width: 90, id: "toDate", header: [{ text: "To" }], autoWidth: true },
            { width: 120, id: "userName", header: [{ text: "Requested by" }], autoWidth: true },
        ],
        selection:"row",
//        adjust: "true", 
        data: dsRequests
    });
    cntGrid.events.on("cellClick", function(row,column){
        console.log(row.Id+" - "+column.id);

        wSelCnt = row.Id;
        tbRequests.enable(['edit', 'delete']);

        dhx.ajax.get("mntMachineQy.php?t=requests&r="+row.Id).then(function (data) {
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
                { id: "lTbRequests", html: "", height: "55px" },
                { id: "gRequests", html: "" },
              ]
            },
        ]
    });

    tbRequests = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbRequests.data.load("toolbarsQy.php?t=e_aed").then(function(){
        tbRequests.disable(['edit', 'delete']);
    });;
    tbRequests.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edit' ) { addEditRequest(); }
        if ( id == 'add' )  { wSelCnt = 0; addEditRequest(); }
    });

    mainLayout.getCell("workplace").attach(pLayout);
    tabDet.getCell("requests").attach(cntLayout);
    cntLayout.getCell("lTbRequests").attach(tbRequests);
    cntLayout.getCell("gRequests").attach(cntGrid);

    function loadRequests() {
        dsRequests.removeAll();
        dsRequests.load("mntMachineQy.php?t=requests&r="+wSelected).then(function(){
            console.log("done contacts read");
        });
    }

    function addEditMachine(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 660,
            closable: true,
            movable: true,
            modal: true,
            title: "Machine"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "635px",
            rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Machine details",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "machineId", name: "machineId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "machine Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "macName", name: "macName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "machine name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macPlate", name: "macPlate", type: "input", label: "Plate Nr.", labelWidth: "70px", width: "330px",  placeholder: "plate number", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                         { id: "macOwnerId", name: "macOwnerId", type: "select", label: "Owner", labelWidth: "70px", width: "230px",  placeholder: "owner", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "0", content: "External"},  { value: "1", content: "Internal"}]  }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macSupplierId", name: "macSupplierId", type: "combo", label: "Supplier", labelWidth: "70px", width: "530px",  placeholder: "supplier", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macSerialNumber", name: "macSerialNumber", type: "input", label: "Serial Nr.", labelWidth: "70px", width: "450px",  placeholder: "serial number", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "macLabelCode", name: "macLabelCode", type: "input", label: "Label code", labelWidth: "70px", width: "300px",  placeholder: "label code", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        }
                   ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "familyId", name: "familyId", type: "combo", label: "Family", labelWidth: "70px", width: "200px",  placeholder: "family", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "subFamilyId", name: "subFamilyId", type: "combo", label: "S.family", labelWidth: "60px", width: "350px",  placeholder: "sub.family", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                }            
            ]
          },
          {
            name: "fieldset2",
            type: "fieldset",
            label: "Machine maintenance",        
            rows:[
                {      
                align: "start", 
                    cols: [
                         { id: "macMaintenance", name: "macMaintenance", type: "select", label: "With?", labelWidth: "40px", width: "120px",  placeholder: "owner", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true, options: [ { value: "0", content: "No"},  { value: "1", content: "Yes"}]  }   ,
                        { id: "macMaintUnit", name: "macMaintUnit", type: "input", label: "Unit", labelWidth: "50px", width: "110px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true } ,   
                        { id: "macMaintFirst", name: "macMaintFirst", type: "input", label: "First", labelWidth: "50px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }  ,   
                        { id: "macMaintNext", name: "macMaintNext", type: "input", label: "Next", labelWidth: "50px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }       
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
        cbFam = form.getItem("familyId").getWidget();
        cbSFam = form.getItem("subFamilyId").getWidget();
        cbSupplier = form.getItem("macSupplierId").getWidget();
        cbSupplier.data.load("commonQy.php?t=sup").then(function(){
            dhx.ajax.get("mntMachineQy.php?t=machine&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.clear('validation');
                form.setValue(obj);
//                var obj = form.getValue();
                if ( obj.macOwnerId == 1 ) form.getItem("macSupplierId").disable();
                cbSupplier.setValue(obj.macSupplierId);
                cbFam.setValue(obj.familyId);
                fam = obj.familyId;
                subFam = obj.subFamilyId;
                
                console.log(obj);
             }).catch(function (err) {
                    console.log(err);
            });
        });
        cbFam.data.load("commonQy.php?t=family").then(function(){  });
        form.events.on("Change",function(name, new_value){
            console.log(name+" - "+new_value);
            if ( name == 'familyId' && new_value != '' ) {
                if ( new_value == fam ) return;
                fam = new_value;
                cbSFam.data.parse({});
                cbSFam.clear();
                cbSFam.data.load("commonQy.php?t=sfam&s="+new_value).then(function(){
                    if ( subFam != '' ) cbSFam.setValue(subFam);
                });
            }
            if ( name == 'macOwnerId' && new_value != '' ) {
                form.getItem("macSupplierId").enable();
                if ( new_value == 1 ) form.getItem("macSupplierId").disable();
            }
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
                        const send = form.send("mntMachineWr.php?t=f&r="+wSelected, "POST").then(function(data){
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