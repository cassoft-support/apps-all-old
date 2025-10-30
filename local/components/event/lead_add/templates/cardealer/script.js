var user = []
var auth = []
BX24.init(function () {
    BX24.callMethod('user.current', {}, function (res) {
        user = res.data()
    });
    auth = BX24.getAuth()
});
$(document).ready(function () {
    $('.progress .circle').removeClass().addClass('circle');
    $('.progress .bar').removeClass().addClass('bar');

    function post(url, data, i) {
        makeActive(i)
        return new Promise(function (resolve, reject) {
            $.ajax({
                data: data,
                dataType: 'json',
                type: 'POST',
                url: url,
                success: resolve,
                error: reject,
            })
        })
    }

    $(document).on('click', '#install', function () {
        $(this).hide()
        $('.progress').show()
        var data = {}
        data.user = user
        data.authParamsBX = auth
        console.log("authParamsBX")
        // console.log(auth)
        data.authParams = $("#reg").val()
        data.cassoftApp = $("#cassoftApp").val()
        // console.log(data)
        post(
            /*    'https://city.brokci.ru/pub/cassoftApp/brokci/updateApp/updatePlans.php',
                data,
                1
            ).then(function (result) {
                console.log(result)
                changeProgressBar(1)
                return post(
                  */
            '/local/components/brokci_settings/update/updateApp/updateObject.php',
            data,
            2
            /*    )
              // }*/
        ).then(function (result) {
                console.log(result)
                changeProgressBar(2)
                return post(
                    '/local/components/brokci_settings/update/updateApp/updateMarketing.php',
                    data,
                    3
                )
            }
        ).then(function (result) {
            console.log(result)
            changeProgressBar(3)
            return post(
                '/local/components/brokci_settings/update/updateApp/updateSetup.php',
                data,
                4
            )
            /*   }).then(function (result) {
                 changeProgressBar(4)
                 return post(
                   '/local/components/brokci_settings/update/updateApp/updateTabs.php',
                     data,
                     5
                 )*/
        }).then(function (result) {
            changeProgressBar(4)
            return post(
                '/local/components/brokci_settings/update/updateApp/updateSite.php',
                data,
                6
            )
        }).then(function (result) {
            changeProgressBar(6)
            return post(
                '/local/components/brokci_settings/update/updateApp/updateFinish.php',
                data,
                7
            )
        }).then(function (result) {
            console.log(result)
            changeProgressBar(7)
            finishInstall()
        })


    })
    $(document).on('click', '#install1', function () {
        let request = $('#reg').val()
        let app = $('#app').val()
        $("#install1").hide()
        console.log(request)
        $.ajax({
            //   url: '/local/components/install/logistics_pro/ajax/ajax_update.php', //установка или обновления хранилищ
            url: '/local/lib/Install/entity/installEntity.php', //установка или обновления хранилищ
            type: "POST",
            data: {
                request_install: request,
                app: app
            },
            dataType: "json",
            success: function (response) {
                $("#install1").show()
                console.log(response)
                console.log(response.entity)
                // $('.finishInstallBlock').html(response)
                $('.finishInstallBlock').show()
                if (response.entity == 'success') {
                    $('.finishInstallBlock').html('Приложение установлено')
                    $('.finishInstallBlock').show()
                    finishInstall()
                }
            },
            error: function (html) {
                $('.finishInstallBlock').html(
                    "Технические неполадки! Попробуйте перезагрузить страницу"
                );
                $('.finishInstallBlock').show()
            },
        })

    })

    function finishInstall() {
        add_notification('Приложение установлено', true)
        $('.finishInstallBlock').show()
        setTimeout(BX24.installFinish(), 3000);
    }

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

    function changeProgressBar(i) {
        console.log("changeProgressBar")
        console.log(i)
        $('.progress .circle:nth-of-type(' + i + ')').removeClass('active').addClass('done');
        $('.progress .circle:nth-of-type(' + i + ')').removeClass('pulse');
        $('.progress .circle:nth-of-type(' + i + ') .label').html('&#10003;');
        $('.progress .bar:nth-of-type(' + (i - 1) + ')').removeClass('active').addClass('done');
    }

    function makeActive(i) {
        console.log("makeActive")
        console.log(i)
        $('.progress .circle:nth-of-type(' + i + ')').addClass('active');
        $('.progress .circle:nth-of-type(' + i + ')').addClass('pulse');
        $('.progress .bar:nth-of-type(' + (i - 1) + ')').addClass('active');
    }
})