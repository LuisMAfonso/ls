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
	
	const scheduler = new eventCalendar.EventCalendar("#root", {
		events: getData(),
		config: {
			viewControl: "toggle",
			readonly: true,
		},
		templates: {
			popup: ({ event, calendar }) => {
			            const start_date = format(event.start_date, "MMM, do h:mm");
			            const end_date = format(event.end_date, "MMM, do h:mm");
			            return `
			                <div className="popup_wrapper">
			                    <h2>${event.text}</h2>
			                    <div className="popup_info">
			                        <span><b>Description:</b></span>
			                        <span>${event.details}</span><br>
			                        <div><span><b>Date:</b></span>
			                        ${start_date} - ${end_date}</div>
			                    </div>
			                </div>`;
			        },		
		},
		date: new Date(2022, 5, 10),
	});

	let format = (date) => {
	  const year = date.getFullYear();
	  const month = String(date.getMonth() + 1).padStart(2, '0');
	  const day = String(date.getDate()).padStart(2, '0');
	  const localDate = `${year}-${month}-${day}`;
	  return localDate;
	};
</script>