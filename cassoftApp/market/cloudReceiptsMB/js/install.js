// our application constructor
function application() {

}

application.prototype.getAppUrl = function() {
    var appUrl = 'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/';
    return appUrl;
}

application.prototype.finishInstallation = function() {
    var curapp = this;

    //анимация на кнопку
    $('#save-btn').find('i').removeClass('fa-check').addClass('fa-spinner').addClass('fa-spin');

    BX24.init(function() {
        /*-------------- проверка заполнения формы ---------------*/
        var message = [];
        var settings = {};
        //проверка инпутов
        $('#form_settings input').each(function() {
            var element = $(this);
            settings[element.attr('name')] = element.val();
            if (!element.val()) {
                message.push('Не заполнено поле: ' + element.data('title'));
            }
        });
        //проверка селектов
        $('#form_settings select').each(function() {
            var element = $(this);
            settings[element.attr('name')] = element.val();
            if (!element.val()) {
                message.push('Не заполнено поле: ' + element.data('title'));
            }
        });

        //вывод предупреждения
        var isIgnoreMessage = false;
        if (message.length == 1) {
            message.push('Оставить поле незаполненным?');
            isIgnoreMessage = confirm(message.join('\n'));
        } else if (message.length > 1) {
            message.push('Оставить поля незаполненными?');
            isIgnoreMessage = confirm(message.join('\n'));
        }

        if (message.length == 0 || isIgnoreMessage) {
            curapp.setSettings(settings);
            /*
                            var handler = curapp.getAppUrl() + 'object.php';
                            var type = 'object';
                            //поиск существующих табов на странице детального просмотра сделки
                            BX24.callMethod(
                                "userfieldtype.list", {},
                                function(result) {
                                    console.log(result);

                                    var isPlacementExist = true;
                                    result.data().forEach(function(placement, key, arr) {
                                        if (placement["USER_TYPE_ID"] == 'object' && placement["HANDLER"] == handler) {
                                            isPlacementExist = false;
                                        }
                                    })
                                    if (isPlacementExist) {

                                        //создание поля объект  на странице детального просмотра сделки
                                        //    curapp.userFieldtypeAdd(type, handler);

                                    }

                                }
                            );
            */
        } else {
            $('#save-btn').find('i').addClass('fa-check').removeClass('fa-spinner').removeClass('fa-spin');
        }

    });
}

//создание таба на странице детального просмотра сделки
application.prototype.placementBind = function(handler, settings) {
    var curapp = this;

    BX24.callMethod(
        'placement.bind', {
            PLACEMENT: 'CRM_DEAL_DETAIL_TAB',
            HANDLER: handler,
            TITLE: 'Подбор (встречные заявки)',
            DESCRIPTION: 'cassoft.company'
        },
        function(res) {
            console.log(res);
            //сохранение настроек в хайблок
            curapp.setSettings(settings);
        }
    );
}

/*------------------------- установка поля Объект недвижимости ---------------*/

application.prototype.userFieldtypeAdd = function(type, handler) {
    var curapp = this;
    //  var fileLog = '/home/bitrix/ext_www/cas.brokci.ru/logs/installRealEstateObject/FiledType.txt';
    BX24.callMethod(
        'userfieldtype.add', {
            'USER_TYPE_ID': type,
            'HANDLER': handler,
            'TITLE': 'Объект недвижимости',
            'OPTIONS': {
                'height': 70,
            },
            'DESCRIPTION': 'Поле по созданию и редактированию объекта недвижимости '.type
        },
        function(res) {

            if (res['result']) {

                //  file_put_contents(fileLog, print_r(res, true), FILE_APPEND);
                console.log(res);
                //сохранение настроек в хайблок
                curapp.crmDealUserFieldAdd(type);
            } else {
                //  file_put_contents(fileLog, print_r(res, true), FILE_APPEND);
            }
        }
    );
}

application.prototype.crmDealUserFieldAdd = function(type) {
    var curapp = this;
    // var fileLog = '/home/bitrix/ext_www/cas.brokci.ru/logs/installRealEstateObject/FiledType' + date('Y-m-d') + '.txt';
    BX24.callMethod(
        'crm.deal.userfield.add', {
            'fields': {
                'USER_TYPE_ID': type,
                'FIELD_NAME': 'UF_CRM_CS_DEAL_OBJECT_BUT',
                'XML_ID': 'UF_CRM_CS_DEAL_OBJECT_BUT',
                'MANDATORY': 'N',
                'SHOW_IN_LIST': 'Y',
                'EDIT_IN_LIST': 'Y',
                'EDIT_FORM_LABEL': 'Объект недвижимости CS',
                'LIST_COLUMN_LABEL': 'Привязка объекта недвижимости',
                'SETTINGS': {}
            }
        },
        function(resultAdd) {
            console.log(resultAdd);

            file_put_contents(fileLog, print_r(resultAdd, true), FILE_APPEND);

        }
    );
}



//сохранение настроек в хайблок
application.prototype.setSettings = function(settings) {
    var curapp = this;

    //добавим в массив данных действие и данные для авторизации
    var authParams = BX24.getAuth();
    var appAction = { 'action': 'setSettings' };
    data = Object.assign({}, settings, authParams, appAction);
    $.ajax({
        url: curapp.getAppUrl() + 'ajax_settings.php',
        type: "POST",
        data: data,
        dataType: "text",
        success: function(responseJson) {
            var response = JSON.parse(responseJson);
            if (response['result'] == 1) {
                $('#save-btn').hide();
                console.log(response)
                    //если настройки сохранены, создать свойства товаров 
                curapp.addProductPropertyes();
            } else {
                alert('Ошибка. ' + response['mesage']);
            }
        },
        error: function(html) {
            alert('Ошибка ajax запроса при сохранении настроек');
        },
    });
}

application.prototype.resizeFrame = function() {

    var currentSize = BX24.getScrollSize();
    minHeight = currentSize.scrollHeight;

    if (minHeight < 400) minHeight = 400;
    BX24.resizeWindow(this.FrameWidth, minHeight);

}

application.prototype.saveFrameWidth = function() {
    this.FrameWidth = document.getElementById("app").offsetWidth;
}

//добавление свойств товара

application.prototype.addProductPropertyes = function() {
    var curapp = this;

    //получение параметров для авторизации
    var authParams = BX24.getAuth();
    var appAction = { 'action': 'addPropertyes' };
    data = Object.assign({}, authParams, appAction);

    $.ajax({
        url: curapp.getAppUrl() + 'ajax_install.php',
        type: "POST",
        data: data,
        dataType: "text",
        timeout: 2 * 60 * 1000, // установка 2 минутного тайм-аута
        success: function(responseJson) {
            console.log(responseJson)
            var response = JSON.parse(responseJson);
            // let response = responseJson;
            //   console.log(responseJson)
            //  console.log(response)
            if (response['result'] == 1) { //если настройки сохранены, окончить установку
                if (response['message']) {
                    alert(response['message']);
                }
                //  $('#save-btn').show();
                $('#install_result').show();
                $('#save-btn').find('i').addClass('fa-check').removeClass('fa-spinner').removeClass('fa-spin');
                BX24.installFinish();
            } else {
                alert('Ошибка. ' + response['message']);
            }

        },
        error: function(html) {
            console.log(html);
            alert('Ошибка ajax запроса при установки приложения');
            $('#save-btn').show();
        },
    });




}

// create our application
app = new application();
//include 'pages/closSetupPage.php';