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


    let usersAdmin = $('#UF_CS_ADMIN').attr('cassoft-data')
    let usersAccountant = $('#UF_CS_ACCOUNTANT').attr('cassoft-data')
    setUsers('UF_CS_ADMIN', usersAdmin)
    setUsers('UF_CS_ACCOUNTANT', usersAccountant)


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

    $('#UF_CS_ADMIN').on('select2:open', function (e) {
        $('#UF_CS_ADMIN').select2('close')
        BX24.selectUsers(function (selected) {
            $('#UF_CS_ADMIN')
                .children('option:not(:first)')
                .remove()
            selected.forEach(function (value, key, arr) {

                let option = document.createElement('option')
                option.setAttribute('value', value['id'])
                option.setAttribute('selected', 'selected')
                option.innerHTML = value['name']
                $('#UF_CS_ADMIN').get('0').appendChild(option)
                console.log(value)
                if (value) {
                    $('#label_UF_CS_ADMIN').attr("style", " transform: translateY(-18px) translateX(10px) scale(0.9); color: var(--lb-font-color__val); ")
                } else {
                    $('#label_UF_CS_ADMIN').attr("style", "")
                }

            })
        })
    })
    $('#UF_CS_ACCOUNTANT').on('select2:open', function (e) {
        $('#UF_CS_ACCOUNTANT').select2('close')
        BX24.selectUsers(function (selected) {
            $('#UF_CS_ACCOUNTANT')
                .children('option:not(:first)')
                .remove()
            selected.forEach(function (value, key, arr) {

                let option = document.createElement('option')
                option.setAttribute('value', value['id'])
                option.setAttribute('selected', 'selected')
                option.innerHTML = value['name']
                $('#UF_CS_ACCOUNTANT').get('0').appendChild(option)
                console.log(value)
                if (value) {
                    $('#label_UF_CS_ACCOUNTANT').attr("style", " transform: translateY(-18px) translateX(10px) scale(0.9); color: var(--lb-font-color__focus);")
                } else {
                    $('#label_UF_CS_ACCOUNTANT').attr("style", "")
                }

            })
        })
    })

})

function entityUpdate(id) {
    $(".slider-btn-group-top").hide()
    /*[UF_CS_ADMIN] =>
[UF_CS_ACCOUNTANT] =>
[UF_CS_SECURITY_SERVICE] =>
[UF_CS_KEY_CDEK] =>
[UF_CS_ACCOUNT_CDEK] =>
[UF_CS_POST_RU] =>
[UF_CS_BUSINESS_LINE] =>
[UF_CS_GTD_KIT] =>
[UF_CS_BOXBERRY] =>
[UF_CS_KEY_PEC] =>
[UF_CS_LOGIN_PEC] =>
[UF_CS_KEY_DPD] =>
[UF_CS_NUMBER_DPD] =>
[UF_CS_LOGIN_IML] =>
[UF_CS_PASS_IML] =>
*/
    console.log(id)
    let UF_CS_KEY_ATI = $("#UF_CS_KEY_ATI").val()
    let UF_CS_ADMIN = JSON.stringify($("#UF_CS_ADMIN").val())
    let UF_CS_ACCOUNTANT = JSON.stringify($("#UF_CS_ACCOUNTANT").val())
    console.log(UF_CS_ADMIN)
    BX24.callMethod('entity.item.update', {
            ENTITY: 'setup',
            ID: id,
            PROPERTY_VALUES: {
                UF_CS_KEY_ATI: UF_CS_KEY_ATI,
                UF_CS_ADMIN: UF_CS_ADMIN,
                UF_CS_ACCOUNTANT: UF_CS_ACCOUNTANT,
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