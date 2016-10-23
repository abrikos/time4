$('#add-income').click(function() {
	var form = $(this).parent().siblings('.modal-body')
	var name = form.find('[name=name]')
	var amount = form.find('[name=amount]')
	var validate = true
	if (!name.val()) {
		name.parent().addClass('has-error')
		validate = false
	}
	if (!amount.val() || !$.isNumeric(amount.val())) {
		amount.parent().addClass('has-error')
		validate = false
	}
	if (validate) {
		$.get('site/add-income',
			{
				'name': name.val(),
				'amount': amount.val(),
			},
			function(result) {
				if (result) {
					$('#income-modal').modal('hide')
					reloadIncome()
				}
			})
	}
})

$('#income-modal').on('shown.bs.modal', function () {
	$(this).find('[name=name]').focus()
})

$('#income-modal').on('hidden.bs.modal', function () {
	$(this).find('[name=name]').val('')
	$(this).find('[name=amount]').val('')
})

function reloadIncome() {
	$.get('site/get-income', null,
		function(stack) {
			var table = $('.income-table').find('tbody')
			table.empty()
			$.each(stack, function(i, income) {
				table.append(
					$('<tr>').data('income-id', income.id).append(
						$('<td>').text(income.name)
					).append(
						$('<td>', {width: 40}).text(income.amount)
					).append(
						$('<td>', {width: 20}).append(
							$('<button/>')
								.bind('click', removeIncome)
								.addClass('btn btn-xs btn-warning glyphicon glyphicon-remove remove-income')
						)
					).fadeIn('fast')
				)
			})
		}, 'JSON')
}

$('.remove-income').click(removeIncome)

function removeIncome() {
	var row = $(this).closest('tr')
	var incomeID = row.data('income-id')
	$.get('site/remove-income',
		{'id': incomeID},
		function(result) {
			if (result) {
				row.fadeOut('fast', function() {
					$(this).remove()
				})
			}
		})
}