$(document).ready(function () {
    resizeFrame()
})

function loadingHH(type) {
        var auth
        BX24.init(function () {
            auth = BX24.getAuth()
            $.ajax({
                url: "/local/components/settings/hr_pro/templates/hh/ajax/ajax.php",
                type: "POST",
                data: {
                    type:  type,
                    app : $('#app').val(),
                    id : $('#ID').val(),
                    user_id : $('#user_id').val(),
                    options : $('#options').val(),
                    auth:auth,
                },
                dataType: 'html',
                success: function (response) {
                    // $('#csvApp').show()
                    console.log(response)
                    $('#resHH').html(response)
                    if (response === 'Y') {
                        // $('#formaApp').hide()
                        // $('#deactive').show()
                    }
                }
            })
        });

}
function deactiveMessager() {
    let keyCian = $('#keyCian').val()
    if (keyCian) {
        var auth
        BX24.init(function () {
            auth = BX24.getAuth()
            $.ajax({
                url: "/local/components/settings/cian_messager/templates/desctop/ajax/deactive.php",
                type: "POST",
                data: {
                    key:  keyCian,
                    app : $('#app').val(),
                    id : $('#ID').val(),
                    options : $('#options').val(),
                    auth:auth,
                },
                dataType: 'text',
                success: function (response) {
                    // $('#csvApp').show()
                    console.log(response)
                    if (response === 'Y') {
                        $('#formaApp').show()
                        $('#deactive').hide()
                    }
                }
            })
        });
    }else{
        alert('Не заполнено поле ключа от ЦИАН')
    }
}

function resizeFrame() {
    var currentSize = BX24.getScrollSize();

    minHeight = currentSize.scrollHeight;
    var FrameWidth = document.getElementById("mainApp").offsetWidth;
    console.log(minHeight)
    console.log(FrameWidth)
    if (minHeight < 300){
        frameHeight = 300;
    } else{
        frameHeight = minHeight+50;
    }
    console.log(frameHeight)
    BX24.resizeWindow(FrameWidth, frameHeight);
}