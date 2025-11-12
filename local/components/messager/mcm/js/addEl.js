var myEditorDesc = null;
$(document).ready(function () {
    $('.js-chosen').chosen({
        width: '100%',
        no_results_text: 'Совпадений не найдено',
        placeholder_text_single: ' ',
        Placeholder_text_multiple: 'Выберите значения++',
        max_selected_options: 5,
        disable_search_threshold: 5
    });
     ClassicEditor
             .create(document.querySelector('#editorGroup'), {
                 toolbar: [
                     'bold', 'italic', '|',
                     'bulletedList', 'numberedList', 'todoList',
                     '-', // break point

                     '-', // break point
                     'insertTable', '|',
                     'uploadImage', 'blockQuote', '|',
                     'link', 'unlink',
                     'undo', 'redo'
                 ]
             })
             .then(editor => {
                 myEditorDesc = editor;
             })
             .catch(error => {
                 console.error(error);
             });
})
    // отправить форму


    $('#uploadMessage').click(function () {
        $('#uploadMessage').hide(); // скрыть кнопку
        console.log('start')

        // var map = new Map()
        // let elements = $('.group-field-input')
        //     .map(function() {
        //         map[this.id] = $(this).val()
        //     })
        //     .get()

        let desc =  myEditorDesc.getData();
        if($('#NAME').val()) {
            $.ajax({
                url: "/local/components/massenger/messager/ajax/save.php",
                type: 'POST',
                data: {
                    app: $('#app').val(),
                    member_id: $('#member_id').val(),
                    name: $('#NAME').val(),
                    messager_type: $('#messager_type').val(),
                    desc:desc,
                    id: $('#id').val(),
                },
                dataType: "json",
                success: function (response) {
                    console.log(response)
                    $('#notification').hide()
                    if (response.error) {
                        alert(response.error);
                    } else {
                        alert('Изменения сохранены')
                        console.log('response.ID')
                        console.log(response.ID)
                        if (response.ID) {
                            console.log('response.ID2')
                            $('#id').attr('value', response.ID);
                            $('#uploadMessage').html('Сохранить');
                            $('#header').html("РЕДАКТИРОВАНИЕ РАССЫЛКИ №" + response.ID);
                        }

                    }
                    if (response.message) {
                        alert(response.message);
                    }
                    if (response.errorCode === 'update_error') {
                        alert('Не удалось обновить алгоритм или его часть.');
                    }
                    // показать кнопку
                    $('#uploadMessage').show();
                },
                error: function (response) {
                    console.log(response)
                    $('#uploadMessage').show(); // показать кнопку
                },
            })
        }else{
            alert('Не заполнено название алгоритма')
            $('#uploadMessage').show(); // показать кнопку
        }
    })

