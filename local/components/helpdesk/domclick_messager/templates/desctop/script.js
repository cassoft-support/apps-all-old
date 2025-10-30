$(document).ready(function () {
    resizeFrame()
})

function activeDCMessager() {
    let keyMessegar = $('#keyMessegar').val()
    if (keyMessegar) {
var auth
BX24.init(function () {
  auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/settings/domclick_messager/templates/desctop/ajax/ajax.php",
            type: "POST",
            data: {
                key:  keyMessegar,
                options : $('#options').val(),
                app : $('#app').val(),
                id : $('#ID').val(),
                auth:auth,
            },

            success: function (response) {
                // $('#csvApp').show()
                console.log(response)
                if (response === 'Y') {
                    alert('Подключение произведено')
                    $('#formaApp').hide()
                    $('#deactive').show()
                }
                if (response === 'N') {
                    alert('Подключение не произведено, проверьте ключ и повторите попытку')
                }
            }
        })
});
    }else{
        alert('Не заполнено поле ключа от ДомКлик')
    }
}
function deactiveDCMessager() {
    let keyMessegar = $('#keyMessegar').val()

var auth
BX24.init(function () {
  auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/settings/domclick_messager/templates/desctop/ajax/deactive.php",
            type: "POST",
            data: {
                key:  keyMessegar,
                options : $('#options').val(),
                app : $('#app').val(),
                id : $('#ID').val(),
                auth:auth,
            },

            success: function (response) {
                // $('#csvApp').show()
                console.log(response)
                if (response === 'Y') {
                    alert('Отключение произведено')
                    $('#formaApp').show()
                    $('#deactive').hide()
                }
            }
        })
});

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