
$('#create').on('click', function() {
    console.log('click')
    editRecordStage()

});

$('#addRecord').on('click', function() {
    editRecordStage()

});
$(document).on('click', '#panel-close', function() {

    $('#resultCard').hide()
});

function deleteRecordStage(idEl) {
    const result = confirm('Вам действительно хотите деактивировать запись ');
    if (result) {
        let params ={
            ID: idEl,
            ENTITY: 'candidates',
            ACTIVE: 'N',
        }
        console.log("dell")
        console.log(params)
        BX24.callMethod("entity.item.update", params,
            function(result) {
                console.log(result)
                if (result.error()) {
                    console.error(result.error())
                    $('#resultMessage').empty()
                    $('#resultMessage').text("Что то пошло не так, обновите страни и повторите редактирование")
                    $('#resultMessage').show()
                    setTimeout($('#resultMessage').hide(), 2000);
                } else{
                    $('#el-'+ idEl).hide()
                    $('#resultMessage').empty()
                    $('#resultMessage').text("Запись деактивирована")
                    $('#resultMessage').show()
                    setTimeout($('#resultMessage').hide(), 800);
                    console.log("dell+")
                }

            }
        );
    }
}


$('#save').on('click', function() {
    let id = $('#ID').val()
    let color = $('#CS_COLOR').val()
    //  let stage =JSON.stringify($('#CS_STAGE_DEAL option:selected').html())
    let stage =JSON.stringify($('#CS_STAGE_SMART').val())
    // console.log(stage)
//console.log($('#CS_STAGE_SMART option:selected').val())
    let params = {
        ENTITY: 'stage',
        NAME: $('#NAME').val(),
        //  ID: $('#ID').val(),
        PROPERTY_VALUES: {
            CS_TYPE_STAGE: $('#CS_TYPE_STAGE').val(),
            CS_STAGE_SMART: stage,
            CS_COLOR: $('#CS_COLOR').val(),
        }
    }
    var method
    if (id) {
        params['ID'] = id
        method = "entity.item.update"
        console.log("update")
    } else {
        method = "entity.item.add"
    }
    console.log(params)
    BX24.callMethod(method, params,
        function (result) {
            console.log(result)
            if (result.error()) {
                console.error(result.error())
            } else {
                $('#resultCard').hide()
                $("#m-" + id).attr('style', "background:" + color + ";")
                console.log("update+")
            }

        }
    );
});
function refreshWindow(value) {
    $('#main').empty()
    $('#main').html(value)
}

function editRecordStage(value) {

    BX24.init(function(){
        var auth = BX24.getAuth()
        // console.log(auth)
        $.ajax({
            type: "POST",
            url: "/local/components/hr/candidates/ajax/creat.php",
            data: {
                value: value,
                auth:auth,
                app:$('#appCode').val()
            },
            dataType: "html",
            success: function(response) {
                //   $("#overlay_popup_object_card").show();
                $('#resultCard').show()
                $('#resultCard').empty()
                $('#resultCard').html(response)
                $('#panel-close').show()
            },
            error: function(data) {
                console.log(data)
            },
        })
    });
}