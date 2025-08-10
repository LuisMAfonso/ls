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
    .dhx_grid-cell:has(.color-cell-template) {
        padding: 0;
    }
    .color-cell-template {
        width: 100%;
        height: 100%;
        display: flex;
        padding: 0 12px;
        align-items: center;
    }
</style>

<?php
require_once('sidebar.php');
?>
<script>
    var pLayout, prjLayout, tbLayoutMaps;
    var pGridProjects, dsProjects, mainForm, projForm, estGrid;
    var tbProjects, tbDetails, tbInfo, tabDet; 
    var cbCountry, cbCustomer, cbCustomerf, cbStatus, cbType, cbPrio;
    var estimatesGrid, gridTotals;
    var wSelected = 0;
    var gMap, myMap, mapParams;
    var urlDesign = urlProj = projName = '';
    var pLat = pLng = 0;
    var pAvatar;

    loadPermission();

    pLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
          	{ id: "projects", header: "Projects", collapsable: true, html: "" },
            { type: "line",
              rows: [ 
                { id: "lTbInfo", html: "", height: "55px" },
                { id: "information", collapsable: false, hidden: true, html: "" },
              ]
            },
            { id: "content", header: "", collapsable: false, width: "600px", html: "" },
            { id: "estimates", width: "500px", hidden: true, type: "wide",
                rows: [ 
                    { id: "estimates_list", header: "", collapsable: false, },
                    { id: "estimates_total", header: "", collapsable: false, height: "244px", },
                ]
            }
        ]
    });
    mainLayout.getCell("workplace").attach(pLayout);
    pLayout.events.on("afterExpand", function(id){
        console.log(id);
        if ( id == 'projects' ) {
            pLayout.getCell("content").show();
            pLayout.getCell("projects").config.header='Projects';
            pLayout.getCell("information").hide();
//            pLayout.getCell("information").config.header=' ';
            pLayout.getCell("estimates").hide();
            pLayout.paint();
            pGridProjects.selection.removeCell(wSelected);
        }
    });
    pLayout.events.on("afterShow", function(id){
        console.log(id);
    });

    prjLayout = new dhx.Layout(null, {
        type: "space",
        cols: [
            { type: "line",
              rows: [ 
                { id: "lTbProjects", html: "", height: "55px" },
                { id: "gProjects", html: "" },
              ]
            },
        ]
    });
    pLayout.getCell("projects").attach(prjLayout);

    var f_projForm = {
        css: "bg-promo",
        align: "start",
        width: "98%",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Project dates",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "lbEmpty", name: "lbEmpty", type: "text", label: "", labelWidth: "150px", width: "230px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbDesign", name: "lbDesign", type: "text", label: "Design", labelWidth: "30px", width: "185px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbProjMan", name: "lbProjMan", type: "text", label: "Proj.Manager", labelWidth: "30px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbSiteMan", name: "lbSiteMan", type: "text", label: "Site Manager", labelWidth: "30px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "fvLabel", name: "fvLabel", type: "text", label: "First site visit", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "fvDesign", name: "fvDesign", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "fvProjMan", name: "fvProjMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "fvSiteMan", name: "fvSiteMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "fvLink", name: "fvLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "soLabel", name: "soLabel", type: "text", label: "Site observations", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "soDesign", name: "soDesign", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "soProjMan", name: "soProjMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "soSiteMan", name: "soSiteMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "soLink", name: "soLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "dlLabel", name: "dlLabel", type: "text", label: "Delivery", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "dlDesign", name: "dlDesign", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "dlProjMan", name: "dlProjMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "dlSiteMan", name: "dlSiteMan", type: "input", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "dlLink", name: "dlLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "propAcc", name: "propAcc", type: "select", label: "Project proposed / accept value", labelWidth: "210px", width: "320px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "p", content: "Proposed"},  { value: "a", content: "Accepted"}] 
                        },
                        { id: "pracDate", name: "pracDate", type: "input", label: "Date", labelWidth: "60px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "pracLink", name: "pracLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
            ]
          },
          {
            name: "fieldset2",
            type: "fieldset",
            label: "Finance / Closing information",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "warranty", name: "warranty", type: "select", label: "Warranty", labelWidth: "60px", width: "140px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "warValue", name: "warValue", type: "input", label: "Value", labelWidth: "50px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "warPerc", name: "warPerc", type: "input", label: "%", labelWidth: "30px", width: "110px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "warApprover", name: "warApprover", type: "input", label: "Approver", labelWidth: "70px", width: "300px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "warLink", name: "warLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "maintenance", name: "maintenance", type: "select", label: "Mainten.", labelWidth: "60px", width: "140px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "maiValue", name: "maiValue", type: "input", label: "Value", labelWidth: "50px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "maiPerc", name: "maiPerc", type: "input", label: "%", labelWidth: "30px", width: "110px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "maiApprover", name: "maiApprover", type: "input", label: "Approver", labelWidth: "70px", width: "300px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "maiLink", name: "maiLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "discount", name: "discount", type: "select", label: "Discount", labelWidth: "60px", width: "140px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "disValue", name: "disValue", type: "input", label: "Value", labelWidth: "50px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "disPerc", name: "disPerc", type: "input", label: "%", labelWidth: "30px", width: "110px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "disApprover", name: "disApprover", type: "input", label: "Approver", labelWidth: "70px", width: "300px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "disLink", name: "disLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "payTerms", name: "payTerms", type: "input", label: "Payment terms", labelWidth: "180px", width: "530px",  placeholder: "payment terms", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "acceptance", name: "acceptance", type: "select", label: "Signed acceptance form", labelWidth: "180px", width: "260px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "acceptDate", name: "acceptDate", type: "input", label: "Date", labelWidth: "60px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "accLink", name: "accLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "closing", name: "closing", type: "select", label: "Signed closing P.Manager", labelWidth: "180px", width: "260px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "closingDate", name: "closingDate", type: "input", label: "Date", labelWidth: "60px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "cloLink", name: "cloLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "imgAuth", name: "imgAuth", type: "select", label: "Final imaging authorization", labelWidth: "180px", width: "260px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "y", content: "Yes"},  { value: "n", content: "No"}] 
                        },
                        { id: "authDate", name: "authDate", type: "input", label: "Date", labelWidth: "60px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { type: "spacer", width: "10px", },
                        { id: "authLink", name: "authLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
             ]
          }
        ]
    };

    var f_project = {
        css: "bg-promo",
        align: "start",
        width: "98%",
        rows: [
          {
            name: "fieldset1",
            type: "fieldset",
            label: "Project info",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "projId", name: "projId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "project id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        {      
                        align: "start", 
                            cols: [
                                { id: "projName", name: "projName", type: "input", label: "Name", labelWidth: "50px", width: "550px",  placeholder: "project name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                            ]
                        },
                        { type: "spacer", width: "10px", },
                        { id: "projLink", name: "projLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "projAddress", name: "projAddress", type: "input", label: "Address", labelWidth: "50px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "projZipcode", name: "projZipcode", type: "input", label: "Zipcode", labelWidth: "50px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "projCity", name: "projCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                }
            ]
          },
          {
            name: "fieldset4",
            type: "fieldset",
            label: "Project type",        
            rows:[
                {      
                    align: "start", 
                    cols: [
                        { id: "projStatus", name: "projStatus", type: "input", label: "Status", labelWidth: "50px", width: "250px",  placeholder: "Type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "projPrioLevel", name: "projPrioLevel", type: "input", label: "Priority", labelWidth: "60px", width: "300px",  placeholder: "Business", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "projTypeSite", name: "projTypeSite", type: "input", label: "Type", labelWidth: "50px", width: "300px",  placeholder: "Business", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        },
                        { id: "projDesign", name: "projDesign", type: "input", label: "Design", labelWidth: "90px", width: "250px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },                        
                        { type: "spacer", width: "10px", },
                        { id: "openLink", name: "openLink", type: "button", icon: "mdi mdi-folder-eye-outline", labelWidth: "20px", url: "" }
                    ]
                }          
            ]
          },
          {
            name: "fieldset2",
            type: "fieldset",
            label: "Customer",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "custId", name: "custId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "customer id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "custName", name: "custName", type: "input", label: "Name", labelWidth: "50px", width: "550px",  placeholder: "customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "custType", name: "custType", type: "input", label: "C.Type", labelWidth: "50px", width: "200px",  placeholder: "Type", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "custBusiness", name: "custBusiness", type: "input", label: "Business", labelWidth: "60px", width: "350px",  placeholder: "Business", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                        }
                    ]
                }
            ]
          },
          {
            name: "fieldset3",
            type: "fieldset",
            label: "Final Customer",        
            rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "custfId", name: "custfd", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "final customer id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "custfName", name: "custfName", type: "input", label: "Name", labelWidth: "50px", width: "550px",  placeholder: "final customer name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: true }    
                    ]
                }            
            ]
          }
        ]
    };
    mainForm = new dhx.Form(null, f_project);
    mainForm.events.on("click", function(name, events) {
        console.log("click", name, events); 
        if ( name == 'openLink' ) window.open(urlDesign, "_blank");
        if ( name == 'projLink' ) window.open(urlProj, "_blank");
    });
    dsProjects = new dhx.DataCollection();

    projForm = new dhx.Form(null, f_projForm);

    function loadProjects() {
        dsProjects.removeAll();
        dsProjects.load("mntProjQy.php?t=projects").then(function(){
    //      console.log("done users read");
        });
    }

    tbProjects = new dhx.Toolbar(null, {
	  css: "dhx_widget--bordered"
	});
	tbProjects.data.load("toolbarsQy.php?t=f_aed").then(function(){
        tbProjects.disable(['edit', 'delete','view']);
    });;
	tbProjects.events.on("inputChange", function (event, arguments) {
		console.log(arguments);
        const value = arguments.toString().toLowerCase();
        pGridProjects.data.filter(obj => {
            return Object.values(obj).some(item => 
                item.toString().toLowerCase().includes(value)
            );
        });
    });
    tbProjects.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'view' ) { openProject(); }
        if ( id == 'edit' ) { addEditProject(); }
        if ( id == 'add' )  { wSelected = 0; addEditProject(); }
        if ( id == 'delete' ) {  
            wMessage = {
                header: "Delete ", text: "Confirm project deletion?", buttons: ["no", "yes"], buttonsAlignment: "center",
            };   
            dhx.confirm(wMessage).then(function(answer){
                if (answer) {
                    console.log(answer);
                    dhx.ajax.get("mntProjWr.php?t=del&r="+wSelected).then(function (data) {
                        wSelected = 0;
                        tbProjects.disable(['edit', 'delete','view']); 
                        loadProjects();
                }).catch(function (err) {
                            console.log(err);
                    });
                }
            });         
        }
    });
	prjLayout.getCell("lTbProjects").attach(tbProjects);

    pGridProjects = new dhx.Grid(null, {
        columns: [
            { width: 40, id: "projId", header: [{ text: "Id" }], autoWidth: true },
            { width: 0, minWidth: 150, id: "projName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
            { width: 120, id: "projCity", header: [{ text: "City" }], autoWidth: true },
            { width: 90, id: "projStatus", header: [{ text: "status" }], autoWidth: true },
            { width: 90, id: "projPrioLevel", header: [{ text: "Prio level" }], autoWidth: true },
            { width: 90, id: "projTypeSite", header: [{ text: "Type site" }], autoWidth: true },
            { width: 70, id: "projDesign", header: [{ text: "Design" }], autoWidth: true },
            { width: 40, id: "calColor", header: [{ text: "C." }], htmlEnable: true, template },
        ],
        selection:"row",
        adjust: "true", 
        data: dsProjects
    });
    pGridProjects.events.on("cellClick", function(row,column){
        console.log(row.projId+" - "+column.id);

        wSelected = row.projId;
        tbProjects.enable(['view']);
        if ( canModify == 1 )  tbProjects.enable(['edit']);
        if ( canDelete == 1 )  tbProjects.enable(['delete']);

        dhx.ajax.get("mntProjQy.php?t=project&r="+row.projId).then(function (data) {
            obj = JSON.parse(data);
            console.log(obj.projLat+" "+obj.projLng);
            mainForm.setValue(obj);
//            mainForm.disable();
            mainForm.clear('validation');
            urlDesign = obj.projDesignLink;
            urlProj = obj.projLink;
            projName = obj.projName;
            pLat = obj.projLat;
            pLng = obj.projLng;
            pAvatar = obj.projAvatar;
            showContent();
        }).catch(function (err) {
            console.log(err);
        });
    });
    pGridProjects.events.on("cellDblClick", function(row,column,e){
        openProject();
    });
    function template(value) {
        return `<div class="color-cell-template" style="background-color: ${value || 'transparent'}"></div>`;
    };
    function showContent() {
        console.log(pLat+" "+pLng+" "+pAvatar);

        const htmlMap = '<div id="divMaps" style="width: 100%; height: 100%;"></div>';
        tbLayoutMaps.getCell("prjMaps").attachHTML(htmlMap);
        const html = '<div style="width: 100%; height: 100%;"><img src="images/imgUsers/project/'+pAvatar+'"></div>';
        tbLayoutMaps.getCell("prjPhoto").attachHTML(html);

// verificar
        initMap(parseFloat(pLat), parseFloat(pLng));
    }
    function openProject() {
        if ( wSelected != 0 ) {
            pLayout.getCell("projects").collapse();
            pLayout.getCell("projects").config.header=projName;
            pLayout.getCell("information").show();
//            pLayout.getCell("information").config.header=projName;
            pLayout.getCell("content").hide();

            pLayout.paint();

            estimatesGrid.data.load("mntProjQy.php?t=estimates&r="+wSelected).then(function(){   
                wGetEstimate = estimatesGrid.data.getItem(estimatesGrid.data.getId(0));
                wGetCol = estimatesGrid.getColumn("estimateId");
                estimatesGrid.selection.setCell(wGetEstimate);
                estGrid.data.load("estimateQy.php?t=estimate&eId="+wGetEstimate.estimateId).then(function(){   });
                gridTotals.data.load("estimateQy.php?t=totals&eId="+wGetEstimate.estimateId).then(function(){  });
            });
        }
    }

    async function initMap(projLat, projLng) {
      console.log(projLat+" "+projLng+" no initMap");
      const { Map } = await google.maps.importLibrary("maps");
      const { AdvancedMarkerView } = await google.maps.importLibrary("marker");

      const latLng = new google.maps.LatLng(projLat, projLng);
      map = new Map(document.getElementById("divMaps"), {
        center: latLng,
        zoom: 8,
        mapId: "DEMO_MAP_ID",
      });
      const marker = new google.maps.marker.AdvancedMarkerElement({
        map,
        position: latLng,
      });
    }


    tabDet = new dhx.Tabbar(null, {
        views: [
            { id: "info", tab: "General Info"},
            { id: "projDates", tab: "Project Info"},
            { id: "estimate", tab: "Estimate"},
        ]
    });
    pLayout.getCell("information").attach(tabDet);
    tabDet.events.on("change", function(id, prev){
        console.log(id+" "+prev);
        if ( id == 'estimate' ) {
            pLayout.getCell("estimates").show();
        }
        if ( prev == 'estimate' ) {
            pLayout.getCell("estimates").hide();
        }
    });
    tbInfo = new dhx.Toolbar(null, {
      css: "dhx_widget--bordered"
    });
    tbInfo.events.on("click", function(id,e){
        console.log(id);
        if ( id == 'edProj' ) { editProj(); }
        if ( id == 'edDate' ) { editDate(); }
    });
    pLayout.getCell("lTbInfo").attach(tbInfo);


    tbLayoutMaps = new dhx.Layout(null, {
        type: "line",
        cols: [
            { type: "space",
              rows: [ 
                { id: "prjMaps", html: '', height: "400px" },
                { id: "prjPhoto", html: '' },
              ]
            },
        ]
    });
    pLayout.getCell("content").attach(tbLayoutMaps);
    tabDet.getCell("info").attach(mainForm);
    tabDet.getCell("projDates").attach(projForm);

    estimatesGrid = new dhx.Grid(null, {
        columns: [
            { width: 30, id: "estimateId", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 0, id: "estName", header: [{ text: "Designation" }], autoWidth: true, align: "left", minWidth: 150, htmlEnable: true },
            { width: 70, id: "estStatusName", header: [{ text: "Status" }], autoWidth: true },
            { width: 45, id: "icon", header: [{ text: "S." }], align: "center", htmlEnable: true  },
            { width: 45, id: "iconLock", header: [{ text: "L." }], align: "center", htmlEnable: true  },
        ],
        selection: "row",
        adjust: "true",
    });
    estimatesGrid.events.on("cellClick", function(row,column){
        console.log(row.estimateId+" - "+column.id);
        estGrid.data.load("estimateQy.php?t=estimate&eId="+row.estimateId).then(function(){   });
        gridTotals.data.load("estimateQy.php?t=totals&eId="+row.estimateId).then(function(){  });
    });

    pLayout.getCell("estimates_list").attach(estimatesGrid);

    gridTotals = new dhx.Grid(null, {
        columns: [
            { width: 0, id: "id", header: [{ text: "" }], autoWidth: true, align: "left" },
            { width: 100, id: "value", header: [{ text: "Amount" }], autoWidth: true, align: "right",  type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
            { width: 100, id: "valueM", header: [{ text: "w/ Margin" }], autoWidth: true, align: "right",  type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
        ],
        rowCss: function(row) { return row.lineBold == 1 ? "row_header" : "" },
        selection: false,
        adjust: "true",
    });
    pLayout.getCell("estimates_total").attach(gridTotals);

    estGrid = new dhx.Grid(null, {
        columns: [
            { width: 30, id: "estimateDetId", header: [{ text: "Id" }], autoWidth: true, hidden: true },
            { width: 30, id: "groupLine", header: [{ text: "gl" }], autoWidth: true, hidden: true },
            { width: 40, id: "icon", header: [{ text: "I." }], align: "center", htmlEnable: true  },
            { width: 70, id: "estReference", header: [{ text: "Ref." }], autoWidth: true },
            { width: 0, id: "estDesign", header: [{ text: "Designation" }], autoWidth: true, align: "left", minWidth: 150, htmlEnable: true },
            { width: 50, id: "estQuant", header: [{ text: "Qt." }], autoWidth: true, align: "right" },
            { width: 45, id: "unitLine", header: [{ text: "Un." }], autoWidth: true, align: "left" },
            { width: 75, id: "estItemValueL", header: [{ text: "Labour" }], autoWidth: true, align: "right" , type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
            { width: 75, id: "estItemValueP", header: [{ text: "Parts" }], autoWidth: true, align: "right",   type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
            { width: 85, id: "estLineValue", header: [{ text: "S.Total" }], autoWidth: true, align: "right",   type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
            { width: 80, id: "estLineTotal", header: [{ text: "Total" }], autoWidth: true, align: "right",   type: "number", numberMask: { maxDecLength: 1, minDecLength: 1, decSeparator: ".", groupSeparator: "," }, },
        ],
        rowCss: function(row) { return row.lineBold == 1 ? "row_header" : "" },
        selection: "row",
        adjust: "true",
    });
    tabDet.getCell("estimate").attach(estGrid);

    prjLayout.getCell("gProjects").attach(pGridProjects);



    function editDate(argument) {
        const dhxWindow = new dhx.Window({
            width: 900,
            height: 660,
            closable: true,
            movable: true,
            modal: true,
            title: "Project Dates"
        });
        const form = new dhx.Form(null, {
            css: "bg-promo",
            align: "start",
            width: "840px",
            rows: [
              {
                name: "fieldset1",
                type: "fieldset",
                label: "Info",        
                rows:[
                {      
                align: "start", 
                    cols: [
                        { id: "lbEmpty", name: "lbEmpty", type: "text", label: "", labelWidth: "150px", width: "230px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbDesign", name: "lbDesign", type: "text", label: "Design", labelWidth: "30px", width: "185px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbProjMan", name: "lbProjMan", type: "text", label: "Proj.Manager", labelWidth: "30px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "lbSiteMan", name: "lbSiteMan", type: "text", label: "Site Manager", labelWidth: "30px", width: "130px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "fvLabel", name: "fvLabel", type: "text", label: "First site visit", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "fvDesign", name: "fvDesign", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "fvProjMan", name: "fvProjMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "fvSiteMan", name: "fvSiteMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "fvLink", name: "fvLink", type: "input", label: "Visit.Link", labelWidth: "150px", width: "550px",  placeholder: "visit link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "soLabel", name: "soLabel", type: "text", label: "Site observations", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "soDesign", name: "soDesign", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "soProjMan", name: "soProjMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "soSiteMan", name: "soSiteMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "soLink", name: "soLink", type: "input", label: "Observ.Link", labelWidth: "150px", width: "550px",  placeholder: "observation link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "dlLabel", name: "dlLabel", type: "text", label: "Delivery", labelWidth: "150px", width: "150px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                        },
                        { id: "dlDesign", name: "dlDesign", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "dlProjMan", name: "dlProjMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                        { id: "dlSiteMan", name: "dlSiteMan", type: "datepicker", label: "", labelWidth: "10px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "dlLink", name: "dlLink", type: "input", label: "Delivey Link", labelWidth: "150px", width: "550px",  placeholder: "delivery link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
                    ]
                },
                {
                align: "start", 
                    cols: [
                        { id: "propAcc", name: "propAcc", type: "select", label: "Project proposed / accept value", labelWidth: "210px", width: "320px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true, options: [ { value: "", content: ""}, { value: "p", content: "Proposed"},  { value: "a", content: "Accepted"}] 
                        },
                        { id: "pracDate", name: "pracDate", type: "datepicker", label: "Date", labelWidth: "60px", width: "200px",  placeholder: "", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true , dateFormat: "%Y-%m-%d"
                        },
                    ]
                },
                {      
                align: "start", 
                    cols: [
                        { id: "pracLink", name: "pracLink", type: "input", label: "Prop/Accept Link", labelWidth: "150px", width: "550px",  placeholder: "proposed / accept link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
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
        dhx.ajax.get("mntProjQy.php?t=projDate&r="+wSelected).then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            form.setValue(obj);
//                var obj = form.getValue();
            console.log(obj);
        }).catch(function (err) {
                console.log(err);
        });

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Project ",
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
                    text: "Confirm project creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntProjWr.php?t=df&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadProjects();
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
    function editProj(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 660,
            closable: true,
            movable: true,
            modal: true,
            title: "Project Info / Type"
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
                            { id: "projId", name: "projId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projName", name: "projName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "project name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projLink", name: "projLink", type: "input", label: "Proj.Link", labelWidth: "70px", width: "550px",  placeholder: "project link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "projAddress", name: "projAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projZipcode", name: "projZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "projCity", name: "projCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "projCountry", name: "projCountry", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custId", name: "custId", type: "combo", label: "Customer", labelWidth: "70px", width: "530px",  placeholder: "customer", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custfId", name: "custfId", type: "combo", label: "Final Cust.", labelWidth: "70px", width: "530px",  placeholder: "final customer", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                        align: "start", 
                        cols: [
                            { id: "projStatusId", name: "projStatusId", type: "combo", label: "Status", labelWidth: "70px", width: "250px",  placeholder: "status", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: true 
                            },
                            { id: "projPrioLevelId", name: "projPrioLevelId", type: "combo", label: "Priority", labelWidth: "60px", width: "300px",  placeholder: "priority", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                            }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projTypeSiteId", name: "projTypeSiteId", type: "combo", label: "Type", labelWidth: "70px", width: "300px",  placeholder: "site type", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: true 
                            },
                             { type: "combo", name: "projDesignId", label: "Design", labelPosition: "left", labelWidth: "90px", width: "250px", data: [ {value: "Yes", id: "1"}, {value: "No", id: "0"}] },
                        ]
                    },          
                    {      
                    align: "start", 
                        cols: [
                            { id: "projDesignLink", name: "projDesignLink", type: "input", label: "Draw.Link", labelWidth: "70px", width: "550px",  placeholder: "draw link", labelPosition: "left", maxlength: "255", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "calColor", name: "calColor", type: "colorpicker", label: "Color", labelWidth: "70px", width: "290px",  placeholder: "color", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
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
        cbCountry = form.getItem("projCountry").getWidget();
        cbCustomer = form.getItem("custId").getWidget();
        cbCustomerf = form.getItem("custfId").getWidget();
        cbStatus = form.getItem("projStatusId").getWidget();
        cbType = form.getItem("projTypeSiteId").getWidget();
        cbPrio = form.getItem("projPrioLevelId").getWidget();
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("mntProjQy.php?t=project&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
//                var obj = form.getValue();
                cbCustomerf.data.load("commonQy.php?t=cus").then(function(){
                    cbCustomerf.setValue(obj.custfId);
                });
                cbCountry.setValue(obj.projCountry);
                cbCustomer.data.load("commonQy.php?t=cus").then(function(){
                    cbCustomer.setValue(obj.custId);
                });
                cbPrio.data.load("commonQy.php?t=prio").then(function(){
                    cbPrio.setValue(obj.projPrioLevelId);
                });
                cbType.data.load("commonQy.php?t=ptype").then(function(){
                    cbType.setValue(obj.projTypeSiteId);
                });
                cbStatus.data.load("commonQy.php?t=pstatus").then(function(){
                    cbStatus.setValue(obj.projStatusId);
                });
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });;

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Project ",
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
                    text: "Confirm project creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntProjWr.php?t=ef&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadProjects();
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

    function addEditProject(argument) {
        const dhxWindow = new dhx.Window({
            width: 680,
            height: 560,
            closable: true,
            movable: true,
            modal: true,
            title: "Project"
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
                            { id: "projId", name: "projId", type: "hidden", label: "Id", labelWidth: "0px", width: "150px",  placeholder: "Id", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projName", name: "projName", type: "input", label: "Name", labelWidth: "70px", width: "450px",  placeholder: "project name", labelPosition: "left", maxlength: "100", gravity: "false", readOnly: false }    
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "projAddress", name: "projAddress", type: "input", label: "Address", labelWidth: "70px", width: "530px",  placeholder: "address", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {      
                    align: "start", 
                        cols: [
                            { id: "projZipcode", name: "projZipcode", type: "input", label: "Zipcode", labelWidth: "70px", width: "200px",  placeholder: "zipcode", labelPosition: "left", maxlength: "20", gravity: "false", readOnly: false 
                            },
                            { id: "projCity", name: "projCity", type: "input", label: "City", labelWidth: "40px", width: "350px",  placeholder: "city", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false 
                            }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "projCountry", name: "projCountry", type: "combo", label: "Country", labelWidth: "70px", width: "530px",  placeholder: "country", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "custId", name: "custId", type: "combo", label: "Customer", labelWidth: "70px", width: "530px",  placeholder: "customer", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
                        ]
                    },
                    {
                    align: "start", 
                        cols: [
                            { id: "calColor", name: "calColor", type: "colorpicker", label: "Color", labelWidth: "70px", width: "290px",  placeholder: "color", labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false }
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
        cbCountry = form.getItem("projCountry").getWidget();
        cbCustomer = form.getItem("custId").getWidget();
        cbCustomer.data.load("commonQy.php?t=cus").then(function(){
        });
        cbCountry.data.load("commonQy.php?t=ct").then(function(){
            dhx.ajax.get("mntProjQy.php?t=project&r="+wSelected).then(function (data) {
                console.log(data);
                var obj = JSON.parse(data);
                form.setValue(obj);
//                var obj = form.getValue();
                cbCountry.setValue(obj.projCountry);
                cbCustomer.setValue(obj.custId);
                console.log(obj);
            }).catch(function (err) {
                    console.log(err);
            });
        });;

        form.events.on("click", function(name,e){
            console.log(name+" "+e);
            if ( name == 'cancel' ) {
                const config = {
                    header: "Project ",
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
                    text: "Confirm project creation?",
                    buttons: ["no", "yes"],
                    buttonsAlignment: "center"
                };     
                dhx.confirm(config).then(function(answer){
                    if (answer) {
                        const send = form.send("mntProjWr.php?t=f&r="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                            console.log(data);
                            loadProjects();
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
        dhx.ajax.get("menuQy.php?t=per&p=mntProj").then(function (data) {
            console.log(data);
            var obj = JSON.parse(data);
            console.log(obj);
            canRead = obj.canRead;
            canCreate = obj.canCreate; 
            canModify = obj.canModify;             
            canDelete = obj.canDelete;   
            tbInfo.data.load("toolbarsQy.php?t=prj_e").then(function(){
                tbInfo.disable(['edProj','edDate','edFina']);
                if ( canModify == 1 || canCreate == 1 ) {
                    tbInfo.enable(['edProj','edDate','edFina']);
                }
            });
            loadProjects();          
        }).catch(function (err) {
            console.log(err);
        });
    };

</script>