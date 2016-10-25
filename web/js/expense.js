$(function () {
	$('#add-expense').click(function() {
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
			$.get('site/add-expense',
				{
					'name': name.val(),
					'amount': amount.val(),
				},
				function(result) {
					if (result) {
						$('#expense-modal').modal('hide')
						reloadExpense()
					}
				})
		}
	})

	$('#expense-modal').on('shown.bs.modal', function () {
		$(this).find('[name=name]').focus()
	})

	$('#expense-modal').on('hidden.bs.modal', function () {
		$(this).find('[name=name]').val('')
		$(this).find('[name=amount]').val('')
	});
	$('.remove-expense').click(removeExpense)

});

function reloadExpense() {
	$.get('site/get-expense', null,
		function(stack) {
			var table = $('.expense-table').find('tbody')
			table.empty()
			$.each(stack, function(i, expense) {
				table.append(
					$('<tr>').data('expense-id', expense.id).append(
						$('<td>').text(expense.name)
					).append(
						$('<td>', {width: 40}).text(expense.amount)
					).append(
						$('<td>', {width: 20}).append(
							$('<button/>')
								.bind('click', removeExpense)
								.addClass('btn btn-xs btn-warning glyphicon glyphicon-remove remove-expense')
						)
					).fadeIn('fast')
				)
			})
		}, 'JSON')
}



function removeExpense() {
	var row = $(this).closest('tr')
	var expenseID = row.data('expense-id')
	$.get('site/remove-expense',
		{'id': expenseID},
		function(result) {
			if (result) {
				row.fadeOut('fast', function() {
					$(this).remove()
				})
			}
		})
}