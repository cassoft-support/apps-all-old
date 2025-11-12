$(document).ready(function() {
   // var UserAut = $('#UserAut').val();
    let Width = window.screen.width
   //let Height = window.screen.height
    if (Width > 400) {
        switchTemplate('chess');
        // switchTemplate('mob');
        BX24.init(function() {
         var size= BX24.getScrollSize()
         console.log(size)

         BX24.resizeWindow(size.scrollWidth, size.scrollHeight)
       

          });
    } else {
        switchTemplate('chartsMob');
    }
    // $("#widthSc").html("ширина " + screenWidth + "высота" + screenHeight)
   
    
      //  BX24.fitWindow(function callback)
     //  BX24.resizeWindow(Integer width, Integer height, [function callback])
});

$('.click').on('click', function() {
    let type = $(this).attr('value')
        //  console.log(type)
    switchTemplate(type)
})


function switchTemplate(type) {
 
    
  
   //let userBX24 = user_id_BX24
  // console.log(userBX24)
    let country = $('#country').val()
    let app = $('#apps').val()
    console.log(app)
    let member_id = $('#member_id').val()
    let user_id = $('#user_id').val()
    let UserAut = $('#UserAut').val()
    let authParams = JSON.parse(UserAut)
   //      console.log(authParams)
    $.ajax({
        url: "/local/components/brokci/dashboard/ajax/ajax-dev.php",
        data: {
            country: country,
            member_id: member_id,
            app: app,
            type: type,
            reg: UserAut,
            user_id: user_id,
            domain: authParams.DOMAIN,
        },
        dataType: "html",
        success: function(response) {
            $('#main').empty()
            $('#main').html(response)
                // $('#main').show()
        },
        error: function(data) {
            console.log(data)
        },
    })

}