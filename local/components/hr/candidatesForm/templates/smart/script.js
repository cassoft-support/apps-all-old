var user = []
var auth = []
BX24.init(function () {
    BX24.callMethod('user.current', {}, function (res) {
        user = res.data()
        //  console.log(user)
    });
    auth = BX24.getAuth()

});


$(document).ready(function() {
    $('[data-fancybox="gallery"]').fancybox({
        loop: true,
        autoFocus: false
    });
    let control = $('#controlRes').val()
    $("#carCard").hide()
    $("#financeForm").hide()
    //   console.log(driversId)
    if(control === 'Y'){

    }else{

        }
      //  applicationCard($('#smartElId').val())
        //  DriversCard()


    setTimeout(resizeFrame(), 3000)
    // $(window).on('load', function () {
    //     $('.preloader').addClass("preloader-remove");
    //     console.log('remove')
    // });
});

$('.mask-card-number').mask('9999 9999 9999 9999');

$('#Active').click(function() {
    let csActive = "N"
    if ($(this).is(':checked') === true) {
        csActive = "Y"
    }
    let smartId= $('#smartId').val()
    let fields = Array()
    fields["ufCrm"+smartId+"CsActive"]=csActive;
    console.info(fields);
    BX24.callMethod(
        "crm.item.update",
        { entityTypeId:$('#entityTypeId').val(),
            id: $('#smartElId').val(),
            fields:{
                ufCrm18CsActive: csActive
            },
        },
        function (result) {
            if (result.error())
                console.log(result.error());
            else {
                console.log(result.data());
            }
        }
    );
    //  }
})
$(document).on('click', '#startBP', function () {

    let smartId = "ufCrm" + $("#smartId").val() + "CsDateChange"
    $.ajax({
        url: '/local/components/event/smart_field/templates/logistics_pro/startBp.php', //copy запрос
        type: "POST",
        data: {
            entityTypeId: $("#entityTypeId").val(),
            smartElId: $("#smartElId").val(),
            smartId: smartId,
            auth: auth,
            app: 'logistics_pro',

        },
        dataType: "json",
        success: function (response) {
            console.log(response)
        }
    })
})

$(document).on('click', '#copyApp', function () {
    let app = 'logistics_pro'
    let appId = $('#APP_ID').val()
    BX24.callMethod('user.current', {}, function (res) {
        var  userCurrent = res.data()
        $('.preloader').attr("class","preloader preloader-add");
        $("#copyApp").hide()
        $.ajax({
            url: '/local/components/logistics/applications/ajax/copyApp.php', //copy запрос
            type: "POST",
            data: {
                userId:userCurrent.ID,
                auth: auth,
                app: app,
                appId:appId
            },
            dataType: "json",
            success: function (response) {
                //   response = JSON.parse(response);
                $(".preloader").attr("class", "preloader preloader-remove")
                console.log(response)
                if (response.error) {
                    alert(response.error);
                    $("#copyApp").show()
                }
                if (response.message) {
                    alert(response.message);
                }
            },
            error: function (html) {
            },
        })
    })
})

function providerFormOpen(){
    $("#providerForm").show()
    $("#infoCard").hide()
    resizeFrame()
}

function resizeFrame() {
    var currentSize = BX24.getScrollSize();
    //  console.log(currentSize)
    minHeight = currentSize.scrollHeight;
    var FrameWidth = document.getElementById("workarea").offsetWidth;
    // console.log(FrameWidth)
    if (minHeight < 300){
        frameHeight = 200;
    } else{
        frameHeight = minHeight+75;
    }
    //  console.log(frameHeight)
    BX24.resizeWindow(FrameWidth, frameHeight);
}


function applicationCard(smartID){
    console.log(smartID)
    BX24.callMethod(
        "entity.item.get",
        {
            'ENTITY': "application",
            'sort': {},
            'filter': {
                'ACTIVE': 'Y',
                'PROPERTY_UF_CS_EX': smartID,
            }
        },
        function(result) {
            if (result.error())
                console.error(result.error());
            else
                    console.dir(result.data());
                var application = Array()
            //   $("#providerBlock").hide()
            application = result.data()[0]
            //    console.log(application)
            if (application !== undefined) {
                //    console.log('application')
                let appProp = application['PROPERTY_VALUES']
                $("#requestInfo").show()
                $("#requestAdd").hide()
                $("#NAME").html(application['NAME'])
                $("#APP_ID").val(application['ID'])

                let vat = new Intl.NumberFormat('ru-RU').format(appProp['UF_CS_OFFER_CASTOMER_VAT'])
                let novat = new Intl.NumberFormat('ru-RU').format(appProp['UF_CS_OFFER_CASTOMER_NOVAT'])
                let cash = new Intl.NumberFormat('ru-RU').format(appProp['UF_CS_OFFER_CASTOMER_CASH'])
                let dateType = Array()
                dateType = {
                    'from-date': "С даты",
                    'ready': "Готов",
                    'permanent': "Постоянно",
                    'rate-request': "Груза нет, запрос ставки",
                }
                if (appProp['UF_CS_BODY']) {
                    let carBodyTypesG = JSON.parse($("#carBodyTypes").val()) // типы кузова
                    let carBodyTypes = ""
                    let arCarBodyTypes = appProp['UF_CS_BODY'].split(',')
                    for (let key in arCarBodyTypes) {
                        carBodyTypes = carBodyTypes + carBodyTypesG[arCarBodyTypes[key]] + ", "
                    }
                    $("#UF_CS_BODY").html(carBodyTypes)
                }

                if (appProp['UF_CS_TYPE_LOADING']) {
                    let carBodyLoadingTypesG = JSON.parse($("#carBodyLoadingTypes").val()) // варианты загрузки
                    let carBodyLoadingTypes = ""
                    let arCarBodyLoadingTypes = appProp['UF_CS_TYPE_LOADING'].split(',')
                    for (let key in arCarBodyLoadingTypes) {
                        carBodyLoadingTypes = carBodyLoadingTypes + carBodyLoadingTypesG[arCarBodyLoadingTypes[key]] + ", "
                    }
                    $("#UF_CS_TYPE_LOADING").html(carBodyLoadingTypes)
                }
                if (appProp['UF_CS_TYPE_UNLOAD']) {
                    let carBodyUnloadingTypesG = JSON.parse($("#carBodyUnloadingTypes").val())// варианты разгрузки
                    let carBodyUnLoadingTypes = ""
                    let arCarBodyUnLoadingTypes = appProp['UF_CS_TYPE_UNLOAD'].split(',')
                    for (let key in arCarBodyUnLoadingTypes) {
                        carBodyUnLoadingTypes = carBodyUnLoadingTypes + carBodyUnloadingTypesG[arCarBodyUnLoadingTypes[key]] + ", "
                    }
                    // UF_CS_TYPE_LOADING_ALL
                    //  UF_CS_TYPE_UNLOAD_ALL
                    $("#UF_CS_TYPE_UNLOAD").html(carBodyUnLoadingTypes)
                }
                if(appProp['UF_CS_STYLE_ATI'] === '1'){
                    $("#statusATI").attr('style', "background: #0ec21fbd; border: 1px solid #0ec21fbd;")
                }
                if(appProp['UF_CS_STYLE_ATI'] === '2'){
                    $("#statusATI").attr('style', "background: #c2320e99; border: 1px solid #e4311b;")
                }
                $("#UF_CS_OFFER_CASTOMER_VAT").html(vat + " ₽.")
                $("#UF_CS_OFFER_CASTOMER_NOVAT").html(novat + " ₽.")
                $("#UF_CS_OFFER_CASTOMER_CASH").html(cash + " ₽.")
                $("#UF_CS_START_DATE").html(dateType[appProp['UF_CS_LOADING_DATES_TYPE']] + " - " + appProp['UF_CS_START_DATE'])


                resizeFrame()
                //  if(company['PHONE'][0]['VALUE']){
                //   $("#providerTel").html(company['PHONE'][0]['VALUE'])
            } else {
                $("#requestInfo").hide()
                $("#requestAdd").show()
            }

        }
    );
}

function candidatesEdit() {
    BX24.init(
        function() {
            parCode={
                smartId:$("#smartElId").val(),
                app:$("#app").val(),
                appType:"candidates",
                candidateId:$("#candidateId").val()
            }

             console.log(parCode)
             console.log('/marketplace/view/cassoft.'+$("#app").val()+'/?params='+JSON.stringify(parCode))
            BX24.openPath(
                '/marketplace/view/cassoft.'+$("#app").val()+'/?params='+JSON.stringify(parCode),
                function(result) {
                    console.log(result);
                    if(result['result'] === 'close'){
                        console.log('close');
                        location.reload();
                    }
                }
            );
        }
    );
}

