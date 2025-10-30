var myDrop = new Dropzone("#dropzone", {
    paramName: "file",
    // acceptedFiles: "image/jpeg,image/png, .pdf ",
    acceptedFiles: "image/jpeg,image/png,image/pdf ",
    maxFiles: 100,
    parallelUploads: 100,
    uploadMultiple: true,
    method: "post",
    url: '/upload/tmp',
    autoProcessQueue: false,
    dictDefaultMessage: 'Выберите или перетащите изображения',
    init: function () {
        thisDropzone = this;
        var submitButton = document.querySelector("#save")
        submitButton.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
        this.on("addedfile", function (file) {

            var removeButton = Dropzone.createElement("<div class='dz-dropzone-delete' ><i class='las la-trash-alt'></i></div>");
            var sortableButton = Dropzone.createElement("<div class='dz-dropzone-sortable sortable-handle' ><i class='las la-arrows-alt'></i></div>");
            var rotateButton = Dropzone.createElement('<div style="display:none;" class="dz-dropzone-rotate rotete-handle" ><i class="las la-redo-alt"></i></div>');
            var _this = this;
            removeButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                _this.removeFile(file);
            });

            rotateButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                //console.log(file);

                if ($(event.target).hasClass('la-redo-alt')) { // Rotate
                    var img_src = file.dataURL;
                    if (img_src && img_src != '') {
                        is_raw = img_src.includes("data:image");
                        if (is_raw) {
                            rotate_image(img_src, 90, false, function (res) {

                                //file.dataURL = res;
                                //file.previewElement.querySelector("img").src = res;
                                //Dropzone.dataURItoBlob(resizedDataURL);

                                var tmp_file = file;
                                myDrop.removeFile(file);

                                fetch(res)
                                    .then(res => res.blob())
                                    .then(blob => {
                                        nfile = new File([blob], tmp_file.name, {type: tmp_file.type});
                                        nfile.dataURL = res;
                                        nfile.accepted = true;
                                        nfile.height = tmp_file.height;
                                        nfile.height = tmp_file.height;
                                        nfile.previewElement = tmp_file.previewElement;
                                        nfile.previewTemplate = tmp_file.previewTemplate;
                                        nfile.status = tmp_file.status;
                                        nfile.upload = tmp_file.upload;
                                        nfile.previewElement.querySelector("img").src = res;

                                        myDrop.files.push(nfile);

                                        myDrop.emit("addedfile", nfile);
                                        myDrop.emit("thumbnail", nfile, nfile.dataURL);
                                        myDrop.emit("complete", nfile)

                                    });

                            });
                        } else {
                            rotate_file(img_src, 90, false, file.name, function (res) {
                                console.log(res);
                                var new_file = res + '?' + new Date().getTime();
                                file.previewElement.querySelector("img").src = new_file;
                                thisDropzone.createThumbnailFromUrl(file, new_file);
                            });
                        }
                    }
                }


            });

            file.previewElement.appendChild(removeButton);
            file.previewElement.appendChild(sortableButton);
            file.previewElement.appendChild(rotateButton);
            file.previewElement.classList.add("dz-success");
            file.previewElement.classList.add("dz-complete");

        });
        this.on('completemultiple', function (file, response) {

        });
        this.on("sendingmultiple", function (files, response) {
            console.log('sendingmultiple')
        });
        this.on("successmultiple", function (files, response) {
            var response = jQuery.parseJSON(response);
            //   console.log('successmultiple')
            //    console.log(response)
        });
        this.on("errormultiple", function (files, response) {
            var response = jQuery.parseJSON(response);
            console.log('errormultiple')
            //   console.log(files)
        });

        this.on("thumbnail", function (file) {
            //console.log("thumbnail");
            if (file.status && file.status == "queued") $(file.previewElement.getElementsByClassName("dz-dropzone-rotate")).get(0).style.display = '';
            //$(file.previewElement.getElementsByClassName("dz-dropzone-rotate")).get(0).style.display = '';
        });
        console.log(thisDropzone.files)
        var gal = jQuery.parseJSON($('#gal').text());
        console.log(gal, 'testGal')

        gal.forEach(function (item, i, arr) {
            //    console.log(item)
            let mockFile = {
                name: item.photo_id,
                type: 'image/jpeg',
                size: "12345",
                status: Dropzone.ADDED,
                url: item.photo_link,
                dataURL: item.photo_link,
            };
            thisDropzone.files.push(mockFile);
            var url = item.photo_link
            thisDropzone.emit("addedfile", mockFile);
            thisDropzone.emit("thumbnail", mockFile, mockFile.url);
            thisDropzone.emit("complete", mockFile)
        });
    },

});

/* $(document).on('click', '#test_span78', function (e) {
	console.log('Debug');
	//console.log(Dropzone.files);

}); */
/*
var myDropPlans = new Dropzone("#dropzone_plans", {
    paramName: "file",
    acceptedFiles: "image/jpeg,image/png,image/heic",
    maxFiles: 100,
    parallelUploads: 100,
    uploadMultiple: true,
    method: "post",
    url: 'https://city.brokci.ru/poligon/newGallery/upload2.php',
    autoProcessQueue: false,
    dictDefaultMessage: 'Выберите или перетащите изображения',
    init: function () {
        thisDropzone = this;
        var submitButton = document.querySelector("#save")
        submitButton.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
        });
        this.on("addedfile", function (file) {
            var removeButton = Dropzone.createElement("<div class='dz-dropzone-delete' ><i class='las la-trash-alt'></i></div>");
            var sortableButton = Dropzone.createElement("<div class='dz-dropzone-sortable sortable-handle' ><i class='las la-arrows-alt'></i></div>");
            var _this = this;
            removeButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                _this.removeFile(file);
            });
            file.previewElement.appendChild(removeButton);
            file.previewElement.appendChild(sortableButton);
            file.previewElement.classList.add("dz-success");
            file.previewElement.classList.add("dz-complete");
        });
        this.on('completemultiple', function (file, response) {

        });
        this.on("sendingmultiple", function (files, response) {
            console.log('sendingmultiple')
        });
        this.on("successmultiple", function (files, response) {
            var response = jQuery.parseJSON(response);
            console.log('successmultiple')
            console.log(response)
        });
        this.on("errormultiple", function (files, response) {
            var response = jQuery.parseJSON(response);
            console.log('errormultiple')
            console.log(files)
        });


        		this.on("thumbnail", function(file) {
                        console.log(event.target);
                });

        console.log(thisDropzone.files)
        var gal = jQuery.parseJSON($('#plans').text());
        console.log(gal)

        gal.forEach(function (item, i, arr) {
            console.log(item)
            let mockFile = {
                name: item.photo_id,
                type: 'image/jpeg',
                size: "12345",
                status: Dropzone.ADDED,
                url: item.photo_link,
                dataURL: item.photo_link,
            };
            thisDropzone.files.push(mockFile);
            var url = item.photo_link
            thisDropzone.emit("addedfile", mockFile);
            thisDropzone.emit("thumbnail", mockFile, mockFile.url);
            thisDropzone.emit("complete", mockFile)
        })

    },

});

function convertHeicToJpg(input) {
    var fileName = $(input).val();
    var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
    if (fileNameExt == "heic") {
        var blob = $(input)[0].files[0]; //ev.target.files[0];
        heic2any({
            blob: blob,
            toType: "image/jpg",
        })
            .then(function (resultBlob) {

                var url = URL.createObjectURL(resultBlob);
                $(input).parent().find(".upload-file").css("background-image", "url(" + url + ")"); //previewing the uploaded picture
                //adding converted picture to the original <input type="file">
                let fileInputElement = $(input)[0];
                let container = new DataTransfer();
                let file = new File([resultBlob], "heic" + ".jpg", {
                    type: "image/jpeg",
                    lastModified: new Date().getTime()
                });
                container.items.add(file);

                fileInputElement.files = container.files;
                console.log("added");
            })
            .catch(function (x) {
                console.log(x.code);
                console.log(x.message);
            });
    }
}

function rotate_file(fileUrl, angle, clockwise = true, img_id, callback) {
    $.ajax({
        url: "/local/components/catalog_object/Object/ajax/ajax_file_rotate.php",
        type: "POST",
        dataType: 'json',
        cache: false,
        data: {
            'url': fileUrl,
            'angle': angle,
            'photoF': $('#photoF').val(),
            'clockwise': clockwise,
            'img_id': img_id,
            'auth': $("#UserAut").val()
        },
        success: function (qdata) {
            if (qdata && qdata.ok) {
                callback(qdata.url);
            } else {
                console.log(qdata.err_txt);
            }
        }
    });
}
*/
function rotate_image(src, angle, clockwise = true, callback) {
    var img = new Image();
    img.src = src;
    img.onload = function () {
        var canvas = document.createElement('canvas');
        canvas.width = img.height;
        canvas.height = img.width,
            degree = angle;
        if (!clockwise) degree = (360 - angle);
        canvas.style.position = "absolute";
        var context = canvas.getContext("2d"),
            cx = 0.5 * img.width,
            cy = 0.5 * img.height;
        context.save();
        context.translate(cy, cx);
        context.rotate((Math.PI / 180) * degree);
        context.translate(-cx, -cy);
        context.drawImage(img, 0, 0);
        context.restore();
        callback(canvas.toDataURL());
        canvas.remove();
    }
}