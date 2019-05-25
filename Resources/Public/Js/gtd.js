$(function(){
	$(window).load( function() {
		var uploadFilesInputButtonText = $('#uploadFilesInput').attr('data-button-text');
		$('#uploadFilesInput').filestyle('buttonText',uploadFilesInputButtonText);
		$( ".dataDetailListTitle").draggable({ cursor: "crosshair", revert: "invalid"  });
		$( ".categoryNode" ).draggable({ cursor: "crosshair", revert: "invalid"  });
		$( ".categoryNode" ).droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						var html4move = "";
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								rootUrl = rootUrl  +"?L="+langId+"&tx_t3gtd_frontendgtd[action]=moveTask";
								html4move = rootUrl + '&tx_t3gtd_frontendgtd[srcTask]=' + draggableId + '&tx_t3gtd_frontendgtd[targetProject]=' + selfId+'&tx_t3gtd_frontendgtd[controller]=Project';
						} else {
								rootUrl = rootUrl +"?L="+langId+"&tx_t3gtd_frontendgtd[action]=moveProject";
								html4move = rootUrl + '&tx_t3gtd_frontendgtd[srcProject]=' + draggableId + '&tx_t3gtd_frontendgtd[targetProject]=' + selfId+'&tx_t3gtd_frontendgtd[controller]=Project';
						}
						window.location.replace(html4move);
				}
		});
		$( ".dataDetailListTitle" ).droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail") {
								rootUrl = rootUrl + "?L="+langId+"&tx_t3gtd_frontendgtd[action]=moveTaskOrder";
						} else if(draggableType == "dataDetailProject") {
								rootUrl = rootUrl +"?L="+langId+"&tx_t3gtd_frontendgtd[action]=moveTaskOrderInsideProject";
						}
						var html4move = rootUrl + '&tx_t3gtd_frontendgtd[srcTask]=' + draggableId + '&tx_t3gtd_frontendgtd[targetTask]=' + selfId+'&tx_t3gtd_frontendgtd[controller]=Task';
						window.location.replace(html4move);
				}
		});
		$("#focus_inbox").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToInbox&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_today").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToToday&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_next").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToNext&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_waiting").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToWaiting&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_someday").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToSomeday&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_completed").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = "" +ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToCompleted&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#focus_trash").droppable({
				drop: function( event, ui ) {
						var selfId = ("" + $( this ).attr("id")).split("_")[1];
						var draggableId = "" +ui.draggable.attr("id").split("_")[1];
						var langId = ""+ui.draggable.attr("id").split("_")[2];
						var draggableType = ""+ui.draggable.attr("id").split("_")[0];
						var rootUrl = $(location).attr('pathname');
						if(draggableType == "dataDetail" || draggableType == "dataDetailProject"){
								var html4move = rootUrl+"?L="+langId+"&tx_t3gtd_frontendgtd[task]="+draggableId+"&tx_t3gtd_frontendgtd[action]=moveToTrash&tx_t3gtd_frontendgtd[controller]=Task";
								window.location.replace(html4move);
						}
				}
		});
		$("#taskDueDate").datepicker({
				dateFormat: 'yy-mm-dd',
				constrainInput: false
		});
	});
};
