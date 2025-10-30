$(document).ready(function () {

    $('#countryID, #region, #city ').select2({
        placeholder: 'Выберите значение',
        allowClear: true,
    })
    /*
        let countryId = $('#countryID').val()
        if (countryId != '') {
            getRegions(countryId)
        } else {
            $('#region_block').hide()
        }
        let regionId = $('#region').attr('cassoft-region-data')
        if (regionId != '' && countryId != '') {
            getCities(countryId, regionId)
        } else {
            $('#city_block').hide()
        }
    */
    var authParams = BX24.getAuth()
    var currentUserId
    BX24.callMethod('user.current', {}, function (res) {
        currentUserId = res.data().ID
    });

    $('.select2').select2({
        placeholder: ' ',
        allowClear: true,
    })


    $('.acor-btn').on('click', function () {
        let parent = $(this).parent()
        let label = parent.find('.acor-container-label')

        var _val = $(this).is(':checked') ? 'checked' : 'unchecked';
        if (_val == 'checked') {
            parent.addClass('open-slider')
            label.addClass('subtitle-block1')
            label.removeClass('subtitle-block')

        }
        if (_val == 'unchecked') {
            parent.removeClass('open-slider')
            label.removeClass('subtitle-block1')
            label.addClass('subtitle-block')
        }
    })


    let usersAdmin = $('#CS_ADMIN').attr('cassoft-data')
    let usersRecruter = $('#CS_RECRUITER').attr('cassoft-data')
    setUsers('CS_ADMIN', usersAdmin)
    setUsers('CS_RECRUITER', usersRecruter)


    function setUsers(element_id, assigned) {
        if (assigned != null && assigned != '' && assigned != undefined && assigned != 'null' && assigned != '[""]') {
            console.log("assigned")
            console.log(assigned)
            $('#label_' + element_id).attr("style", " transform: translateY(-18px) translateX(10px) scale(0.9); color: var(--lb-font-color__val); ")
            users = JSON.parse(assigned)
            for (key in users) {
                BX24.callMethod('user.get', {"ID": users[key]}, function (result) {
                    if (result.error()) {
                        console.log(result.error())
                    } else {
                        let userInfo = result.data()
                        let option = document.createElement('option')
                        option.setAttribute('value', users[key])
                        option.setAttribute('selected', 'selected')
                        option.innerHTML = userInfo[0].NAME + ' ' + userInfo[0].LAST_NAME
                        $('#' + element_id).get('0').appendChild(option)
                    }
                })

            }
        }
    }

    $('#CS_ADMIN').on('select2:open', function (e) {
        $('#CS_ADMIN').select2('close')
        BX24.selectUsers(function (selected) {
            $('#CS_ADMIN')
                .children('option:not(:first)')
                .remove()
            selected.forEach(function (value, key, arr) {

                let option = document.createElement('option')
                option.setAttribute('value', value['id'])
                option.setAttribute('selected', 'selected')
                option.innerHTML = value['name']
                $('#CS_ADMIN').get('0').appendChild(option)
                console.log(value)
                if (value) {
                    $('#label_CS_ADMIN').attr("style", " transform: translateY(-18px) translateX(10px) scale(0.9); color: var(--lb-font-color__val); ")
                } else {
                    $('#label_CS_ADMIN').attr("style", "")
                }

            })
        })
    })
    $('#CS_RECRUITER').on('select2:open', function (e) {
        $('#CS_RECRUITER').select2('close')
        BX24.selectUsers(function (selected) {
            $('#CS_RECRUITER')
                .children('option:not(:first)')
                .remove()
            selected.forEach(function (value, key, arr) {

                let option = document.createElement('option')
                option.setAttribute('value', value['id'])
                option.setAttribute('selected', 'selected')
                option.innerHTML = value['name']
                $('#CS_RECRUITER').get('0').appendChild(option)
                console.log(value)
                if (value) {
                    $('#label_CS_RECRUITER').attr("style", " transform: translateY(-18px) translateX(10px) scale(0.9); color: var(--lb-font-color__focus);")
                } else {
                    $('#label_CS_RECRUITER').attr("style", "")
                }

            })
        })
    })

})

function entityUpdate(id) {
    $(".slider-btn-group-top").hide()
    /* [CS_TOKEN_DADATA] =>
            [CS_KEY_DADATA] =>
            [CS_API_WHATSAPP] =>
            [CS_LINE_WHATSAPP] =>
            [CS_COLOR] =>
            [CS_BACKGROUND_COLOR] =>
            [CS_BACKGROUND_IMG] =>
            [CS_TELEGRAM_SHOP] =>
            [CS_TELEGRAM_GROUP] =>
            [CS_KEY_TELEGRAM] =>
            [CS_BOT_TELEGRAM] =>
           [CS_KEY_CDEK] =>
            [CS_ACCOUNT_CDEK] =>
            [CS_VK_ACCESS_TOKEN] =>
            [CS_VK_GROUP_ID] =>
            [CS_ACTIVE_VK] =>

*/
    console.log(id)
    let CS_ADMIN = JSON.stringify($("#CS_ADMIN").val())
    let CS_RECRUITER = JSON.stringify($("#CS_RECRUITER").val())
    console.log(CS_ADMIN)
    BX24.callMethod('entity.item.update', {
            ENTITY: 'setup',
            ID: id,
            PROPERTY_VALUES: {
                CS_HH_KEY: $("#CS_HH_KEY").val(),
                CS_HH_ID: $("#CS_HH_ID").val(),
                CS_RR_KEY: $("#CS_RR_KEY").val(),
                CS_ADMIN: CS_ADMIN,
                CS_RECRUITER: CS_RECRUITER,
            },
        },
        function (result) {
            if (result.error())
                console.error(result.error());
            else
                $(".result").html("Запись обновлена");
            setTimeout(resultMessage, 2000)

        }
    );


}

function resultMessage() {
    $(".result").hide()
    $(".slider-btn-group-top").show()
}