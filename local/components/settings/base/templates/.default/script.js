$(document).ready(function () {
    $('#rcBase').select2();
    $("#rcBase").change(function () {
        var rcBaseId = $("#rcBase option:selected").val();
        //$('#rcBaseHTML').html(rcBaseId);
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: { type: 'baseCheck', rcBaseId: rcBaseId },
            dataType: "json",
            success: function (data) {
                $('#rcBaseHTMLAvitoRC').html(data.avitoRC);
                $('#rcBaseHTMLAvitoBuild').html(data.avitoBuild);
                $('#rcBaseHTMLYandexRC').html(data.yandexRC);
                $('#rcBaseHTMLYandexBuild').html(data.yandexBuild);
                $('#rcBaseHTMLCianRC').html(data.cianRC);
                $('#rcBaseHTMLCianBuild').html(data.cianBuild);
            },
            error: function (data) {
                $("#rcBaseHTMLAvito").html("wtf! This error! omg! again!");
            },
        });
    });
    $('#region').select2();
    $("#region").change(function () {
        var region = $("#region option:selected").text();
        //$('#rcBaseHTML').html(rcBaseId);
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/city.php",
            type: "POST",
            data: { type: 'region', region: region },
            dataType: "json",
            success: function (data) {

                $('#cityAvito').children('option:not(:first)').remove()
                let avito = data.avitoCity
                for (var i = 0; i < avito.length; i++) {
                    $('#cityAvito').append('<option value="' + avito[i] + '">' + avito[i] + '</option');
                }
                $('#cityYandex').children('option:not(:first)').remove()
                let yandex = data.yandexCity
                for (var i = 0; i < yandex.length; i++) {
                    $('#cityYandex').append('<option value="' + yandex[i] + '">' + yandex[i] + '</option');
                }
                $('#cityCian').children('option:not(:first)').remove()
                let cian = data.cianCity
                for (var i = 0; i < cian.length; i++) {
                    $('#cityCian').append('<option value="' + cian[i] + '">' + cian[i] + '</option');
                }
            },
            error: function (data) {
                $("#rcBaseHTMLAvito").html("wtf! This error! omg! again!");
            },
        });
    });




    $('#cityAvito').select2();
    $("#cityAvito").change(function () {
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
        var region = $("#region option:selected").text();
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: { type: type, cityName: cityName, region: region },
            dataType: "json",
            success: function (data) {
                if (data.length !== 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#residentialComplexAvito').append('<option value="' + data[i].rc_id + '">' + data[i].rc_name + '</option');
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
    $("#residentialComplexAvito").change(function () {
        $('#buildingsHTMLAvito').empty();
        $('#buildingsAvito').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var rcName = $("#residentialComplexAvito option:selected").text();
        var rcNameVal = $("#residentialComplexAvito option:selected").val();
        var rcNameFull = "ID " + rcName + " в базе Авито: " + rcNameVal;
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/avito.php",
            type: "POST",
            data: { rcID: rcNameVal, type: "build" },
            dataType: "json",
            success: function (data) {
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#buildingsAvito').append('<option value="' + data[i].b_id + '">' + data[i].b_name + '</option');
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
    $("#buildingsAvito").change(function () {
        var buildId = $("#buildingsAvito option:selected").val();
        var buildNameFull = "ID корпуса в Авито:" + buildId;
        $('#buildingsHTMLAvito').html(buildNameFull);
    });

    $('#saveToBase').click(function () {
        event.preventDefault();
        var rcBaseId = $("#rcBase option:selected").val();

        var resultAvito
        var resultAvitoBuild
        var resultYandex
        var resultYandexBuild
        var resultCian
        var resultCianBuild
        // Авито
        resultAvitoBuild = $("#buildingsAvito option:selected").val();
        resultAvito = $("#residentialComplexAvito option:selected").val();



        // Яндекс
        resultYandexBuild = $("#buildingsYandex option:selected").val();
        resultYandex = $("#residentialComplexYandex option:selected").val();


        // Cian
        var resultCianBuild = $("#buildingsCian option:selected").val();
        var resultCian = $("#residentialComplexCian option:selected").val();

        $('#feedback').html(resultCian);
        $('#feedback').html(rcBaseId);
        $.ajax({
            type: 'POST',
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/avito.php",
            data: {
                resultAvito: resultAvito, resultAvitoBuild: resultAvitoBuild,
                resultYandex: resultYandex, resultYandexBuild: resultYandexBuild,
                resultCian: resultCian, resultCianBuild: resultCianBuild,
                type: "save",
                rcBaseId: rcBaseId
            },
            dataType: "json",
            success: function (data) {
                $('#feedback').html(data);
            },
            error: function (data) {
                $("#feedback").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#cityYandex').select2();
    $("#cityYandex").change(function () {
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
        var region = $("#region option:selected").text();
        $('#cityNameHTMLYandex').html(cityName);
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/yandex.php",
            type: "POST",
            data: { type: type, cityName: cityName, region: region },
            dataType: "json",
            success: function (data) {
                if (data.length !== 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#residentialComplexYandex').append('<option value="' + data[i].rc_id + '">' + data[i].rc_name + '</option');
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
    $("#residentialComplexYandex").change(function () {
        $('#buildingsHTMLYandex').empty();
        $('#buildingsYandex').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var rcName = $("#residentialComplexYandex option:selected").text();
        var rcNameVal = $("#residentialComplexYandex option:selected").val();
        var rcNameFull = "ID " + rcName + " в базе Яндекса: " + rcNameVal;
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/yandex.php",
            type: "POST",
            data: { rcID: rcNameVal, type: "build" },
            dataType: "json",
            success: function (data) {
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#buildingsYandex').append('<option value="' + data[i].b_id + '">' + data[i].b_name + '</option');
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
    $("#buildingsYandex").change(function () {
        var buildId = $("#buildingsYandex option:selected").val();
        var buildNameFull = "ID корпуса в Яндексе:" + buildId;
        $('#buildingsHTMLYandex').html(buildNameFull);
    });

    // Cian
    $('#cityCian').select2();
    $("#cityCian").change(function () {
        $('#rcNameHTMLCian').empty();
        $('#residentialComplexCian').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        $('#buildingsHTMLCian').empty();
        $('#buildingsCian').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var cityName = $("#cityCian option:selected").text();
        var type = 'rc';
        var region = $("#region option:selected").text();
        $('#cityNameHTMLCian').html(cityName);
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/cian.php",
            type: "POST",
            data: { type: type, cityName: cityName, region: region },
            dataType: "json",
            success: function (data) {
                if (data.length !== 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#residentialComplexCian').append('<option value="' + data[i].rc_id + '">' + data[i].rc_name + '</option');
                    }
                    $('#residentialComplexCian').prop('disabled', false);
                }
            },
            error: function (data) {
                $("#rcNameHTMLCian").html("wtf! This error! omg! again!");
            },
        });
    });

    $('#residentialComplexCian').select2();
    $("#residentialComplexCian").change(function () {
        $('#buildingsHTMLCian').empty();
        $('#buildingsCian').find('option:not(:first)')
            .remove()
            .end()
            .prop('disabled', true);
        var rcName = $("#residentialComplexCian option:selected").text();
        var rcNameVal = $("#residentialComplexCian option:selected").val();
        var rcNameFull = "ID " + rcName + " в базе Циан: " + rcNameVal;
        $.ajax({
            url: "https://city.brokci.ru/local/components/cassoft/newBuildings/cian.php",
            type: "POST",
            data: { rcID: rcNameVal, type: "build" },
            dataType: "json",
            success: function (data) {
                if (data.length > 0) {
                    for (var i = 0; i < data.length; i++) {
                        $('#buildingsCian').append('<option value="' + data[i].b_id + '">' + data[i].b_name + '</option');
                    }
                    $('#buildingsCian').prop('disabled', false);
                }
                $('#rcNameHTMLCian').html(rcNameFull);
            },
            error: function (data) {
                $("#rcNameHTMLCian").html("wtf! This error! omg! again!");
            },
        });

    });
    $('#buildingsCian').select2();
    $("#buildingsCian").change(function () {
        var buildId = $("#buildingsCian option:selected").val();
        var buildNameFull = "ID корпуса в Циан:" + buildId;
        $('#buildingsHTMLCian').html(buildNameFull);
    });
});
