$(document).ready(function () {
    resizeFrame()
})

function activeMessager() {
    let profile = $('#UF_PROFILE_ID').val()
    if (profile) {
var auth
BX24.init(function () {
  auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/settings/mcm/templates/connector/ajax/ajax.php",
            type: "POST",
            data: {
                profile:  profile,
                options : $('#options').val(),
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
    }else{
        alert('Не выбран профиль мессенджера')
    }
}
function deactiveMessager() {
    let profileId = $('#profileId').val()
    console.log('deactiveMessager')
    console.log(profileId)
    if (profileId) {
        var auth
        BX24.init(function () {
            auth = BX24.getAuth()
            $.ajax({
                url: "/local/components/settings/mcm/templates/connector/ajax/deactive.php",
                type: "POST",
                data: {
                    profileId: profileId,
                    app: $('#app').val(),
                    id: $('#ID').val(),
                    options: $('#options').val(),
                    auth: auth,
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