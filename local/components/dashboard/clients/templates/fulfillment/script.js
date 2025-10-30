$(document).ready(function () {
    // var UserAut = $('#UserAut').val();
    let Width = window.screen.width
    //let Height = window.screen.height
    let tab = $("#placement").val()
    if (Width > 400 ) {
        console.log(tab)
        //    switchTemplate('charts');
       switchTemplate('product');
        // switchTemplate('mob');
        // BX24.init(function () {
        //     //    var size = BX24.getScrollSize()
        //     //   console.log(size)
        //     //  BX24.resizeWindow(size.scrollWidth, size.scrollHeight)
        // });
    } else {
      //  switchTemplate('chartsMob');
    }
    // $("#widthSc").html("ширина " + screenWidth + "высота" + screenHeight)


    //  BX24.fitWindow(function callback)
    //  BX24.resizeWindow(Integer width, Integer height, [function callback])
});


$('.click').on('click', function () {
    let type = $(this).attr('value')
    //  console.log(type)
    switchTemplate(type)
})



function switchTemplate(type) {

    let app = $('#apps').val()
    // console.log(app)
    let member_id = $('#member_id').val()
    let UserAut = $('#UserAut').val()
    let user_id = $('#user_id').val()

    // console.log(member_id, 'member_id')
    // console.log(UserAut, 'UserAut')
    // console.log(user_id, 'user_id')
    // console.log(app, 'app')

    //  console.log(type)
    $.ajax({
        url: "/local/components/dashboard/main_app/ajax/ajax-fulfillment.php",
        data: {
            UserAut: UserAut,
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
         //   resizeFrame()
        },
        error: function (data) {
            console.log(data)
        },
    })

}

// function resizeFrame() {
//     var currentSize = BX24.getScrollSize();
//     console.log(currentSize)
//     minHeight = currentSize.scrollHeight;
//     var FrameWidth = document.getElementById("workareaApp").offsetWidth;
//     console.log(FrameWidth)
//     if (minHeight < 300){
//         frameHeight = 300;
//     } else{
//         frameHeight = minHeight + 100;
//     }
//     console.log(frameHeight)
//     BX24.resizeWindow(FrameWidth, frameHeight);
// }