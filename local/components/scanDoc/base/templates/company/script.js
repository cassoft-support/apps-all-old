console.log('testlog')
Dropzone.autoDiscover = false;
var auth = []
BX24.init(function () {
    auth = BX24.getAuth()
});
$(document).ready(function() {
    setTimeout(function () {
        var headID = document.getElementsByTagName("head")[0];
        var newScript = document.createElement('script');
        newScript.type = 'text/javascript';
        newScript.src = '/local/components/scanDoc/base/templates/company/gallery.js?1548';
        headID.appendChild(newScript);
    }, 2000);
        resizeFrame();
});

function resizeFrame() {
    var currentSize = BX24.getScrollSize();
    console.log(currentSize)
    minHeight = currentSize.scrollHeight;
    var FrameWidth = document.getElementById("workarea").offsetWidth;
    console.log(FrameWidth)
    if (minHeight < 300){
        frameHeight = 300;
    } else{
        frameHeight = minHeight + 50;
    }
    console.log(frameHeight)
    BX24.resizeWindow(FrameWidth, frameHeight);
}
$(document).on('click', '.dz-dropzone-delete', function (e) {

    $(this).parent().find("input").remove();
    $(this).parent().remove().hide();

});
$(".sortable").sortable({
    handle: '.sortable-handle',
    //zIndex: 1000,
    cursor: 'move',
    opacity: 0.5,
    start: function (event, ui) {
        ui.item.startPos = ui.item.index();
    },
    stop: function (event, ui) {
        console.log("Start position: " + ui.item.startPos);
        console.log("New position: " + ui.item.index());
        //console.log(ui.item)
        var queue = myDrop.files;
        console.log(queue)
        //console.log(thisDropzone.files)

        newQueue = [];
        $('.dropzone .dz-preview .dz-filename [data-dz-name]').each(function (count, el) {
            var name = el.innerHTML;
            console.log(el)
            var mockFile = {
                name: "name",
            };
            //myDrop.options.addedfile.call(myDrop, mockFile);
            queue.forEach(function (file) {
                //console.log(file)
                if (file.name === name) {
                    newQueue.push(file);
                }
            });
        });
        myDrop.files = newQueue;
    }
});

$('#save').on('click', function() {
    $('#save').hide()
    $('.info').show()
  //  var authParams = Array();
    var authParams = Array();
    authParams.push(BX24.getAuth())
   // console.log(authParams)
    var formData = new FormData(document.getElementById('scanForm'))

    let files = $('#dropzone').get(0).dropzone.files
    var oldPhotos = []
    var sort = []
    files.forEach(function(item, i, arr) {
        // console.log(item, 'testItem')
        if (item instanceof File) {
            formData.append('sort[]', item.name);
            formData.append('files[]', item, item.name);
            //  formData.append('test[]', item, item.name);
        } else {
            let oldPhotoInfo = []
            oldPhotoInfo.push(item.name)
            oldPhotoInfo.push(item.url)
            formData.append('sort[]', item.name);
            formData.append('oldPhotoInfo[]', oldPhotoInfo);
        }
    })
    formData.append("authParams", JSON.stringify(authParams[0]));
    formData.append('app', $('#app').val());
    formData.append('company_id', $('#company_id').val());

      console.log(formData, 'testFormData')
    $.ajax({
        url: "/local/components/scanDoc/base/templates/company/save.php",
        type: "POST",
        data:formData,
        processData: false,
        contentType: false,
      //  dataType: "json",
        success: function(response) {
            console.log("save-edit")
           // alert("Данные сохранены")
          //  console.log(response)
            location.reload();

        },

        //},
        error: function(data) {
            console.log(data)
        },
    })
    //  }
})

function scanEdit(){
    $("#galForm").show()
    $("#galCard").hide()
    resizeFrame()
}
function resizeFrameGal() {
    var FrameWidth = document.getElementById("workarea").offsetWidth;
        frameHeight = 1000;
    BX24.resizeWindow(FrameWidth, frameHeight);
}
$('.scan-doc').on('click', function() {
    console.log('scan-doc')
    resizeFrameGal()
    $('[data-fancybox="gallery"]').fancybox({
        loop: true,
        autoFocus: false,
        animationEffect: "zoom",
        transitionIn: 'elastic',
        transitionOut: 'elastic',
        speedIn: 500,
        speedOut: 500,
        hideOnOverlayClick: false,
        titlePosition: 'over',
        fullScreen: {
            autoStart: false
        },
        afterClose  : function() {
            resizeFrame()
        },
    })
})
