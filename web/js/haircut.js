
$('#haircut-modal').on('hidden.bs.modal', function() {
    var id = $(this).data('haircut-id')
    var note = $(this).find('input[name=note]').val()
    $.get('/site/update-haircut', {
        'id': id,
        'price': null,
        'note': note
    },function (haircut) {
        redrawTotalTable();

    },'JSON');
    $(this).find('.modal-body').html(
        $('<div>', {style: 'text-align: center'}).html(
            $('<img>', {src: '/images/ajax-loading.gif', alt: 'Загрузка...', width: '50px'})
        )
    )
});


function redrawBonus(haircut) {
    return ;
    if(haircut.bonus_id){
        $('#haircut-price-'+haircut.id).addClass('hasBonus').html( haircut.price*1>0 ? haircut.price:'Бонус'  );
    }else{
        $('#haircut-price-'+haircut.id).removeClass('hasBonus');
    }
}


function haircutDialog(id) {
    var modal = $('#haircut-modal');
    modal.modal();
    $.get('site/get-haircut', {'id': id}, function(result) {
        var modalbody = modal.find('.modal-body');
        modalbody.empty()
            .append(
                $('<input>', {class: 'form-control', placeholder: 'Пометки', name: 'note'}).val(result.note)
            )
            .append(tmpl("bonus-form", result))
            .append('<br>')
            .append(
                $('<table>', {class: 'table-condensed'}).append(
                    $('<tr>')
                        .append($('<th>', {width: '70%'}).text('Материал'))
                        .append($('<th>', {width: '20%'}).text('Цена'))
                        .append($('<th>', {width: '10%'}).text(''))
                )
            );

        $.each(result.materials, function(i, material) {
            $('<tr>')
                .data('material-id', material.id)
                .append($('<td>').text(material.name))
                .append($('<td>').text(material.price))
                .append($('<td>')
                    .append(
                        $('<i>', {class: 'btn btn-warning btn-xs glyphicon glyphicon-remove'})
                            .click(removeMaterial)
                    )
                )
                .appendTo(modal.find('table'))
        })
        $('<tr class="new-material">')
            .data('haircut-id', id)
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'name'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'price'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>')
                .append($('<i>', {class: 'btn btn-primary btn-xs glyphicon glyphicon-plus'}))
                .click(addMaterial)
            )
            .appendTo(modal.find('table'))
    }, 'JSON')
}



$('#haircut-modal2').on('show.bs.modal', function(e) {
    var id = $(e.relatedTarget).siblings('.editable').data('haircut-id')
    var modal = $(this)
    modal.data('haircut-id', id)
    $.get('site/get-haircut', {'id': id}, function(result) {
        var modalbody = modal.find('.modal-body');
        modalbody.empty()
            .append(
                $('<input>', {class: 'form-control', placeholder: 'Пометки', name: 'note'}).val(result.note)
            )
            .append(tmpl("bonus-form", result))
            .append('<br>')
            .append(
                $('<table>', {class: 'table-condensed'}).append(
                    $('<tr>')
                        .append($('<th>', {width: '70%'}).text('Материал'))
                        .append($('<th>', {width: '20%'}).text('Цена'))
                        .append($('<th>', {width: '10%'}).text(''))
                )
            );

        $.each(result.materials, function(i, material) {
            $('<tr>')
                .data('material-id', material.id)
                .append($('<td>').text(material.name))
                .append($('<td>').text(material.price))
                .append($('<td>')
                    .append(
                        $('<i>', {class: 'btn btn-warning btn-xs glyphicon glyphicon-remove'})
                            .click(removeMaterial)
                    )
                )
                .appendTo(modal.find('table'))
        })
        $('<tr class="new-material">')
            .data('haircut-id', id)
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'name'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>', {style: 'padding-right: 10px'}).append(
                $('<input>', {class: 'form-control', name: 'price'}))
                .focusin(function(e) { $(e.target).parent().removeClass('has-error') })
                .keyup(function(e) { $(e.target).parent().removeClass('has-error'); if (e.keyCode == 13) { addMaterial() } })
            )
            .append($('<td>')
                .append($('<i>', {class: 'btn btn-primary btn-xs glyphicon glyphicon-plus'}))
                .click(addMaterial)
            )
            .appendTo(modal.find('table'))
    }, 'JSON')
})

function restoreHaircutPriceInput(obj,e) {
     if (e.keyCode == 13) {
         var input = $(obj);
         $.getJSON('/haircut/change-price',{id:input.data('id'),price:input.val()},function (json) {
             input.blur()
             if(json.error){
                 alert(json.error);
             }
         })


     }
}


function bonusAdd(id) {
    $.getJSON('/haircut/bonus-add',{id:id},function (json) {
        $('#control-no-bonus').fadeOut();
        $('#card-info').fadeIn();
        $('#haircut-bonus').html(json.haircut_bonus);
        $('#card-bonus').html(json.card_bonus);
        $('#haircut-price-'+id).addClass('hasBonus');
    })
}

function discountAdd(id) {
    var sum=$('#card-bonus-discount').val();
    $.getJSON('/haircut/discount-add',{id:id,reduce:sum},function (json) {
        $('#card-bonus-sum').text(json.bonus);
        $('#card-status').html('<div class="text-'+json.status.class+'">'+json.status.message+'</div>');
        if(json.status.class=='success'){
            $('#control-no-bonus').fadeOut();
            $('#card-info').fadeIn();
            $('#haircut-bonus').html(json.bonus.price);
            $('#card-bonus').html(json.card.bonus);
            $('#haircut-price-'+id).addClass('hasDiscount');
            $('#haircut-discount-'+id).text(json.haircut.price - json.haircut.discount);
            $('#haircut-payment').text(json.haircut.price - json.haircut.discount);
        }
    })
}

function cardChange(id,cardnum) {
    $.getJSON('/haircut/card-change',{cardnum:cardnum,id:id},function (json) {
        if(json) {
            $('#card-bonus-input').html(json.bonus);
            $('#card-discount-max').html(json.bonus);
            $('#bonus-add').fadeIn();
            if(json.bonus>100){
                $('#discount-add').fadeIn();
            }else {
                $('#discount-add').fadeOut();
            }
        }
    })
}