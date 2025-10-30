
$(document).ready(function () {
    searchApp()

});
$('#remove25').on('click', function() {
    console.log('click');
    //  console.log(value, row, index);
    /* $table.bootstrapTable('remove', {
         field: 'id',
         values: [row.id]
     });
*/
});
function searchApp() {
       $(".btn-link-fa-blue").hide()
        var authParams = BX24.getAuth();
//console.log(authParams)

        let app = $('#apps').val()
        let UserAut = $('#UserAut').val()
   // console.log(UserAut)
        $.ajax({
            url: "/local/components/logistics/applications/ajax/search-tab.php",
            type: 'POST',
            data: {
                authParams: authParams,
                app: app,
                UserAut: UserAut,
            },
            dataType: "html",
            success: function (response) {

                $('#resTable').empty()
                $('#resTable').html(response)
                // $('#main').show()
                $(".btn-link-fa-blue").show()

                // спрятать ненужные колонки
                // найти все колонки
                let arColumns = $('#fresh-table').find('th');
                // перебрать найденные колонки
                arColumns.each(function(i, elem) {
                    hideColumn(elem);
                });
            },
            error: function (data) {
                console.log(data)
            },
        })
    }
    $('#create').on('click', function() {
        edit()
        console.log("creat")
    });
    function edit(id) {
        var authParams = BX24.getAuth();
        let app = $('#apps').val()
        let UserAut = $('#UserAut').val()
        $.ajax({
            url: "/local/components/logistics/applications/ajax/edit.php",
            type: 'POST',
            data: {
                authParams: authParams,
                app: app,
                UserAut: UserAut,
                id:id
            },
            dataType: "html",
            success: function (response) {
                console.log("creat2")
                    // console.log(response)
                $('#slider_card').empty()
                $('#slider_card').html(response)
                $('#slider_card').show()
                 $('#report').hide()
                // $('#main').show()

            },
            error: function (data) {
                console.log(data)
            },
        })
    }

    $(document).on('click', '#panel-close', function() {
        $('#slider_card').empty()
        $('#slider_card').hide()
        $("#report").show();
    });

    let arDefaultFields = [
       /* 'ID',*/
        'actions',
     /*   'UF_CS_NAME',*/
        'UF_CS_START_DATE',
        'UF_CS_OFFER_CASTOMER_VAT',
        'UF_CS_OFFER_CASTOMER_NOVAT',
        'UF_CS_OFFER_CASTOMER_CASH',
        'UF_CS_DOWNLOAD_ADDRESS',
        'UF_CS_UNLOADING_ADDRESS',
      //  'UF_CS_WEIGHT',
      //  'UF_CS_VOLUME'
    ];


    function hideColumn(element) {
      //  console.log(element);
        let fieldName = $(element).data('field');
       // console.log(fieldName);
        let needHide = true;
        // проверка, не содержится ли название поля в массиве полей показываемых по умолчанию
        arDefaultFields.forEach(function (value) {
                if (fieldName === value) {
                    needHide = false;
                }
            }
        );
        if (needHide) {
            // спрятать колонку
            $table.bootstrapTable('hideColumn', fieldName)
        }
    }

function addNew() {
        $(".table-form").attr('style', "display:block;")
    $(".table-add").attr('style', "display:none;")
    new Cleave($('#UF_CS_OFFER_CASTOMER_CASH'), {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        delimiter: ' '
    })

   new Cleave($('#UF_CS_OFFER_CASTOMER_NOVAT'), {
       numeral: true,
       numeralThousandsGroupStyle: 'thousand',
       delimiter: ' '
   })
   new Cleave($('#UF_CS_OFFER_CASTOMER_VAT'), {
       numeral: true,
       numeralThousandsGroupStyle: 'thousand',
       delimiter: ' '
   })

    suggestionsInit()
    console.log("addNew")
    $.datepicker.regional['ru'] = {
        closeText: 'Закрыть',
        prevText: 'Предыдущий',
        nextText: 'Следующий',
        currentText: 'Сегодня',
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        weekHeader: 'Не',
        dateFormat: 'dd.mm.yy',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
    $(function () {
        $("#datepickerOpen").datepicker();
        console.log("open")
    });
}

function deleteRecord(id) {
    let index = $("#"+id).data()
    console.log(index.index)
    BX24.callMethod('entity.item.update', {
        ENTITY: 'application',
        id: id,
        ACTIVE : 'N'
    },
        function (result) {
            console.log(result.data())
            if (result.error())
                console.error(result.error());
            else
                $table.bootstrapTable('remove', {
                    field: '$index',
                    values: index.index,
                })
            /*  $table.bootstrapTable('hideRow', {
                  index: index.index,
              })*/
        },
    );
}
function saveNew() {
    $(".table-form").attr('style', "display:none;")
    $(".table-add").attr('style', "display:block;")
    console.log("saveNew")
    let UF_CS_START_DATE = $("#datepickerOpen").val()
    let UF_CS_OFFER_CASTOMER_VAT = $("#UF_CS_OFFER_CASTOMER_VAT").val().split(' ').join('');
    let UF_CS_OFFER_CASTOMER_NOVAT = $("#UF_CS_OFFER_CASTOMER_NOVAT").val().split(' ').join('');
    let UF_CS_OFFER_CASTOMER_CASH = $("#UF_CS_OFFER_CASTOMER_CASH").val().split(' ').join('');
    let UF_CS_DOWNLOAD_ADDRESS = $("#UF_CS_DOWNLOAD_ADDRESS").val()
    let UF_CS_UNLOADING_ADDRESS = $("#UF_CS_UNLOADING_ADDRESS").val()

    if(!UF_CS_DOWNLOAD_ADDRESS){
        console.log("UF_CS_DOWNLOAD_ADDRESS")
        alert('Адрес загрузки не выбран из списка');
        return
    }else{
        var dowloadAdd = JSON.parse(UF_CS_DOWNLOAD_ADDRESS)['value']
    }
    if(!UF_CS_UNLOADING_ADDRESS){
        console.log("UF_CS_UNLOADING_ADDRESS")
        alert('Адрес разгрузки не выбран из списка');
        return
    }else{
        var unloadAdd = JSON.parse(UF_CS_UNLOADING_ADDRESS)['value']
    }

    let UF_CS_LEAD = $("#UF_CS_LEAD").val() ? $("#UF_CS_LEAD").val():"";
    let UF_CS_DEAL = $("#UF_CS_DEAL").val()? $("#UF_CS_DEAL").val():"";
    let UF_CS_CONTACT = $("#UF_CS_CONTACT").val()? $("#UF_CS_CONTACT").val():"";
    let UF_CS_COMPANY = $("#UF_CS_COMPANY").val()? $("#UF_CS_COMPANY").val():"";
    let UF_CS_QUOTE = $("#UF_CS_QUOTE").val()? $("#UF_CS_QUOTE").val():"";
let name = "Доставка груза "+dowloadAdd+" - "+unloadAdd
    BX24.callMethod('entity.item.add', {
            ENTITY: 'cargo',
            NAME: name,
            PROPERTY_VALUES: {

                UF_CS_DOWNLOAD_ADDRESS: UF_CS_DOWNLOAD_ADDRESS,
                UF_CS_UNLOADING_ADDRESS: UF_CS_UNLOADING_ADDRESS,
            },
        },
        function (result) {
            if (result.error())
                console.error(result.error());
            else
console.log(result.data())
console.log(result)
                BX24.callMethod('entity.item.add', {
                        ENTITY: 'application',
                        NAME: name,
                        PROPERTY_VALUES: {
                            UF_CS_START_DATE: UF_CS_START_DATE,
                            UF_CS_OFFER_CASTOMER_VAT: UF_CS_OFFER_CASTOMER_VAT,
                            UF_CS_OFFER_CASTOMER_NOVAT: UF_CS_OFFER_CASTOMER_NOVAT,
                            UF_CS_OFFER_CASTOMER_CASH: UF_CS_OFFER_CASTOMER_CASH,
                            UF_CS_LEAD: UF_CS_LEAD,
                            UF_CS_DEAL: UF_CS_DEAL,
                            UF_CS_CONTACT: UF_CS_CONTACT,
                            UF_CS_COMPANY: UF_CS_COMPANY,
                            UF_CS_QUOTE: UF_CS_QUOTE,
                            UF_CS_LOADING: result.data(),
                        },
                    },
                        function (result) {
                            if (result.error())
                                console.error(result.error());
                            else    // $(".result").html("Запись создана");
                                //  setTimeout(resultMessage, 2000)
                                console.log(result.data())
                                    $table.bootstrapTable('insertRow', {
                                        index: 0,
                                        row: {
                                            actions: "<a href='javascript:edit(<?= json_encode($value[`ID`]) ?>)' title='Редактирование'><i class='fa fa-edit'></i></a> <a href='javascript:deleteRecord(<?= json_encode($value[`ID`])?>)' title='В архив'><i class='fa fa-remove'></i></a>",
                                            NAME : name,
                                            UF_CS_OFFER_CASTOMER_VAT: $("#UF_CS_OFFER_CASTOMER_VAT").val(),
                                            UF_CS_OFFER_CASTOMER_NOVAT: $("#UF_CS_OFFER_CASTOMER_NOVAT").val(),
                                            UF_CS_OFFER_CASTOMER_CASH: $("#UF_CS_OFFER_CASTOMER_CASH").val(),
                                            UF_CS_DOWNLOAD_ADDRESS: dowloadAdd,
                                            UF_CS_UNLOADING_ADDRESS: unloadAdd,
                                            UF_CS_START_DATE: UF_CS_START_DATE,
                                        }
                                    })
                        }
                );
         }
    );
}
    function saveLoadingSuggestion(suggestion) {
        $('#UF_CS_DOWNLOAD_ADDRESS').val(JSON.stringify(suggestion));
    }

    function saveUnloadingSuggestion(suggestion) {
        $('#UF_CS_UNLOADING_ADDRESS').val(JSON.stringify(suggestion));
    }
    function suggestionsInit(){
        console.log("dadata")
        let token = '37e84e13164831626cf935884808e3abc8477333';
        $.ajax({
            url: 'https://city.brokci.ru/local/components/logistics/applications/js/jquery.suggestions.min.js',
            dataType: 'script',
            success: function() {
                $('#LOADING_FULLADDRESS_TAB').suggestions({ // инпут с адресом загрузки
                    token: token,
                    type: 'ADDRESS',
                    noCache: true,
                    triggerSelectOnBlur: false,
                    onSelect: saveLoadingSuggestion,
                });
                $('#UNLOADING_FULLADDRESS_TAB').suggestions({ // инпут с адресом разгрузки
                    token: token,
                    type: 'ADDRESS',
                    noCache: true,
                    triggerSelectOnBlur: false,
                    onSelect: saveUnloadingSuggestion,
                });
            },
        });
    }

