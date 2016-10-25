$(function () {
	$('.prepayment').click(togglePrepayment)
	$('.penalty').click(togglePenalty)
})


function togglePrepayment() {
	var master = $('#shift-table').find('th').eq($(this).closest('td').index())
	if ($('.prepayment-input').length == 0) {
		$(this).parent().html(
			$('<input>', {class: 'form-control input-sm prepayment-input'})
				.data('master-id', master.data('master-id'))
				.val($(this).text())
				.focusout(dismissPrepayment)
				.keyup(function(e) { if (e.keyCode == 13) { $(this).blur() } })
		)
		$('input.prepayment-input').focus().select()
	}
}

function dismissPrepayment() {
	var input = $(this)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-master-prepayment', {'id': input.data('master-id'), 'value': value}, function(result) {
			if (result) {
				redrawTotalTable()
			} else {
				input.parent().addClass('has-error')
				input.focus()
			}
		}, 'JSON')
	} else {
		input.parent().addClass('has-error')
		input.focus()
	}
}



function togglePenalty() {
	var master = $('#shift-table').find('th').eq($(this).closest('td').index())
	if ($('.penalty-input').length == 0) {
		$(this).parent().html(
			$('<input>', {class: 'form-control input-sm penalty-input'})
				.data('master-id', master.data('master-id'))
				.val($(this).text())
				.focusout(dismissPenalty)
				.keyup(function(e) { if (e.keyCode == 13) { $(this).blur() } })
		)
		$('input.penalty-input').focus().select()
	}
}

function dismissPenalty() {
	var input = $(this)
	var value = input.val()
	if (value && $.isNumeric(value)) {
		input.parent().removeClass('has-error')
		$.get('site/update-master-penalty', {'id': input.data('master-id'), 'value': value}, function(result) {
			if (result) {
				redrawTotalTable()
			} else {
				input.parent().addClass('has-error')
				input.focus()
			}
		}, 'JSON')
	} else {
		input.parent().addClass('has-error')
		input.focus()
	}
}
