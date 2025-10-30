  ///--------------------------------



  function setRegion(data) {
      let region_selector = $('#region_name').get('0')
      data.forEach(element => {
          let option = document.createElement('option')
          option.setAttribute('value', element['ID'])
          option.setAttribute('data-name', element['UF_CS_D_NAME'])
          option.innerHTML = element['UF_CS_D_NAME']
          region_selector.appendChild(option)
      });
  }
  /*
    function setCities(data) {
        let cities_selector = $('#city_name').get('0')
        data.forEach(element => {
            let option = document.createElement('option')
            option.setAttribute('value', element['ID'])
            option.setAttribute('data-full', element['UF_FULL_NAME'])
            option.setAttribute('data-type', element['UF_TYPE'])
            option.setAttribute('data-type-short', element['UF_TYPE_SHORT'])
            option.setAttribute('data-name', element['UF_NAME'])
            option.setAttribute('data-long', element['UF_CS_CITY_LONG'])
            option.setAttribute('data-lat', element['UF_CS_CITY_LAT'])
            option.innerHTML = element['UF_SHORT_NAME']
            cities_selector.appendChild(option)
        });
    }
  */


  function getRegion() {
      let country_id = $('#country_name option:selected').val()
      let country_code = $('#country_name option:selected').attr('data-code')
          //   console.log(country_id)
          //   console.log(country_code)
      $.ajax({
          url: '/pub/cassoftApp/brokci/tools/settings.php',
          type: 'POST',
          data: {
              getInfo: 'region',
              country_id: country_id,
              country_code: country_code

          },
          dataType: 'json',
          success: function(data) {
              //      console.log(data)
              setRegion(data)
          },
          error: function(data) {
              console.log(data)
          },
      })
  }

  /*
    function getCity() {

        let region_id = $('#region_name option:selected').val()
        let country_code = $('#country_name option:selected').attr('data-code')
        $.ajax({
            url: '/pub/cassoftApp/realEstateObject/tools/settings.php',
            type: 'POST',
            data: {
                getInfo: 'cities',
                //country_id: country_id,
                region_id: region_id,
                country_code: country_code
            },
            dataType: 'json',
            success: function(data) {
                //     console.log(data)
                setCities(data)
            },
            error: function(data) {
                console.log(data)
            },
        })
    }
  */
  $('#country_name').change(function() {

          let value = $('#country_name').val()
          if (value != '') {
              getRegion()
              $('#region_block').show()
          }
      })
      /*
      $('#region_name').change(function() {
          let value = $('#region_name').val()
          if (value != '') {
              //  $('#city_block').empty()
              getCity()
              $('#city_block').show()

          }
      })
      */