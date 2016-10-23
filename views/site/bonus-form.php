<div class="row bg-warning {% if (o.form_hide) { %} collapse {% } %}" id="control-no-bonus">
    <input type="hidden" id="haircut-id" value="{%=o.id%}"/>
    <div class="col-md-3">
        <b>Карта:</b>
        <input class="form-control input-sm" placeholder="Введите часть номера карты" id="card-number" list="card_list" value="{%=o.card%}" oninput="cardChange({%=o.id%}, this.value)" />
        Бонусов: <span id="card-bonus-input" >{%=o.card_bonus%}</span>
    </div>
    <div class="col-md-3 {% if (o.card) { %} {% } else { %}collapse{% } %}" id="bonus-add">
        <button onclick="bonusAdd({%=o.id%})">Оформить бонус</button>
    </div>
    <div id="discount-add" class="col-md-3 {% if (o.card_bonus>100) { %} {% } else { %}collapse{% } %}">
        <input class="form-control input-sm" id="card-bonus-discount" placeholder="Введите сумму бонуса"/>
        <button id="card-submit" onclick="discountAdd({%=o.id%})" class="btn btn-xs btn-success">Вычесть из стоимости</button>
        от 100 до <span id="card-discount-max" >{%=o.card_bonus%}</span>
    </div>


</div>
<div id="card-info" class="{% if (!o.form_hide) { %} collapse {% } %} bg-success">
    Номер карты: <b>{%=o.card%}</b> (бонусов на карте <span id="card-bonus" >{%=o.card_bonus%}</span>)

    <br/>
    Стоимость услуги: <span >{%=o.original_price%}</span> руб.

    {% if (o.price!=o.original_price) { %}
    <br/> Оплачено бонусами {%=o.original_price-o.price%}
    <br/> К оплате с учетом бонусов: <b>{%=o.price%}</b> руб.
    {% } %}

    {% if (o.haircut_bonus) { %}
    <br/>
    Платеж добавил <span id="haircut-bonus">{%=o.haircut_bonus%}</span> бонусов
    {% } %}
</div>

<div id="card-status"></div>