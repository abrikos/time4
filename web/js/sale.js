$(function () {


$('#add-sale').click(function() {
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
		$.get('site/add-sale',
			{
				'name': name.val(),
				'amount': amount.val(),
			},
			function(result) {
				if (result) {
					$('#sale-modal').modal('hide')
					reloadSale()
				}
			})
	}
})

$('#sale-modal').on('shown.bs.modal', function () {
	$(this).find('[name=name]').focus()
})

$('#sale-modal').on('hidden.bs.modal', function () {
	$(this).find('[name=name]').val('')
	$(this).find('[name=amount]').val('')
})
	$('.remove-sale').click(removeSale)
});

function reloadSale() {
	$.get('site/get-sale', null,
		function(stack) {
			var table = $('.sale-table').find('tbody')
			table.empty()
			$.each(stack, function(i, sale) {
				table.append(
					$('<tr>').data('sale-id', sale.id).append(
						$('<td>').text(sale.name)
					).append(
						$('<td>', {width: 40}).text(sale.amount)
					).append(
						$('<td>', {width: 20}).append(
							$('<button/>')
								.bind('click', removeSale)
								.addClass('btn btn-xs btn-warning glyphicon glyphicon-remove remove-sale')
						)
					).fadeIn('fast')
				)
			})
		}, 'JSON')
}



function removeSale() {
	var row = $(this).closest('tr')
	var incomeID = row.data('sale-id')
	$.get('site/remove-sale',
		{'id': incomeID},
		function(result) {
			if (result) {
				row.fadeOut('fast', function() {
					$(this).remove()
				})
			}
		})
}