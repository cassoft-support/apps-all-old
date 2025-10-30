$(document).on('click', '#updateApp', function () {
    let request = $('#reg').val()
    $('#blockButton').hide()
    BX24.init(function () {
        auth = BX24.getAuth()
    
    $.ajax({
        url: "/local/components/support/tasks_pro/ajax/ajax.php",
        type: "POST",
        data: {
            smart:$('#smart').val(),
            request: request,
            auth:auth
        },
        dataType: "html",
        success: function (response) {
            $('#blockButton').show()
            console.log(response)
            if (response) {

                $('.finishInstallBlock').html(response);
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
});
$(document).on('click', '#uploadApplication', function () {
    let request = $('#reg').val()
    let eventLoad = $('#eventLoad').val()
    //   let eventType = $('#eventType').val(
    $('#uploadApplication').hide()
    $('.finishInstallBlock').hide()
    $('.finishInstallBlock').html("")
    console.log(eventLoad)
    $.ajax({
        url: "/local/components/support/logistics_pro/ajax/ajax_load.php",
        type: "POST",
        data: {
            eventLoad: eventLoad,
            //    eventType: eventType,
            request: request
        },
        dataType: "html",
        success: function (response) {
            console.log(response)
            $('#uploadApplication').show()
            if (response) {
                $('.finishInstallBlock').html(response)
                $('.finishInstallBlock').show()
            }
        },
        error: function (html) {
            $('.finishInstallBlock').html(
                "Технические неполадки! Попробуйте перезагрузить страницу"
            );
            $('.finishInstallBlock').show()
            $('#uploadApplication').show()
        },

    })
});

$(document).on('click', '#upSmart', function () {
    let request = $('#reg').val()
    let smart = $('#smart').val()
    $('.finishInstallBlock').hide()
    $('.finishInstallBlock').html("")
    console.log(smart)
    $.ajax({
        url: "/local/components/support/logistics_pro/ajax/itemUpdate.php",
        type: "POST",
        data: {
            smart: smart,
            request: request
        },
        dataType: "html",
        success: function (response) {
            console.log(response)
            if (response) {
                $('.finishInstallBlock').html(response)
                $('.finishInstallBlock').show()
            }
        },
        error: function (html) {
            $('.finishInstallBlock').html(
                "Технические неполадки! Попробуйте перезагрузить страницу"
            );
            $('.finishInstallBlock').show()
        },
    })
});