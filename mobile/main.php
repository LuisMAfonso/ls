<?php 

include( '../include.php' );
include( "../logedCheck.php" );

require_once('header.php');

?>

<div id="layout" style="height: 100vh;"></div>
<style>
    .dvMenu_container {
      display: flex;
      flex-direction: column;
      justify-content: center;
  	  align-items: center;
      height: 65px;
    }
    .dvMenu_icon {
    	margin-top: 9px;
    }
    .dvMenu_text {
	   	margin-bottom: -3px;
    }
    .dvMenu_border {
		border: 1px solid #BBBBBB;
  	}

</style>

<?php
require_once('layout.php');
?>

<script>

    var dvMenu;

    dvMenu = new dhx.DataView(null, {
        itemsInRow: 2,
        gap: 20,
        template: dvMenuTamplate,
    });
    dvMenu.data.load("menuQy.php?t=menu").then(function () {
        dvMenu.data.map(function (item, i) {
            dvMenu.data.update(item.id, {css: "dvMenu_border", template: dvMenuTamplate,});
        });
    });
    function dvMenuTamplate(item) {
        let template = "<div class='dvMenu_container'>";
        template += "<div class='dvMenu_icon'>" + item.icon + "</div>";
        template += "<div class='dvMenu_text'>" + item.value + "</div>";
        template += "</div>";
        return template;
    }
    dvMenu.events.on("click", function(id, e){
        console.log(dvMenu.data.getItem(id).prog+" - "+e);
        prog = dvMenu.data.getItem(id).prog;
        window.open(prog,'_self');
    });    
    mainLayout.getCell("workplace").attach(dvMenu);

</script>
