$(document).ready(function () {
    // var UserAut = $('#UserAut').val();
    let Width = window.screen.width
    //let Height = window.screen.height
    if (Width > 400) {
        switchTemplate('charts');
        BX24.init(function () {
            //    var size = BX24.getScrollSize()
            //   console.log(size)
            //  BX24.resizeWindow(size.scrollWidth, size.scrollHeight)
        });
    } else {
        switchTemplate('chartsMob');
    }

});

$('.click').on('click', function () {
    let type = $(this).attr('value')
    //  console.log(type)
    switchTemplate(type)
})


function switchTemplate(type) {

    let app = $('#apps').val()
    console.log(app)
    let member_id = $('#member_id').val()
    let user_id = $('#user_id').val()
    console.log(type)
    $.ajax({
        url: "/local/components/dashboard/main_app/ajax/ajax-callControler.php",
        data: {
            member_id: member_id,
            app: app,
            type: type,
            user_id: user_id,
        },
        dataType: "html",
        success: function (response) {
            $('#main').empty()
            $('#main').html(response)
            // $('#main').show()
        },
        error: function (data) {
            console.log(data)
        },
    })

}