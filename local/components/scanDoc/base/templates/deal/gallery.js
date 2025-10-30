var myDrop = new Dropzone("#dropzone", {
    paramName: "file",
    // acceptedFiles: "image/jpeg,image/png, .pdf ",
    acceptedFiles: "image/jpeg,image/png,image/pdf,image ",
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