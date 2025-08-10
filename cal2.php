<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>

<div id="layout" style="height: 100vh;"></div>
<?php

require_once('sidebar.php');

?>
		<link rel="stylesheet" href="codebase/event-calendar.css" />
		<link
			rel="stylesheet"
			href="https://cdn.dhtmlx.com/fonts/wxi/wx-icons.css"
		/>
		<script src="codebase/event-calendar.js"></script>

		<!-- Demo data -->
		<script src="data/data.js"></script>
		<link rel="stylesheet" href="css/demos.css" />

<style>
</style>
<script>

	//	mainLayout.getCell("workplace").attachHTML('<div id="root"></div>');

			const scheduler = new eventCalendar.EventCalendar("#root", {
				events: getData(),
				date: new Date(2022, 5, 10),
			});
			scheduler.toggleSidebar({ show: false });
			
</script>		
