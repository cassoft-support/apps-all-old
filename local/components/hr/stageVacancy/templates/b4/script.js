var $table = $('#fresh-table'),
    //  $alertBtn = $('#alertBtn'),
    full_screen = true;

$().ready(function() {
    $table.bootstrapTable({
        toolbar: ".toolbar",
        toolbarAlign: 'right',
        clickToSelect: true,
        singleSelect: true,
        showRefresh: false,
        search: true,
        showToggle: false,
        showColumns: true,
        checkbox: true,
        checkboxEnabled: true,
        pagination: true,
        striped: true,
        pageSize: 25,
        pageList: [5, 10, 25, 50, 100],
        searchAlign: 'left',

        formatShowingRows: function(pageFrom, pageTo, totalRows) {

        },
        formatRecordsPerPage: function(pageNumber) {
            return pageNumber + " записей на страницу";
        },
        icons: {
            refresh: 'fa fa-refresh',
            toggle: 'fa fa-th-list',
            columns: 'fa fa-cog fa-2x',
            detailOpen: 'fa fa-plus-circle',
            detailClose: 'fa fa-minus-circle'
        }
    });

    $(window).resize(function() {
        $table.bootstrapTable('resetView');
    });


    window.operateEvents = {

        'click .edit': function(e, value, row, index) {
            alert('You click edit icon, row: ' + JSON.stringify(row));
            console.log(value, row, index);
        },
        'click .remove': function(e, value, row, index) {
            $table.bootstrapTable('remove', {
                field: 'id',
                values: [row.id]
            });

        }
    };

    //   $alertBtn.click(function() {


});

$('#create').on('click', function() {
    $.ajax({
        type: 'POST',
        url: "/local/components/brokci_settings/favouritesStage/ajax/create.php",
        data: {
            type: 'addRecord',
        },
        dataType: "html",
        success: function(response) {
            //    console.log(response)
            if (response) {
                $('#resultCard').empty()
                    //   $("#overlay_popup_object_card").show();
                $('#resultCard').html(response)
                $('#resultCard').show()
            } else {
                console.log("Что то пошло не так")
                $('#resultMessage').empty()
                $('#resultMessage').text("Что то пошло не так, обновите страни и повторите редактирование")
                $('#resultMessage').show()
                setTimeout(refreshWindow, 2000, response);
            }
        },
        error: function(data) {
            console.log(data)
        },
    })
});

$('#addRecord').on('click', function() {
    var map = new Map()
    let elements = $('.fields-input')
    elements.map(function() {
            map[this.id] = $(this).val()
        })
        .get()
    console.log(map)
    $.ajax({
        type: 'POST',
        url: "/local/components/brokci_settings/favouritesStage/ajax/addRecord.php",
        data: map,
        dataType: "html",
        success: function(response) {
            console.log(response)
            if (response) {
                console.log("запись Создана")
                $('#resultCard').empty()
                $('#resultMessage').text("Запись создана")
                $('#resultMessage').show()
                setTimeout(refreshWindow, 1000, response);
            } else {
                console.log("Что то пошло не так")
                $('#resultMessage').empty()
                $('#resultMessage').text("Что то пошло не так, обновите страни и повторите редактирование")
                $('#resultMessage').show()
                setTimeout(refreshWindow, 2000, response);
            }
        },
        error: function(data) {
            console.log(data)
        },
    })
});
$('#Close').on('click', function() {
    console.log("close")
    $('#resultCard').empty()
    $('#resultCard').hide()
    $("#overlay_popup_object_card").hide();
});

/*
            function operateFormatter(value, row, index) {
                return [

                    '<a rel="tooltip" title="Edit" id="edit" class="table-action edit" href="javascript:void(0)" >',
                    '<i class="fa fa-edit"></i>',
                    '</a>',
                    '<a rel="tooltip" title="Remove" class="table-action remove" href="javascript:void(0)" title="Remove">',
                    '<i class="fa fa-remove"></i>',
                    '</a>'
                ].join('');
            }
*/
function deleteRecord(id) {
    const result = confirm('Вам действительно хотите деактивировать запись ');
    if (result) {
        let UserAut = $('#UserAutPlan').val();
        $.ajax({
            type: 'POST',
            url: "/local/components/brokci_settings/favouritesStage/ajax/delete.php",
            data: {
                id: id,
                UserAutPlan: UserAut,
            },
            dataType: "html",
            success: function(response) {
                console.log(response)
                if (response) {
                    $('#resultMessage').empty()
                    $('#resultMessage').text("Запись деактивирована")
                    $('#resultMessage').show()
                    setTimeout(refreshWindow, 800, response);
                } else {
                    console.log("Что то пошло не так")
                    $('#resultMessage').empty()
                    $('#resultMessage').text("Что то пошло не так, обновите страни и повторите редактирование")
                    $('#resultMessage').show()
                    setTimeout(refreshWindow, 2000, response);
                }
            },
            error: function(data) {
                console.log(data)
            },
        })
    }
}

$('#save').on('click', function() {
    var map = new Map()
    let elements = $('.fields-input')
    elements.map(function() {
            map[this.id] = $(this).val()
        })
        .get()
    $.ajax({
        url: '/local/components/brokci_settings/favouritesStage/ajax/save.php',
        type: 'POST',
        data: map,
        dataType: "html",
        success: function(response) {
            if (response) {
                $('#resultCard').empty()
                $('#resultMessage').empty()
                $('#resultMessage').text("Запись сохранена")
                $('#resultMessage').show()
                setTimeout(refreshWindow, 800, response);
            } else {
                console.log("Что то пошло не так")
                $('#resultMessage').empty()
                $('#resultMessage').text("Что то пошло не так, обновите страни и повторите редактирование")
                $('#resultMessage').show()
                setTimeout(refreshWindow, 2000, response);
            }
        },
        error: function(data) {
            console.log(data)
        },
    })
})

function refreshWindow(value) {
    $('#main').empty()
    $('#main').html(value)
}

function editRecord(value) {
    let planTypeQ = $('#planTypeQ').val();
    $.ajax({
        url: "/local/components/brokci_settings/favouritesStage/ajax/edit.php",
        data: {
            value: value,
            planType: planTypeQ,
        },
        dataType: "html",
        success: function(response) {
            //   $("#overlay_popup_object_card").show();
            $('#resultCard').empty()
            $('#resultCard').html(response)
            $('#resultCard').show()
        },
        error: function(data) {
            console.log(data)
        },
    })
}
$(document).on('click', '#panel-close', function() {
    $('#overlay_popup_object_card').hide()
    $('#resultCard').hide()
});