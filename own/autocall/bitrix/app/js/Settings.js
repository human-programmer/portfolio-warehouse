$(document).ready(function() {
    const integrationInfo = $('input[type=hidden]');
    const urlSave = 'https://smart.pragma.by/api/own/lirax/pip.php';
    const urlSattings = 'https://smart-dev.pragma.by/api/own/autocall/pragma/settings.php';
    const useStore = $('#using-stores');
    const numberFunnels = $('#number-funnels');
    const usePriory = $('#user-chek');
    const quantityItem = $('#quantity-request-title');
    const maxQuantityRequest = 10;

    getSettings(integrationInfo.attr('id'), integrationInfo.attr('name'));
    getLiraxSettings(integrationInfo.attr('id'), integrationInfo.attr('name'));

    async function getSettings(account_id, referrer) {

        let result = await $.ajax({
            url: urlSave,
            method: 'post',
            data: {
                flag: 'get',
                account_id: account_id,
                referrer: referrer
            },
        })
        const parseJsonRes = JSON.parse(result)

        parseJsonRes.pipelines.forEach(function (item) {
            $('.table-section').find('table').find('tbody').find('tr').each(function () {
                let allInput = $(this).find('td').find('input').attr('id');
                let statusInput = $(this).find('td').find('input')

                if (item == allInput) {
                    statusInput.attr('checked', true)
                }
            })
        })
    }

    async function getLiraxSettings(account_id, referrer) {
        let result = await $.ajax({
            url: urlSattings,
            method: 'post',
            data: {
                flag: 'get_settings',
                typeCRM: 'bitrix',
                ID_ACCOUNT: account_id,
                referrer: referrer
            },
        })
        const parseJsonRes = JSON.parse(result)

        parseJsonRes.data.forEach(function (item){
            $('#key-lirax').val(item.token)

            if(!isNaN(item.use_responsible)) {
                $('#time-auto-call-title').val(item.use_responsible)
            }

            if(item.use_store === 'true'){
                useStore.attr('checked', true)
                useStore.attr('data-settings', 'true')
                $('.store-funnels-block-list').addClass('active')
            }

            if (item.use_number === 'true') {
                numberFunnels.attr('checked', true)
                numberFunnels.attr('data-settings', 'true')
                $('.number-funnels-block-list').addClass('active')
            }

            if (item.use_priory === 'true') {
                usePriory.attr('checked', true)
                usePriory.attr('data-settings', 'true')
                $('.user-table').addClass('active')
            }
        })

        parseJsonRes.pipelines.forEach(function (item, index) {
            $('.store-funnels-block-item').remove();

            $('<li class="funnels-block-item"><div class="form-group row"><label class="col-sm-6 col-form-label col-form-label-sm" for="stores-input-funnels-'+index+'" id="'+item.id_pipeline+'">'+item.name_pipeline+'</label><div class="col-sm-3"><input class="form-control form-control-sm chek-number" type="number" id="stores-input-funnels-'+index+'" value="'+item.id_set_pep+'"></div></div></li>\';').appendTo('.store-funnels-block-list');

        })

        parseJsonRes.numbers.forEach(function (item, index) {
            $('.number-funnels-block-item').remove();

            $('<li class="funnels-block-item"><div class="form-group row"><label class="col-sm-6 col-form-label col-form-label-sm" for="number-input-funnels-'+index+'" id="'+item.id_pipeline+'">'+item.name_pipeline+'</label><div class="col-sm-3"><input class="form-control form-control-sm chek-number" type="number" id="number-input-funnels-'+index+'" value="'+item.number+'"></div></div></li>\';').appendTo('.number-funnels-block-list');

        })
        if (parseJsonRes.priority.length !== 0) {
        let obj = [];
        parseJsonRes.priority.forEach(function (item, index) {
            // console.log(item)
            $('.user-list').remove();
            index++
            let arTable = '<tr class="user-list"><td><span class="number-list" id="'+item.id+'">'+index+'</span></td>\n' +
                '                                            <td><span class="name-user">'+item.name+'</span></td>\n' +
                '                                            <td><span class="last-name-user">'+item.lastName+'</span></td>\n' +
                '                                            <td><input type="text" class="user-input" value="'+item.priory+'"></td>\n' +
                '                                        </tr>';
            obj.push(arTable)
        })
        $('.js-table').html(obj)
        }
        $('.user-input').on('focusout',function () {
            Item.upInput()
        })

        quantityItem.val(parseJsonRes.quantity.quantity)

        quantityChek(quantityItem.val(), maxQuantityRequest);

        if (parseJsonRes.quantity.quantity) {
            parseJsonRes.quantity.data_q.forEach(function (item) {
                $('#quantity-'+item.q).val(item.time)
            })
        }

        $('#work-time-title').val(parseJsonRes.quantity.work_start)
        $('#work-end-time').val(parseJsonRes.quantity.work_finish)

    }

    function quantityChek(qRequest, maxRequest,) {
        $('.quantity-attempts').remove()

        if (qRequest <= maxRequest) {
            $('.alert-danger').remove();
            for (let i = 1; i <= qRequest; i++) {
                $('<div class="col-sm-6 quantity-attempts"><div class="form-group row"><label for="quantity-'+i+'" class="col-sm-1 col-form-label col-form-label-sm">' + i + '</label><div class="col-sm-3"><input type="time" class="form-control form-control-sm" id="quantity-'+i+'" value="00:01"></div></div></div>').appendTo('#clone-here');
            }
        } else {
            $('<div class="alert alert-danger" role="alert">Максимальное количество попыток дозвона до клиента ' + maxQuantityRequest + ' раз</div>').appendTo('#clone-here')
        }
    }



    class Item {

        _$
        _$$INPUT

        constructor(item, index) {
            this._$ = Item._render(item, index)
            this._$$INPUT = this._$.find('.user-input')
            this.bind_actions()

        }

        bind_actions = () => {
            this.$$INPUT.on('focusout', () => {
                Item.upInput()
            })
        }

        get $() {
            return this._$
        }

        get $$INPUT() {
            return this._$$INPUT
        }

        static upInput = () => {
            const sortArray = Item.SORT()
            let obJ = [];
            sortArray.forEach(function (item, index) {
                const $str = new Item(item, index)
                obJ.push($str.$)
            })
            $('.js-table').html(obJ)
        }

        static  SORT = () => {
            let arrUser = []
            $('.user-table').find('table').find('tbody').find('tr').each(function () {
                let id = $(this).find('td').find('span').attr('id');
                let userName = $(this).find('td').find('.name-user').html();
                let userLastName = $(this).find('td').find('.last-name-user').html();
                let userPrioryVal = $(this).find('td').find('.user-input').val();
                userPrioryVal = userPrioryVal? userPrioryVal*1:0


                arrUser.push({id, userName, userLastName, userPrioryVal})

            })
            arrUser.sort(function (a, b) {
                if (a.userPrioryVal > b.userPrioryVal) {
                    return 1;
                }
                if (a.userPrioryVal < b.userPrioryVal) {
                    return -1;
                }
                return 0;
            });

            return arrUser.reverse();
        }


        static _render = (item, index) => {
            const item_table = '<tr class="user-list">' +
                '<td><span class="number-list" id="' + item.id + '">' + (index*1 + 1) + '</span></td>' +
                '<td><span class="name-user">' + item.userName + '</span></td>' +
                '<td><span class="last-name-user">' + item.userLastName + '</span></td>' +
                '<td><input type="text" class="user-input" value="' + item.userPrioryVal + '"></td>' +
                '</tr>'

            return $(item_table)
        }
    }
})