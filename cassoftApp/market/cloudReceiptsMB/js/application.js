// our application constructor
function application() {

}

application.prototype.getAppUrl = function() {
    var appUrl = 'https://cas.brokci.ru/pub/cassoftApp/brokci/';
    return appUrl;
}

//функция перезагружает страницу, убрав из гет запроса параметр page
application.prototype.cancellSettings = function() {
    var url = location.href;
    newUrl = url.replace(/page=settingsPage/, '');
    location.href = newUrl;
}

//функция записывыает настройки в хайблок
application.prototype.saveSettings = function() {
    var curapp = this;

    //анимация на кнопку
    $('#save-btn').find('i').removeClass('fa-check').addClass('fa-spinner').addClass('fa-spin');
    //спрятать кнопку Отмена
    $('#cancel-btn').hide();


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

        //передача настроек в хайблок
        if (message.length == 0 || isIgnoreMessage) {
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
                    if (response['result'] == 1) { //если настройки сохранены, перезагрузить страницу
                        alert('Настройки сохранены');
                        curapp.cancellSettings();
                    } else {
                        alert('Ошибка. ' + response['mesage']);
                        location.href = location.href;
                    }
                },
                error: function(html) {
                    alert('Ошибка ajax запроса при сохранении настроек');
                    location.href = location.href;
                },
            });
        } else {
            $('#save-btn').find('i').addClass('fa-check').removeClass('fa-spinner').removeClass('fa-spin');
            $('#cancel-btn').show();
        }
    });
}

application.prototype.resizeFrame = function() {
    var currentSize = BX24.getScrollSize();
    minHeight = currentSize.scrollHeight;
    if (minHeight < 400) minHeight = 400;
    BX24.resizeWindow(this.FrameWidth, minHeight);
}

application.prototype.resizeFrameNew = function(height) {
    BX24.resizeWindow(this.FrameWidth, height);
}

application.prototype.saveFrameWidth = function() {
    this.FrameWidth = document.getElementById("app").offsetWidth;
}

application.prototype.writeObjectIdToDeal = function(dealId, objectId, objectTitle) {
    BX24.callMethod(
        "crm.deal.update", {
            id: dealId,
            fields: {
                "UF_CRM_CS_DEAL_OBJECT_ID": objectId,
                "UF_CRM_CS_DEAL_OBJECT_TITLE": objectTitle
            },
            params: { "REGISTER_SONET_EVENT": "N" }
        },
        function(result) {
            if (result.error()) {
                console.log('ошибка записи id объекта в сделку');
                console.error(result.error());
            } else {
                console.log('объект записан в сделку');
            }
        }
    );
}

//функция ищет в каталоге все возможные значения для свойства "Стадия сделки"
//$sectionId - id раздела товарного каталога
//$propertyName- имя свойства "Стадия сделки" (например PROPERTY_232)
application.prototype.runGetDealStageNames = function(sectionId, propertyName) {
    var curapp = this;
    var arStageNames = [];
    var arStageNamesNew = ['foo'];

    //запуск функции getDealStageNames (ответа от нее ждать бесмысленно, так как она асинхронна)
    curapp.getDealStageNames(arStageNames, arStageNamesNew, propertyName);
}

application.prototype.getDealStageNames = function(arStageNames, arStageNamesNew, propertyName) {
    var curapp = this;

    //будем приравнивать новый массив старому, пока старый не перестанет расти
    arStageNamesNew = arStageNames.slice();

    var propertyNameNot = '!' + propertyName; //прибавим к названию свойства восклицательный знак
    var filter = {};
    filter[propertyName] = '%'; //не получать объекты с незаполненным свойством "Стадия сделки"
    filter[propertyNameNot] = arStageNames; //не получать объекты с уже найденными Стадиями

    BX24.callMethod(
        "crm.product.list", {
            order: { "NAME": "ASC" },
            filter: filter,
            select: [propertyName]
        },
        function(result) {
            // console.dir("Одна ИТЕРАЦИЯ");
            if (result.error())
                console.error(result.error());
            else {
                var products = result.data();
                for (key in products) {
                    if (products[key][propertyName]) {
                        var stageName = products[key][propertyName]["value"]
                            //проверка, содержится ли в массиве эта стадия
                        if (!curapp.inArray(stageName, arStageNames)) {
                            arStageNames.push(stageName);
                        }
                    }
                }
                //если массив arStageNames вырос и не равен массиву arStageNamesNew, то запустим новый цикл рекурсии
                if (arStageNames.length != arStageNamesNew.length) {
                    arStageNames = curapp.getDealStageNames(arStageNames, arStageNamesNew, propertyName)
                } else {
                    console.dir("Победа");
                    console.dir(arStageNames);
                    //подстановка названий стадий в select
                    var html = '';
                    arStageNames.forEach(function(value, key, arr) {
                        //обрезка длинных названий
                        var sliced = value.slice(0, 30);
                        if (sliced.length < value.length) {
                            sliced += '...';
                        }
                        html += `<option value='${value}' >${sliced}</option>`;
                    })
                    $(".selectpicker-stage-deal").append(html);
                    $(".selectpicker-stage-deal").selectpicker('refresh');
                    // console.dir(html);
                }
            }
        }
    );
}

//функция проверяет есть ли заданное значение в заданном массиве
application.prototype.inArray = function(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false
}


// create our application
app = new application();