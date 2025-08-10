<?php 

include( 'include.php' );
include( "logedCheck.php" );

require_once('header.php');

?>

<div id="layout" style="height: 100vh;"></div>

<?php

require_once('sidebar.php');

?>

<style>
		.dhx_cal_event{
			transition: opacity 0.1s;
			opacity: 0.7;
		}
		.dhx_cal_event .dhx_title{
			line-height: 12px;
		}
		.dhx_cal_event_line:hover,
		.dhx_cal_event:hover,
		.dhx_cal_event.selectedy,
		.dhx_cal_event.dhx_cal_select_menu{
			opacity: 1;
		}

		.event_math{
			--dhx-scheduler-event-background: #FF5722;
			--dhx-scheduler-event-border: 1px solid #732d16;
		}

		.event_science{
			--dhx-scheduler-event-background: #0FC4A7;
			--dhx-scheduler-event-border: 1px solid #698490;
		}

		.event_english{
			--dhx-scheduler-event-background: #684f8c;
			--dhx-scheduler-event-border: 1px solid #9575CD;
		}
</style>
<script>
		window.addEventListener('DOMContentLoaded', function (event) {
			scheduler.config.multi_day = true;
			scheduler.config.readonly = true;
			

			scheduler.attachEvent("onSchedulerReady", function () {
				requestAnimationFrame(function(){
					scheduler.setCurrentView(new Date(2025,2,25), "week");
					scheduler.load("getCalData.php?t=stf");
				});
				
			});

			mainLayout.getCell("workplace").attach(scheduler);
		});
</script>