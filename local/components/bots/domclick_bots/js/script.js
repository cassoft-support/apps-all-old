 $(document).ready(function () {
//     let searchStart = $("#searchStart").val()
//     console.log(searchStart)
//     if (searchStart === 'start') {
     searchMessage()
//     }
 });

function searchMessage() {
    $(".btn-link-fa-blue").hide()
     var authParams = BX24.getAuth();
      if (!authParams) {
          BX24.refreshAuth();
          authParams = BX24.getAuth();
      }
    $.ajax({
        url: "/local/components/massenger/messager/ajax/search.php",
        type: 'POST',
        data: {
            authParams:authParams,
            app: $('#app').val(),
            member_id: $('#member_id').val(),
        },
        dataType: "html",
        success: function (response) {
            //     console.log(response)
            $('#report').empty()
            $('#report').html(response)
            // $('#main').show()
            $(".btn-link-fa-blue").show()

            // спрятать ненужные колонки
            // найти все колонки
            let arColumns = $('#fresh-table').find('th');
             console.log(arColumns)
            // перебрать найденные колонки
            arColumns.each(function (i, elem) {
             //   hideColumn(elem);
            });
          //  resizeFrame();
        },
        error: function (data) {
            console.log(data)
        },
    })
}

$('#create').on('click', function () {
    edit()
    console.log("creat")
});

function edit(id) {
    $.ajax({
        url: "/local/components/massenger/messager/ajax/edit.php",
        type: 'POST',
        data: {
          //  authParams: authParams,
            app: $('#app').val(),
            member_id: $('#member_id').val(),
            id: id
        },
        dataType: "html",
        success: function (response) {
            console.log("creat2")
            // console.log(response)
            $('#slider_card').empty()
            $('#slider_card').html(response)
            $('#slider_card').show()
            $('#panel-close').show()
            $('.slider-card').attr('class', "slider-card slideLeft")
            $('.slider-card').attr('style', "width: 960px; background: var(--bg-form);")

            $('#report').hide()
            // $('#main').show()

        },
        error: function (data) {
            console.log(data)
        },
    })
}


function deleteRecord(id) {
    if (!confirm('Деактивировать рассылку ' + id + '?')) {
        return;
    }
    BX24.callMethod(
        'entity.item.update',
        {
            ENTITY: 'messages',
            ID: id,
            DATE_ACTIVE_FROM: new Date(),
            ACTIVE:'N'
        },
        function(result)
        {
            if(result.error())
                console.log(result.error());
            else
                console.log("Элемент " + id+" деактивирован");
            $('#fresh-table').bootstrapTable('removeByUniqueId', id);
        }
    );
}

$(document).on('click', '#panel-close', function () {
    console.log('panel-close')
    $('#slider_card').empty()
    $('#slider_card').hide()
    $("#report").show()
});

// let arDefaultFieldsApp = [
//     'actions',
//     'ID',
//     'NAME',
//     'LEAD_ID',
//     'DEAL_ID',
//     'COMPANY_ID',
//     'SMART_ID',
//     'MARKETS',
//     //'ASSIGNED',
// ];
//
function hideColumn(element) {
    let fieldName = $(element).data('field');
    let needHide = true;
    // проверка, не содержится ли название поля в массиве полей показываемых по умолчанию
    arDefaultFieldsApp.forEach(function (value) {
            if (fieldName === value) {
                needHide = false;
            }
        }
    );
    if (needHide) {
        // спрятать колонку
        $table.bootstrapTable('hideColumn', fieldName)
    }
}