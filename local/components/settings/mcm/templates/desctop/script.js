$(document).ready(function () {
  //  resizeFrame()
})

function addProfile() {
if($('#profileName').val()) {
    const radios = document.querySelectorAll('input[name="customRadio"]');
    let typeLine
    radios.forEach(radio => {
        if (radio.checked) {
            typeLine = radio.id
        }
    });
    var auth
    BX24.init(function () {
        auth = BX24.getAuth()
        $.ajax({
            url: "/local/components/settings/mcm/templates/desctop/ajax/ajax.php",
            type: "POST",
            data: {

                app: $('#appCode').val(),
                profile_name: $('#profileName').val(),
                typeLine: typeLine,
                auth: auth,
            },

            success: function (response) {
                // $('#csvApp').show()
                console.log(response)
                if (response === 'Y') {
                    alert('Обработка завершина')
                    switchTemplate('general_settings')
                }
            }
        })
    });
}else{
    alert("Не введено имя профиля")
}
}
// $('body').on('input', '.profile-en', function(){
//     this.value = this.value.replace(/[^0-9a-zA-Z]/g, '');
// });

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