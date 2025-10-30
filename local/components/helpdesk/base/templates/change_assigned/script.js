// $(document).ready(function () {
//
// });

function entityUpdate(id) {
  //  $("#saveButton").hide()

    let CS_LEAD = JSON.stringify(getCheckedByClass('lead'))
    let CS_DEAL = JSON.stringify(getCheckedByClass('deal'))
    let CS_CONTACT = JSON.stringify(getCheckedByClass('contact'))
    let CS_COMPANY = JSON.stringify(getCheckedByClass('company'))
    let CS_QUOTE = JSON.stringify(getCheckedByClass('quote'))

    BX24.callMethod('entity.item.update', {
            ENTITY: 'setup',
            ID: id,
            PROPERTY_VALUES: {
                CS_DEAL:CS_DEAL,
                CS_LEAD:CS_LEAD,
                CS_CONTACT:CS_CONTACT,
                CS_COMPANY:CS_COMPANY,
                CS_QUOTE:CS_QUOTE,
                CS_SMART:'',
                CS_INVOICE:'',
            },
        },
        function (result) {
            if (result.error())
                console.error(result.error());
            else
                alert("Запись обновлена");
           // $("#saveButton").show()
        }
    );


}



function getCheckedByClass(className) {
    // Получаем все чекбоксы с указанным классом
    const checkboxes = document.querySelectorAll(`.${className}`);
    const checkedValues =  new Object();
    // Перебираем чекбоксы и добавляем отмеченные в массив
    checkboxes.forEach((checkbox) => {
        const name = checkbox.getAttribute('name');
        const isChecked = checkbox.checked;
             checkedValues[name] = isChecked ;
    });

    return checkedValues;
}