$(document).on('click', '#updateApp', function () {
    let request = $('#reg').val()
    console.log(request)
    $.ajax({
        url: "/local/components/install/logistics_pro/ajax/ajax_update.php",
        type: "POST",
        data: {
            request: request
        },
        dataType: "text",
        success: function (response) {
            console.log(response)
            if (response == 'yes') {
                $('.finishInstallBlock').html('Приложение обновлено')
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
