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
		.dhx_layout-cell-content {
			height: 100%;
			position:relative;
		}
		.dhx_layout-cell-header + .dhx_layout-cell-content {
			height: calc(100% - 37px);
		}
</style>
<script>
		window.addEventListener('DOMContentLoaded', function (event) {
			scheduler.config.multi_day = true;
			scheduler.config.readonly = true;
			scheduler.config.tooltip_offset_x = 30;
			
			scheduler.plugins({
			    tooltip: true
			});
			scheduler.templates.tooltip_text = function(start,end,event) {
			    return "<b>Staff:</b> "+event.text+"<br/><b>Start date:</b> "+
			    scheduler.templates.tooltip_date_format(start)+"<br/>"+
			    "<b>End date:</b> "+scheduler.templates.tooltip_date_format(end)+"<br/>"+"<b>Project:</b> "+event.details;
			};			

			scheduler.attachEvent("onSchedulerReady", function () {
				requestAnimationFrame(function(){
					scheduler.setCurrentView(new Date(), "week");
					scheduler.load("getCalData.php?t=stf");
				});
				
			});

			mainLayout.getCell("workplace").attach(scheduler);
		});
</script>