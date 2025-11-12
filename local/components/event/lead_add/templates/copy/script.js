$(document).ready(function() {
    $('#rcBase').select2();
    $("#rcBase").change(function() {
        var rcBaseId = $("#rcBase option:selected").val();
        //$('#rcBaseHTML').html(rcBaseId);
        $.ajax({
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: {type:'baseCheck', rcBaseId:rcBaseId},
            dataType: "json",
            success: function (data) {
                $('#rcBaseHTMLAvito').html(data.avito);
                $('#rcBaseHTMLYandex').html(data.yandex);
            },
            error: function (data) {
                $("#rcBaseHTMLAvito").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#cityAvito').select2();
    $("#cityAvito").change(function() {
        $('#rcNameHTMLAvito').empty();
        $('#residentialComplexAvito').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        $('#buildingsHTMLAvito').empty();
        $('#buildingsAvito').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var cityName = $("#cityAvito option:selected").text();
        var type = 'rc';
        $('#cityNameHTMLAvito').html(cityName);
        $.ajax({
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: {type:type, cityName:cityName},
            dataType: "json",
            success: function (data) {
                if(data.length !== 0) {
                    for(var i=0; i<data.length; i++) { 
                        $('#residentialComplexAvito').append('<option value="'+data[i].rc_id+ '">'+data[i].rc_name+'</option');
                    }
                    $('#residentialComplexAvito').prop('disabled', false);
                }
            },
            error: function (data) {
                $("#cityNameHTMLAvito").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#residentialComplexAvito').select2();
    $("#residentialComplexAvito").change(function() {
        $('#buildingsHTMLAvito').empty();
        $('#buildingsAvito').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var rcName = $("#residentialComplexAvito option:selected").text();
        var rcNameVal = $("#residentialComplexAvito option:selected").val();
        var rcNameFull = "ID "+rcName+" в базе Авито: "+rcNameVal;
        $.ajax({
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: {rcID:rcNameVal, type:"build"},
            dataType: "json",
            success: function (data) {
                if(data.length > 0) {
                    for(var i=0; i<data.length; i++) { 
                        $('#buildingsAvito').append('<option value="'+data[i].b_id+ '">'+data[i].b_name+'</option');
                    }
                    $('#buildingsAvito').prop('disabled', false);
                }
                $('#rcNameHTMLAvito').html(rcNameFull);
            },
            error: function (data) {
                $("#rcNameHTMLAvito").html("wtf! This error! omg! again!");
            },
        });

    });
    $('#buildingsAvito').select2();
    $("#buildingsAvito").change(function() {
        var buildId = $("#buildingsAvito option:selected").val();
        var buildNameFull = "ID корпуса в Авито:" + buildId;
        $('#buildingsHTMLAvito').html(buildNameFull);
    });

    $('#saveToBase').click( function() {
        event.preventDefault();
        var rcBaseId = $("#rcBase option:selected").val();

        var resultAvito = 0
        var resultYandex = 0
        // Авито
        var buildIdAvito = $("#buildingsAvito option:selected").val();
        var rcNameValAvito = $("#residentialComplexAvito option:selected").val();
        
        if(buildIdAvito != 0) {
            resultAvito = buildIdAvito;
        } else {
            resultAvito = rcNameValAvito;
        }

        // Яндекс
        var buildIdYandex = $("#buildingsYandex option:selected").val();
        var rcNameValYandex = $("#residentialComplexYandex option:selected").val();

        if(buildIdYandex != 0) {
            resultYandex = buildIdYandex;
        } else {
            resultYandex = rcNameValYandex;
        }
        $('#feedback').html(rcBaseId);
        $.ajax({
            type: 'POST',
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/avito.php",
            data:{resultAvito:resultAvito, resultYandex:resultYandex, type: "save",rcBaseId: rcBaseId },
            dataType: "json",
            success: function(data) {
                $('#feedback').html(data);
            },
            error: function (data) {
                $("#feedback").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#cityYandex').select2();
    $("#cityYandex").change(function() {
        $('#rcNameHTMLYandex').empty();
        $('#residentialComplexYandex').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        $('#buildingsHTMLYandex').empty();
        $('#buildingsYandex').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var cityName = $("#cityYandex option:selected").text();
        var type = 'rc';
        $('#cityNameHTMLYandex').html(cityName);
        $.ajax({
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/yandex.php",
            type: "POST",
            data: {type:type, cityName:cityName},
            dataType: "json",
            success: function (data) {
                if(data.length !== 0) {
                    for(var i=0; i<data.length; i++) { 
                        $('#residentialComplexYandex').append('<option value="'+data[i].rc_id+ '">'+data[i].rc_name+'</option');
                    }
                    $('#residentialComplexYandex').prop('disabled', false);
                }
            },
            error: function (data) {
                $("#rcNameHTMLYandex").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#residentialComplexYandex').select2();
    $("#residentialComplexYandex").change(function() {
        $('#buildingsHTMLYandex').empty();
        $('#buildingsYandex').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var rcName = $("#residentialComplexYandex option:selected").text();
        var rcNameVal = $("#residentialComplexYandex option:selected").val();
        var rcNameFull = "ID "+rcName+" в базе Яндекса: "+rcNameVal;
        $.ajax({
            url: "https://2223030.xn--p1ai/local/components/cassoft/newBuildings/yandex.php",
            type: "POST",
            data: {rcID:rcNameVal, type:"build"},
            dataType: "json",
            success: function (data) {
                if(data.length > 0) {
                    for(var i=0; i<data.length; i++) { 
                        $('#buildingsYandex').append('<option value="'+data[i].b_id+ '">'+data[i].b_name+'</option');
                    }
                    $('#buildingsYandex').prop('disabled', false);
                }
                $('#rcNameHTMLYandex').html(rcNameFull);
            },
            error: function (data) {
                $("#rcNameHTMLYandex").html("wtf! This error! omg! again!");
            },
        });

    });
    $('#buildingsYandex').select2();
    $("#buildingsYandex").change(function() {
        var buildId = $("#buildingsYandex option:selected").val();
        var buildNameFull = "ID корпуса в Яндексе:" + buildId;
        $('#buildingsHTMLYandex').html(buildNameFull);
    });
});