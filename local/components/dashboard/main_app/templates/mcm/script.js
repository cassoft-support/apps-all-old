$(document).ready(function () {
    // var UserAut = $('#UserAut').val();
    let Width = window.screen.width
    //let Height = window.screen.height
    if (Width > 400) {
        //    switchTemplate('charts');
        switchTemplate('general_settings');
        // switchTemplate('mob');
        BX24.init(function () {
            //    var size = BX24.getScrollSize()
            //   console.log(size)
            //  BX24.resizeWindow(size.scrollWidth, size.scrollHeight)
        });
    } else {
        switchTemplate('chartsMob');
    }
    // $("#widthSc").html("ширина " + screenWidth + "высота" + screenHeight)


    //  BX24.fitWindow(function callback)
    //  BX24.resizeWindow(Integer width, Integer height, [function callback])
});

$('.click').on('click', function () {
    let type = $(this).attr('value')
    console.log(type)
    switchTemplate(type)
})


function switchTemplate(type) {

    BX24.init(function () {
        auth = BX24.getAuth()
      console.log(type)
    $.ajax({
        url: "/local/components/dashboard/main_app/templates/mcm/ajax.php",
        data: {
            auth: auth,
            member_id:$('#member_id').val(),
            app: $('#appCode').val(),
            type: type,
            user_id: $('#user_id').val(),
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
})
}