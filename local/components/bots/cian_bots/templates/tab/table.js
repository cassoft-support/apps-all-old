var $table = $('#fresh-table'),
    //  $alertBtn = $('#alertBtn'),
    full_screen = true;

$().ready(function () {
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
        pageList: [5, 10, 25, 50, 100, 500],
        searchAlign: 'left',

        formatShowingRows: function (pageFrom, pageTo, totalRows) {

        },
        formatRecordsPerPage: function (pageNumber) {
            return pageNumber + " записей на страницу";
        },
        icons: {
            refresh: 'fa fa-refresh',
            toggle: 'fa fa-th-list',
            columns: 'fa fa-cog ',
            detailOpen: 'fa fa-plus-circle',
            detailClose: 'fa fa-minus-circle'
        }
    });

        $(window).resize(function () {
            $table.bootstrapTable('resetView');
        });


        window.operateEvents = {
/*
            'click #addNew': function (e, value, row, index) {
                alert('You click edit icon, row: ' + JSON.stringify(row));
                console.log(value, row, index);
            },

 */
            'click .like': function (e, value, row, index) {
                alert('You click edit icon, row: ' + JSON.stringify(row));
                console.log(value, row, index);
            },
            'click .remove': function (e, value, row, index) {
                $table.bootstrapTable('remove', {
                    field: 'id',
                    values: [row.id]
                });

            }
        };

    //   $alertBtn.click(function() {


});
/*
function check_uncheck_all() {
    console.log("check")
    var frm = document.editnews;
    for (var i = 0; i < frm.elements.length; i++) {
        var elmnt = frm.elements[i];
        if (elmnt.type == 'checkbox') {
            if (frm.master_box.checked == true) { elmnt.checked = false; } else { elmnt.checked = true; }
        }
    }
    if (frm.master_box.checked == true) { frm.master_box.checked = false; } else { frm.master_box.checked = true; }
}

$('#checkAll').click(function() {
    console.log("check")
    if ($(this).is(':checked')) {
        $('#controls input:checkbox').prop('checked', true);
    } else {
        $('#controls input:checkbox').prop('checked', false);
    }
});
*/