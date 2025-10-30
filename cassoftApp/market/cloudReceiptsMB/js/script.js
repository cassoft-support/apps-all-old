var user = []
//var auth = []
BX24.init(function () {
    BX24.callMethod('user.current', {}, function (res) {
        user = res.data()
    });
   // auth = BX24.getAuth()
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
      let request=$('#request').html()  
        $(this).hide()
        $('.progress').show()
        var data = {}
        data.request = request
        data.user = user
       // data.authParams = auth
      //  console.log(data)
        post(
            'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/entityInstall/installPaySystem.php',
            data,
            1
        ).then(function (result) {
            console.log(result)
            changeProgressBar(1)
            return post(
                'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/entityInstall/installSetup.php',
                data,
                2
            )
        }).then(function (result) {
            console.log(result)

            changeProgressBar(2)
            return post(
                'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/entityInstall/installTabs.php',
                data,
                3
            )
        }).then(function (result) {
          console.log(result)

          changeProgressBar(3)
          return post(
              'https://city.brokci.ru/pub/cassoftApp/cloudReceiptsMB/entityInstall/installFinish.php',
              data,
              4
          )
      }).then(function (result) {
            console.log(result)
            changeProgressBar(4)
            finishInstall()
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
        $('.progress .circle:nth-of-type(' + i + ')').removeClass('active').addClass('done');
        $('.progress .circle:nth-of-type(' + i + ')').removeClass('pulse');
        $('.progress .circle:nth-of-type(' + i + ') .label').html('&#10003;');
        $('.progress .bar:nth-of-type(' + (i - 1) + ')').removeClass('active').addClass('done');
    }
    function makeActive(i) {
        $('.progress .circle:nth-of-type(' + i + ')').addClass('active');
        $('.progress .circle:nth-of-type(' + i + ')').addClass('pulse');
        $('.progress .bar:nth-of-type(' + (i - 1) + ')').addClass('active');
    }
})
