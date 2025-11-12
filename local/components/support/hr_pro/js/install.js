function install  (request) {
  console.log(request)
  $.ajax({
    url: "/local/components/install/base/ajax/ajax_install.php",
    type: "POST",
    data: request,
    dataType: "text",
    success: function(response) {
      console.log(response)
      if(response=='yes'){
      $('.finishInstallBlock').html('Приложение установлено')
      $('.finishInstallBlock').show()
      setTimeout(BX24.installFinish(), 5000);
      }
    },
    error: function(html) {
      $('.finishInstallBlock').html(
          "Технические неполадки! Попробуйте перезагрузить страницу"
      );
      $('.finishInstallBlock').show()
  },
  })
}
