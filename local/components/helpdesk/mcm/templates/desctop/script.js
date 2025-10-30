$(document).ready(function () {
    resizeFrame()
})

function addProfile() {

var auth
BX24.init(function () {
  auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/settings/mcm/templates/desctop/ajax/ajax.php",
            type: "POST",
            data: {

                app : $('#app').val(),
                id : $('#ID').val(),
                auth:auth,
            },

            success: function (response) {
                // $('#csvApp').show()
                console.log(response)
                if (response === 'Y') {
                    alert('Обработка завершина')
                    $('#link').show()
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