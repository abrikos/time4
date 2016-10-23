$('#add-reserve').click(function() {
	var form = $(this).parent().siblings('.modal-body')
	var name = form.find('[name=name]')
	var phone = form.find('[name=phone]')
	var time = form.find('[name=time]')
	var validate = true
	if (!name.val()) {
		name.parent().addClass('has-error')
		validate = false
	}
	if (!phone.val()) {
		phone.parent().addClass('has-error')
		validate = false
	}
	if (validate) {
		$.get('site/add-reserve',
			{
				'name': name.val(),
				'phone': phone.val(),
				'time': time.val(),
			},
			function(result) {
				if (result) {
					$('#reserve-modal').modal('hide')
					reloadReserve()
				}
			})
	}
})

$('#reserve-modal').on('shown.bs.modal', function () {
	$(this).find('[name=name]').focus()
})

$('#reserve-modal').on('hidden.bs.modal', function () {
	$(this).find('[name=name]').val('')
	$(this).find('[name=phone]').val('')
})

function reloadReserve() {
	$.get('site/get-reserve', null,
		function(stack) {
			var table = $('.reserve-table').find('tbody')
			table.empty()
			$.each(stack, function(i, reserve) {
				table.append(
					$('<tr>').data('reserve-id', reserve.id).append(
						$('<td>', {width: 40}).append(
							$('<button/>')
								.bind('click', removeReserve)
								.addClass('btn btn-xs btn-default glyphicon glyphicon-ok remove-reserve')
						)
					).append(
						$('<td>', {width: 90}).text(reserve.time)
					).append(
						$('<td>').attr('title', reserve.text).text(reserve.text)
					).fadeIn('fast')
					)
			})
		}, 'JSON')
}

$('.remove-reserve').click(removeReserve)

function removeReserve() {
	var row = $(this).closest('tr')
	var reserveID = row.data('reserve-id')
	$.get('site/remove-reserve',
		{'id': reserveID},
		function(result) {
			if (result) {
				row.fadeOut('fast', function() {
					$(this).remove()
				})
			}
		})
}