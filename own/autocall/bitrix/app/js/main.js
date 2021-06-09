$(document).ready(function () {
    const maxQuantityRequest = 10;
    const quantityItem = $('#quantity-request-title');


    quantityChek(quantityItem.val(), maxQuantityRequest);

    function quantityChek(qRequest, maxRequest,) {
        if (qRequest <= maxRequest) {
            $('.alert-danger').remove();
            for (let i = 1; i <= qRequest; i++) {
                $('<div class="col-sm-6 quantity-attempts"><div class="form-group row"><label for="quantity-'+i+'" class="col-sm-1 col-form-label col-form-label-sm">' + i + '</label><div class="col-sm-3"><input type="time" class="form-control form-control-sm" id="quantity-'+i+'" value="00:01"></div></div></div>').appendTo('#clone-here');
            }
        } else {
            $('<div class="alert alert-danger" role="alert">Максимальное количество попыток дозвона до клиента ' + maxQuantityRequest + ' раз</div>').appendTo('#clone-here')
        }
    }

    $('#time-auto-call-title').on('input', function () {
        this.value = this.value.replace(/[^0-9]/, '')
    })

    $('.chek-number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/, '')
    })

    quantityItem.on('input', function (e) {
        this.value = this.value.replace(/[^0-9]/, '')
        $('.alert-danger').remove();
        $('.quantity-attempts').remove();
        let quantityChange = $(this).val()
        quantityChek(quantityChange, maxQuantityRequest);
    })

    $('#using-stores').change(function () {
        if (this.checked) {
            $(this).parent('.col-sm-6').find('.store-funnels-block-list').addClass('active')
            $(this).attr('data-settings', 'true')
        } else {
            $(this).parent('.col-sm-6').find('.store-funnels-block-list').removeClass('active')
            $('.store-funnels-block-list').find('li').each(function (e) {
                $(this).find('input').val('0')
            })
            $(this).attr('data-settings', 'false')
        }
    })

    $('#number-funnels').change(function () {
        if (this.checked) {
            $(this).parent('.col-sm-6').find('.number-funnels-block-list').addClass('active')
            $(this).attr('data-settings', 'true')
        } else {
            $(this).parent('.col-sm-6').find('.number-funnels-block-list').removeClass('active')
            $('.number-funnels-block-list').find('li').each(function (e) {
                $(this).find('input').val('0')
            })
            $(this).attr('data-settings', 'false')
        }
    })
    $('#user-chek').change(function () {
        if (this.checked) {
            $(this).parent('.col-sm-12').parent('.row').find('.user-table').addClass('active')
            $(this).attr('data-settings', 'true')
        } else {
            $(this).parent('.col-sm-12').parent('.row').find('.user-table').removeClass('active')
            $('.js-table').find('tr').each(function () {
                $(this).find('.user-input').val(' ')
            })
            $(this).attr('data-settings', 'false')
        }
    })
    $('.user-input').on('focusout',function () {
        Item.upInput()
    })

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