$(document).ready(function () {
  addKeyHH()
     //   setTimeout(addKeyHH, 1000);
});
function addKeyHH() {
console.log('addKeyHH')
        $.ajax({
            url: "/local/components/settings/base/templates/hr_pro/ajax/addKey.php",
            type: 'POST',
            data: {
                member_id:$('#member_id').val(),
                app: $('#appCode').val(),
                keyHH: $('#keyHH').val(),
                setupKeyHH: $('#setupKeyHH').val(),
                id: $('#ID').val(),
                user_id: $('#user_id').val(),
            },
            dataType: "html",
            success: function (response) {
             alert('Регистрация сохранена, вернитесь в приложение')
                window.close();
            },
            error: function (data) {
                console.log(data)
            },
        })

}