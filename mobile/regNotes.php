<?php 

include( '../include.php' );
include( "../logedCheck.php" );

require_once('header.php');

?>

<div id="layout" style="height: 100vh;"></div>


<?php
require_once('layout.php');
?>

<script>

var pLayout;
var tbStaff, pGridStaff;
var dsNotes, notesGrid, wSelNote, cbNoteType;

loadPermission();
dsNotes = new dhx.DataCollection();

pLayout = new dhx.Layout(null, {
    type: "line",
    rows: [
        { type: "line",
            rows: [ 
                { id: "lTbStaff", html: "", height: "55px" },
                { id: "staff", html: "" },
            ]
        },
        { id: "showNotes", html: "", height: "270px"  },
    ]
});
mainLayout.getCell("workplace").attach(pLayout);

dsStaff = new dhx.DataCollection();
loadUsers();

function loadUsers() {
    dsStaff.removeAll();
    dsStaff.load("readDB.php?t=staff").then(function(){
    //      console.log("done users read");
    });
}
tbStaff = new dhx.Toolbar(null, {
    css: "dhx_widget--bordered"
});
tbStaff.data.load("toolbarsQy.php?t=nt_search").then(function(){
    tbStaff.disable(['add','delete']);
});
tbStaff.events.on("input", function (event, arguments) {
    console.log(arguments);
    const value = arguments.toString().toLowerCase();
    pGridStaff.data.filter(obj => {
        return Object.values(obj).some(item => 
            item.toString().toLowerCase().includes(value)
        );
    });
});
tbStaff.events.on("click", function(id,e){
    console.log(id);
//    if ( id == 'edit' ) { addEditNote(); }
    if ( id == 'add' )  { wSelNote = 0; addEditNote(); }
});
pLayout.getCell("lTbStaff").attach(tbStaff);

pGridStaff = new dhx.Grid(null, {
    columns: [
        { width: 30, id: "Id", header: [{ text: "Id" }], autoWidth: true, hidden: true },
        { width: 50, id: "staffNumber", header: [{ text: "#" }], autoWidth: true },
        { width: 0, minWidth: 150, id: "staffName", header: [{ text: "Name" }], autoWidth: true, align: "left" },
    ],
    selection:"row",
    adjust: "true", 
    data: dsStaff
});
pGridStaff.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    tbStaff.enable(['add']);
    wSelected = row.Id;
    loadNotes();

});

pLayout.getCell("staff").attach(pGridStaff);

notesGrid = new dhx.Grid(null, {
    columns: [
        { width: 0, minWidth: 100, id: "noteId", header: [{ text: "Id" }], autoWidth: true, hidden: true, align: "left" },
        { width: 40, id: "noteIcon", header: [{ text: "T." }], autoWidth: true, htmlEnable: true, align: "center" },
        { width: 0, id: "noteText", header: [{ text: "Text" }], autoWidth: true },
        { width: 125, id: "notedate", header: [{ text: "Date" }], autoWidth: true },
    ],
    selection:"row",
    adjust: "true", 
    data: dsNotes
});
notesGrid.events.on("cellClick", function(row,column){
    console.log(row.Id+" - "+column.id);

    wSelNote = row.noteId;
    tbStaff.enable(['delete']);

    dhx.ajax.get("../mntStaffQy.php?t=notes&r="+row.Id).then(function (data) {
        obj = JSON.parse(data);
    }).catch(function (err) {
        console.log(err);
    });
});
function loadNotes() {
    dsNotes.removeAll();
    dsNotes.load("../mntStaffQy.php?t=notes&r="+wSelected).then(function(){
        console.log("done notes read");
    });
}

pLayout.getCell("showNotes").attach(notesGrid);

function addEditNote(argument) {
    const dhxWindow = new dhx.Window({
        width: 400,
        height: 360,
        closable: true,
        movable: true,
        modal: true,
        title: "Note registration"
    });
    const form = new dhx.Form(null, {
        css: "dhx_widget--bordered",
        padding: 10,
        width: 380,
        rows: [
            { type: "input", name: "Id", required: false, label: "Id", labelPosition: "left", labelWidth: "10px", hidden: true },
            { id: "noteTypeId", name: "noteTypeId", type: "combo", label: "Type", labelWidth: "50px", width: "320px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
            { id: "noteText", name: "noteText", type: "input", label: "Text", labelWidth: "50px", width: "320px",  labelPosition: "left", maxlength: "200", gravity: "false", readOnly: false },
            { type: "datepicker", name: "date", label: "Date", labelPosition: "left", labelWidth: "50px", width: "250px", dateFormat: "%Y-%m-%d %H:%i", timePicker: true, timeFormat: 24 },
            {
                align: "end",
                cols: [
                    { type: "button", name: "cancel", view: "link", text: "Cancel", },
                    { type: "button", name: "send", view: "flat", text: "Save", submit: true, },
                ]
            }
        ]
    });
    cbNoteType = form.getItem("noteTypeId").getWidget();
    cbNoteType.data.load("../commonQy.php?t=tnote").then(function(){
//        if ( wSelTime != 0 ) {
//            dhx.ajax.get("../addTimeHrQy.php?t=r&id="+wSelTime).then(function (data) {
//                console.log(data);
//                var obj = JSON.parse(data);
//                console.log(obj);
//                form.setValue(obj);
//                cbNoteType.setValue(obj.projId);
//            }).catch(function (err) {
//                    console.log(err);
//            });
//        }
    });
    form.events.on("click", function(name,e){
        console.log(name+" "+e);
        if ( name == 'cancel' ) {
            const config = {
                header: "Time registration ",
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
                header: "Note registration ",
                text: "Confirm note registration?",
                buttons: ["no", "yes"],
                buttonsAlignment: "center"
            };     
            dhx.confirm(config).then(function(answer){
                if (answer) {
                    const send = form.send("writeDB.php?t=wnote&s="+wSelected, "POST").then(function(data){
//                            message = JSON.parse(data);
                        console.log(data);
                        loadNotes();
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
    dhx.ajax.get("../menuQy.php?t=perm&p=regTimes").then(function (data) {
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
