/**
 * This function triggers a specific highlight on a target
 * 
 * @param {string} targetId
 * @returns undefined
 */
function scrollToTarget(targetId) {
	$('html, body').animate({
		scrollTop : $(targetId).offset().top
	}, '400'); // Attention, parfois le # est contenu dans le target id
	$(targetId).effect({
		"effect" : "highlight",
		"duration" : 5000,
		"color" : "#428bca"
	});
}

$(document).on(
		'click',
		'.internal-link',
		function(e) {
			e.preventDefault();

			// Generate a correct string for a link
			var targetType = $(this).attr('tar');
			var targetId = $(this).attr('ref');
			if (!targetId.startsWith("#")) {
				targetId = "#" + targetId;
			}
			// If a modal is displayed, first close it
			$(".modal").modal("hide");

			// If the target belongs to a tab: display the correct tab
			var tabId = $(targetId).closest(".tab-pane").attr('id');
			$("a[href='#" + tabId + "'").tab('show');

			// specific case: dynamic generation of the data
			if (targetType === "to-row-ms") {
				$("a[href='#manuscripts']").tab("show");
				var table = $("#ms-table");
			}

			if (targetType === "to-row-work") {
				$("a[href='#works']").tab("show");
				var table = $("#work-table");
			}

			// If the target is in a collapse, the correct collapse shows
			$(targetId).closest(".collapse").collapse("show");

			// If the target is a work related to a tree-view display: show it
			if (targetType === 'to-work') {
				var node = $('#navigation-tree').treeview('findNodes',
						[ '^' + targetId + '$', 'href' ])[0];
				// ici faire un tableau avec d'un côté les parts et de l'autre
				// les id des targets.
				$('#navigation-tree').treeview('selectNode', node);

			}

			if (targetType.startsWith('to-row')) {
				thatdataTable = table.DataTable();
				var regex = '^\\s'+targetId+'\\s*$';
				var regex = '^'+targetId+'$';
				thatdataTable.column({id:name}).search(regex, true, false).draw();
				return true;
			}
			// Display the target with coloration
			setTimeout(function() {
				scrollToTarget(targetId);
			}, 300);

			return true;
		});