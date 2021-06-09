$(document).ready(function() {
    const integrationInfo = $('input[type=hidden]');
    const urlSave = 'https://smart.pragma.by/api/own/lirax/pip.php';
    const urlSattings = 'https://smart-dev.pragma.by/api/own/autocall/pragma/settings.php';
    const USE_STORE = $('#using-stores');
    const USE_NUMBER = $('#number-funnels');
    const USE_RESPONSIBLE = $('#time-auto-call-title');
    const USE_PRIORY = $('#user-chek');
    const TOKEN = $('#key-lirax');
    let ARRAY_PIPELINE = [];
    let ARRAY_NUM_PIP = [];
    const QUANTITY = $('#quantity-request-title');
    let data_q = [];
    const work_start = $('#work-time-title');
    const work_finish = $('#work-end-time');
    let ARRAY_PRIORY = [];


    $('.js-ajax-request').change(function () {
        let infoFunnels = $(this).parent('td').parent('tr').find('label');
        if(this.checked) {
            $.ajax({
                url: urlSave,
                method: 'post',
                dataType: 'json',
                data: {flag: 'save', CHECK: this.checked, id: infoFunnels.attr('id'), account_id: integrationInfo.attr('id'), referrer: integrationInfo.attr('name')},
            })
        } else {
            $.ajax({
                url: urlSave,
                method: 'post',
                data: {flag: 'save', CHECK: this.checked, id: infoFunnels.attr('id'), account_id: integrationInfo.attr('id'), referrer: integrationInfo.attr('name')},
            })
        }
    })

    $('.js-submit').on('click', function (){
        ARRAY_PIPELINE = [];
        ARRAY_NUM_PIP = [];
        data_q = [];
        ARRAY_PRIORY = [];
        $('.store-funnels-block-list').find('li').each(function (){
            let pip_name = $(this).find('label').html()
            let pip_set_id = $(this).find('input').val()
            let pip_id = $(this).find('label').attr('id')

            ARRAY_PIPELINE.push({pip_name, pip_set_id, pip_id})
        })

        $('.number-funnels-block-list').find('li').each(function (){
            let name_pipeline = $(this).find('label').html()
            let number = $(this).find('input').val()
            let id_pipeline = $(this).find('label').attr('id')

            ARRAY_NUM_PIP.push({name_pipeline, number, id_pipeline})
        })

        $('#clone-here').find('.quantity-attempts').each(function () {
            let q = $(this).find('label').html();
            let time = $(this).find('input').val();
            let id = $(this).find('input').attr('id');

            data_q.push({q, time, id})
        })

        $('.js-table').find('tr').each(function () {
            let id = $(this).find('.number-list').attr('id');
            let priory = $(this).find('.user-input').val()
            let name = $(this).find('.name-user').html();
            let lastName =  $(this).find('.last-name-user').html()

            ARRAY_PRIORY.push({id, priory, name, lastName})

        })

        $.ajax({
            url: urlSattings,
            method: 'post',
            data: {
                flag: 'save_settings',
                typeCRM: 'bitrix',
                REFERRER: integrationInfo.attr('name'),
                ID_ACCOUNT: integrationInfo.attr('id'),
                USE_STORE: USE_STORE.attr('data-settings'),
                USE_NUMBER: USE_NUMBER.attr('data-settings'),
                USE_RESPONSIBLE: USE_RESPONSIBLE.val(),
                USE_PRIORY: USE_PRIORY.attr('data-settings'),
                TOKEN: TOKEN.val(),
                APPLICATION: "",
                ARRAY_PIPELINE: ARRAY_PIPELINE,
                ARRAY_NUM_PIP: ARRAY_NUM_PIP,
                QUANTITY:  {quantity: QUANTITY.val(), data_q: data_q, work_start: work_start.val(), work_finish: work_finish.val()},
                ARRAY_PRIORY: ARRAY_PRIORY

            },
            success: function (data) {
                if (data === '1') {
                    $('<div class="alert alert-success" role="alert">Настройки были сохранены<div class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></div></div>').appendTo('.auto-call-modal-content')
                } else {
                    $('<div class="alert alert-danger" role="alert">Настройки не были сохранены обратитесь в техподдержку<div class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></div></div>').appendTo('.auto-call-modal-content')
                    console.log(data)
                }
            }
        })
    })
})