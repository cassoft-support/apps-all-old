$(document).on('click', '#updateApp', function () {
    let request = $('#reg').val()
    $('#blockButton').hide()

    
    $.ajax({
        url: "/local/components/support/mcm/ajax/ajax.php",
        type: "POST",
        data: {

            request: request
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
});
