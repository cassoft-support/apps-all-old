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
        newScript.src = '/local/components/scanDoc/base/js/gallery.js';
        headID.appendChild(newScript);
    }, 2000);
        resizeFrame();
});

function resizeFrame() {
    var currentSize = BX24.getScrollSize();
    console.log(currentSize)
    minHeight = currentSize.scrollHeight;
    //var FrameWidth = document.getElementById("workscan").offsetWidth;
    var FrameWidth = currentSize.scrollWidth;
    console.log(FrameWidth)
    if (minHeight < 350){
        frameHeight = 350;
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
    //console.log($('#scanForm').val(), 'LogScanForm')
    var formData = new FormData(document.getElementById('scanForm'))

    let files = $('#dropzone').get(0).dropzone.files
    var oldPhotos = []
    var sort = []

    console.log(files, 'files')
    files.forEach(function(item, i, arr) {
        if (item instanceof File) {
            formData.append('sort[]', item.name);
            formData.append('files[]', item, item.name);
            //  formData.append('test[]', item, item.name);
        } else {
            console.log('oldPhotoInfo')
            console.log(item)
            console.log(item.name)
            console.log(item.url)
            let oldPhotoInfo = []
            oldPhotoInfo.push(item.name)
            oldPhotoInfo.push(item.url)
            console.log(oldPhotoInfo)
            formData.append('sort[]', item.name);
            formData.append('oldPhotoInfo[]', oldPhotoInfo);
        }
    })
    formData.append("authParams", JSON.stringify(authParams[0]));
    formData.append('app', $('#app').val());
    formData.append('id', $('#id').val());
    formData.append('type', $('#type').val());
    formData.append('smartId', $('#smartId').val());
    formData.append('entityTypeId', $('#entityTypeId').val());

      console.log(formData, 'testFormData')
    $.ajax({
        url: "/local/components/scanDoc/base/templates/crm/save.php",
        type: "POST",
        data:formData,
        processData: false,
        contentType: false,
      //  dataType: "json",
        success: function(response) {
            console.log("save-edit")
           // alert("Данные сохранены")
         console.log(response)
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
    var currentSize = BX24.getScrollSize();
    console.log(currentSize)
    var FrameWidth = currentSize.scrollWidth;
    console.log(FrameWidth)
        frameHeight = 1000;
    BX24.resizeWindow(FrameWidth, frameHeight);
}
$('.scan-doc').on('click', function() {
    console.log('scan-doc')
    resizeFrameGal()
    $('[data-fancybox="gallery"]').fancybox({
        loop: true,
        autoFocus: true,
      //  autoScale: true,
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

// function resizeFrame() {
//     var currentSize = BX24.getScrollSize();
//     console.log(currentSize)
//     minHeight = currentSize.scrollHeight;
//     var FrameWidth = currentSize.scrollWidth;
//    // var FrameWidth = document.getElementById("workarea").offsetWidth;
//     console.log(FrameWidth)
//     if (minHeight < 150){
//         frameHeight = 150;
//     } else{
//         frameHeight = minHeight + 50;
//     }
//     console.log(frameHeight)
//     BX24.resizeWindow(FrameWidth, frameHeight);
// }