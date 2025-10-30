var token = '37e84e13164831626cf935884808e3abc8477333';
Dropzone.autoDiscover = false;
$(document).ready(function() {

    $('.js-chosen').chosen({
        width: '100%',
        no_results_text: 'Совпадений не найдено',
        placeholder_text_single: 'Выберите значение'
    });

    setTimeout(function () {
        var headID = document.getElementsByTagName("head")[0];
        var newScript = document.createElement('script');
        newScript.type = 'text/javascript';
        newScript.src = '/local/components/event/contact_add/templates/.default/gallery.js';
        headID.appendChild(newScript);
    }, 2000);
    suggestionsInit();

});

$.mask.definitions['h'] = "[0|1|3|4|5|6|7|9]"
$(".mask-phone").mask("+7 (h99) 999-99-99");
$('.mask-pasport-series').mask('99-99');
$('.mask-pasport-number').mask('999999');
$('.mask-pasport-division').mask('999-999');
$('.mask-driver').mask('99 ** 999999');
$('#phone').on(" keyup change", function() {
    const phone_res = $('#phone').val();
    let arPhone = phone_res.includes("_");
    console.log(arPhone)
    if (arPhone === false) {
        $('#phone').attr('style', 'border: 1px solid #0fac0f!important;');
        //$('.fieldset-name').show()
        //  $('#RQ_IDENT_DOC_SER').attr('style', 'border: 2px solid #0fac0f!important; ');
        // $('#name').focus();
        document.getElementById("RQ_IDENT_DOC_SER").focus();
    } else {
        $('#phone').addClass('req')
        //  add_notification("Номер телефона введен не полностью", "false")
        //  setTimeout(messageClose, 3000)
    }
});
$('#phone').on("blur", function() {
    console.log('Элемент вышел из фокуса');
    let phoneRes = $('#phone').val();
    let blurPhone = phoneRes.includes("_");
    if (blurPhone === false) {
        //  $('#RQ_IDENT_DOC_SER').attr('style', 'border: 2px solid #0fac0f!important;');
    } else {

        add_notification("Номер телефона введен не полностью", "false")
        setTimeout(messageClose, 3000)
    }

});
$("#fms_unit").suggestions({
    token: token,
    type: "fms_unit"
});
$("#fullname").suggestions({
    token: token,
    type: "NAME",
    /* Вызывается, когда пользователь выбирает одну из подсказок */
    onSelect: function(suggestion) {
        let dataFullname = suggestion.data
        $('#NAME').attr('value', dataFullname['name']);
        $('#LAST_NAME').attr('value', dataFullname['surname']);
        $('#SECOND_NAME').attr('value', dataFullname['patronymic']);
    }
});
function saveUnloadingSuggestion(suggestion) {
    $('#ADDRESS_ONLY').attr('value',JSON.stringify(suggestion));
}

function suggestionsInit() {
    console.log('FULLADDRESS')
    $.ajax({
        url: 'https://city.brokci.ru/local/components/logistics/applications/js/jquery.suggestions.min.js',
        dataType: 'script',
        success: function() {
            $('#FULLADDRESS').suggestions({ // инпут с адресом разгрузки
                token: token,
                type: 'ADDRESS',
                noCache: true,
                triggerSelectOnBlur: false,
                onSelect: saveUnloadingSuggestion,
            });
        },
    });
}

$(document).on('click', '.dz-dropzone-delete', function (e) {

    $(this).parent().find("input").remove();
    $(this).parent().remove().hide();

});
$('#save').on('click', function () {
    let require_fields = check_require_fields()
    if (require_fields == true) {
        $('.btn-help-row').hide()
        var formData = new FormData(document.getElementById('add_contact'))
        console.log(formData)
        let files = $('#dropzone').get(0).dropzone.files
        var oldPhotos = []
        var sort = []
        files.forEach(function (item, i, arr) {
            if (item instanceof File) {
                formData.append('sort[]', item.name);
                formData.append('files[]', item, item.name);
                formData.append('test[]', item, item.name);
                console.log(item);
            } else {
                let oldPhotoInfo = []
                oldPhotoInfo.push(item.name)
                oldPhotoInfo.push(item.url)
                formData.append('sort[]', item.name);
                formData.append('oldPhotoInfo[]', oldPhotoInfo);
            }
        })
        //  formData.append('description', myEditor.getData());
        console.log(formData)
        $.ajax({
            url: '/local/components/hr/candidates/templates/.default/ajax/save.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.error) {
                    alert(response.error);
                    $('.btn-help-row').show()
                    $('#notification').empty()
                }
                if (response.message) {
                    alert(response.message);
                    $('#notification').empty()
                    $('#notification').html("Кандидат создан закройте вкладку")
                    $('#notification').attr('class', 'notification-success')
                }
            },
            error: function (data) {
                $('.btn-help-row').show()
                console.log(data)
            },
        })
    }
})



function add_notification(value, status) {
    let element = $('#notification').get(0)
    let div = document.createElement('div')
    if (status == true) {
        div.setAttribute('class', 'notification-success')
    } else {
        div.setAttribute('class', 'notification')
    }
    div.innerHTML = value
    element.appendChild(div)
}


function check_require_fields() {
    $('#notification').empty()
    var map = new Map()
    // console.log(map)
    let elements = $('.cs-input-block')
        .map(function () {
            map[this.id] = $(this).val()
        })
        .get()

    // console.log(map)

    let fields = []
    $('[require = true]').each(function (i) {
        fields[i] = $(this).attr('id')
    })
    let status = true
    fields.forEach(function (item) {
        $('#' + item).removeClass('req')
        if (map[item] === '' || map[item] == 0 || map[item] == null) {
            status = false
            let fieldName = $("label[for='" + item + "']").text()
            $('#' + item).addClass('req')
            add_notification('Не заполнено поле: ' + fieldName)
        }
    })
    const phone_res = $('#phone').val();
    let arPhone = phone_res.includes("_");
    if (arPhone === true) {
        $('#phone').addClass('req')
        status = false
        add_notification('Номер телефона введен не полностью')
    }

    if (status === false) {
        return false
    }

    if (status === true) {
        add_notification('Ошибок не найдено, начинаем обработку фотографий и сохранение данных. Это займет немного времени', true)
        return true
    }
}

function messageClose() {
    /*  $('#notification').hide()*/
    $('#notification').empty()
}