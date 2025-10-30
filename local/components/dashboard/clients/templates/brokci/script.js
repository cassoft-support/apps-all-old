var auth =[]

$(document).ready(function () {
    BX24.init(function () {
        auth = BX24.getAuth()
        console.log(auth)

    //  let UserAut = $('#UserAut').val();
    let screenWidth = window.screen.width
    let screenHeight = window.screen.height
    if (screenWidth > 400) {
        switchTemplate('object');
        console.log("400")
    } else {
        console.log("mob")
        switchTemplate('chartsMob');
    }
    })
    // $("#widthSc").html("ширина " + screenWidth + "высота" + screenHeight)
    // console.log(screenWidth)
    //  console.log(screenHeight)
    //    app.resizeFrame();
});


$('.click').on('click', function () {
    let type = $(this).attr('value')
    switchTemplate(type)
})


function switchTemplate(type) {
    console.log(type)
    let authParams = auth
    console.log(authParams)
    $.ajax({
        url: "/local/components/dashboard/main_app/templates/brokci/ajax-brokci.php",
        type: 'POST',
        data: {
            user_id: $('#user_id').val(),
            type: type,
            deal_id: $('#deal_id').val(),
            app: $('#apps').val(),
            auth: authParams,
        },
        dataType: "html",
        success: function (response) {
            $('#main').empty()
            $('#main').html(response)
            resizeFrame()
        },
        error: function (data) {
            console.log(data)
        },
    })
}

/*
$('#admin').on('click', function() {
    $('#main').empty()
    $('#user-menu').hide()
    $('#admin-menu').show()
    $('#welcome').show()

})
$('#user').on('click', function() {
    $('#main').empty()
    $('#user-menu').show()
    $('#admin-menu').hide()
    $('#welcome').hide()
    switchTemplate('charts');
})
*/

function resizeFrame() {
    var currentSize = BX24.getScrollSize();
    //  console.log(currentSize)
    minHeight = currentSize.scrollHeight;
    var FrameWidth = document.getElementById("main").offsetWidth;
    console.log(FrameWidth)
    if (minHeight < 300){
        frameHeight = 200;
    } else{
        frameHeight = minHeight+100;
    }
     console.log(frameHeight)
    BX24.resizeWindow(FrameWidth, frameHeight);
}