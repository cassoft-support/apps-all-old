$(document).ready(function () {
//     let searchStart = $("#searchStart").val()
//     console.log(searchStart)
//     if (searchStart === 'start') {
    searchApp()
//     }
});

function searchApp() {
    $(".btn-link-fa-blue").hide()
    /*  var authParams = BX24.getAuth();
      if (!authParams) {
          BX24.refreshAuth();

          authParams = BX24.getAuth();
      }*/
    let app = $('#apps').val()
    let member_id = $('#member_id').val()
    let client_id = $('#client_id').val()
    let resCode = $('#resCode').val()
    // console.log(authParams)
    console.log(app)
    //  console.log(UserAut)
    $.ajax({
        url: "/local/components/fulfillment/applications/ajax/search.php",
        type: 'POST',
        data: {
            resCode: resCode,
            client_id: client_id,
            app: app,
            member_id: member_id,
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

// $('#create').on('click', function () {
//     edit()
//     console.log("creat")
// });

function edit(id) {
    let app = $('#apps').val()
    let member_id = $('#member_id').val()
    let UserAut = $('#UserAut').val()
    $.ajax({
        url: "/local/components/fulfillment/applications/ajax/edit.php",
        type: 'POST',
        data: {
          //  authParams: authParams,
            app: app,
            member_id: member_id,
            UserAut: UserAut,
            id: id
        },
        dataType: "html",
        success: function (response) {
            console.log("creat2")
             console.log(response)
            $('#slider_card').empty()
            $('#slider_card').html(response)
            $('#slider_card').show()
            $('#panel-close').show()
            $('.slider-card').attr("class", "slider-card slideLeft")
            $('.slider-card').attr("style", "width: 960px; background: var(--bg-form);")
            $('#report').hide()
            // $('#main').show()

        },
        error: function (data) {
            console.log(data)
        },
    })
}


function deleteRecord(id) {
    if (!confirm('Деактивировать заявку ' + id + '?')) {
        return;
    }

    var authParams = BX24.getAuth();
    if (!authParams) {
        BX24.refreshAuth();
        authParams = BX24.getAuth();
    }
    let app = $('#apps').val();
    let UserAut = $('#UserAut').val();
    $.ajax({
        url: "/local/components/fulfillment/applications/ajax/deactivate.php",
        type: 'POST',
        data: {
            authParams: authParams,
            app: app,
            UserAut: UserAut,
            id: id
        },
        dataType: "html",
        success: function (response) {
            response = JSON.parse(response);
            if (response.error) {
                alert(response.error);
            }
            if (response.message) {
                alert(response.message);
            }
            if (response.success) {
                // удалить строку из таблицы
                $('#fresh-table').bootstrapTable('removeByUniqueId', id);
            }
        },
        error: function (data) {
            console.log(data)
        },
    })
}

$(document).on('click', '#panel-close', function () {
    $('#slider_card').empty()
    $('#slider_card').hide()
    $("#report").show();
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