$(function () {
	$('.remove-master-from-stack').click(removeMasterFromStack)
})
$('#add-master-to-stack').click(function() {
	var master = $(this).parent().siblings('td:last').children('select')
	var masterID = master.val()
	var masterName = master.find(':selected').text()
	var table = $(this).closest('table')
	$.get('site/add-master-to-stack',
		{'id': masterID},
		function(result) {
			if (result) {
				master.find('option:selected').remove()
				table.find('tbody').append(
					$('<tr>').data('master-id', masterID).append(
						$('<td>').append(
							$('<button/>')
								.bind('click', removeMasterFromStack)
								.addClass('btn btn-sm btn-default glyphicon glyphicon-minus remove-master-from-stack')
						)
					).append(
						$('<td>').text(masterName)
					).fadeIn('fast')
				)
			}
			master.find('option:first').attr('selected', 'selected')
		})
})



function removeMasterFromStack() {
	var row = $(this).closest('tr')
	var masterID = row.data('master-id')
	$.get('site/remove-master-from-stack',
		{'id': masterID},
		function(result) {
			var select = row.closest('table').find('select')
			select.append($('<option>').attr('value', masterID).text(row.find('td:last-child').text()))
			row.fadeOut('fast', function() {
				$(this).remove()
			})
		})
}