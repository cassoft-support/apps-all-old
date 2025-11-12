$(document).ready(function() {
    let UserAut = $('#UserAut').val();
    let screenWidth = window.screen.width
    let screenHeight = window.screen.height
    if (screenWidth > 400) {
        switchTemplate('dashboard');
        // switchTemplate('mob');
    } else {
        switchTemplate('dashboard');
    }
    // $("#widthSc").html("ширина " + screenWidth + "высота" + screenHeight)
    console.log(screenWidth)
    console.log(screenHeight)
        //   app.resizeFrame();
});

$('.click').on('click', function() {
    let type = $(this).attr('value')
        //  console.log(type)
    switchTemplate(type)
})


function switchTemplate(type) {
    //let type = typecountry
   let member_id = $('#member_id').val()
   let app = $('#app').val()
   let UserAut = $('#UserAut').val()
 let authParams = JSON.parse(UserAut)
    console.log(type)
    $.ajax({
        url: "/local/components/dashboard/main_app/templates/beeline/tools/ajax.php",
        data: {
            authParams: authParams,
            type: type,
            member_id: member_id,
            app: app,
        },
        dataType: "html",
        success: function(response) {
            console.log(response)
            $('#main').empty()
            $('#main').html(response)
        },
        error: function(data) {
            console.log(data)
        },
    })
}