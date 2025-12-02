import { B24Frame } from '@bitrix24/b24jssdk';
import { wappGet } from '@/services/cs-wappi'

export async function execute($b24: B24Frame) {
  try {
   // const url = '/api/sync/chats/days/get?profile_id=92bfec8e-80b7&offset=0&limit=10'
   //
   //  const response = await wappGet(url)

    // console.log('Проверка наличия метода:', $b24.callMethod);
    // console.log('Параметры вызова:', {
    //   method: 'imconnector.get',
    //   params: { CONNECTOR: 'cs_mcm_whatsApp' },
    // });
      const data=[
//     {
//   "PROPERTY": "crm",
//   "NAME": "Привязка к сущности",
//   "TYPE": "S",
//   "SORT": "500"
// },
//     {
//       "PROPERTY": "action",
//       "NAME": "Реакция",
//       "TYPE": "S",
//       "SORT": "500"
//     },
//     {
//       "PROPERTY": "telegram_id",
//       "NAME": "telegram_id",
//       "TYPE": "S",
//       "SORT": "500"
//     },
    // {
    //   "PROPERTY": "first_name",
    //   "NAME": "Имя",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "last_name",
    //   "NAME": "Фамилия",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "username",
    //   "NAME": "username",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "is_bot",
    //   "NAME": "Бот",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "is_premium",
    //   "NAME": "Премиум аккаунт",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "user_link",
    //   "NAME": "Ссылка на пользователя",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    //
    // {
    //   "PROPERTY": "subscribed",
    //   "NAME": "Подписка",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "subscribe_date",
    //   "NAME": "Дата начала подписки",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "unsubscribe_date",
    //   "NAME": "Дата окончания подписки",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "days_in_channel",
    //   "NAME": "Дней на канале",
    //   "TYPE": "N",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_link",
    //   "NAME": "UTM-ссылка",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_source",
    //   "NAME": "Источник трафика",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_medium",
    //   "NAME": "Канал распространения",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_campaign",
    //   "NAME": "Название кампании",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_content",
    //   "NAME": "Контент",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
    // {
    //   "PROPERTY": "utm_term",
    //   "NAME": "Ключевое слово",
    //   "TYPE": "S",
    //   "SORT": "500"
    // },
  // {
  //     "PROPERTY": "source_id",
  //     "NAME": "ID источника (бот, канал)",
  //     "TYPE": "S",
  //     "SORT": "500"
  //   }
  //   {
  //     "PROPERTY": "bot_id",
  //     "NAME": "ID бота)",
  //     "TYPE": "S",
  //     "SORT": "500"
  //   },
  {
      "PROPERTY": "CS_TG_ID_CHANNEL",
      "NAME": "Telegram ID канала CS",
    },
    {
      "PROPERTY": "CS_TG_ID_GROUP",
      "NAME": "Telegram ID группы CS",
    },
    {
      "PROPERTY": "CS_TG_NANE_USER",
      "NAME": "Telegram UserName CS",
    },
    {
      "PROPERTY": "CS_TG_ID_USER",
      "NAME": "Telegram ID пользователя CS",
    },

  ];

    // const response = await $b24.callMethod(
    //     'imconnector.list',
    //     // {
    //     //   CONNECTOR: 'cs_mcm_whatsApp',
    //     // }
   // );

    // for (const key in data) {
    //     const fieldsProp = {
    //         "FIELD_NAME": data[key].PROPERTY,
    //         "EDIT_FORM_LABEL": data[key].NAME,
    //         "LIST_COLUMN_LABEL": data[key].NAME,
    //         "USER_TYPE_ID": "string",
    //         "XML_ID": data[key].PROPERTY,
    //         "SHOW_FILTER": 'Y',
    //         "SHOW_IN_LIST": 'Y',
    //         "EDIT_IN_LIST": 'N',
    //         "IS_SEARCHABLE": 'Y',
    //     }
    //
    //     await $b24.callMethod(
    //         "crm.lead.userfield.add",
    //         {
    //             fields:fieldsProp
    //
    //         })
    //     await $b24.callMethod(
    //         "crm.deal.userfield.add",
    //         {
    //             fields:fieldsProp
    //
    //         })
    //     await $b24.callMethod(
    //         "crm.contact.userfield.add",
    //         {
    //             fields:fieldsProp
    //
    //         })
    //     await $b24.callMethod(
    //         "crm.company.userfield.add",
    //         {
    //             fields:fieldsProp
    //
    //         })
    // }
// //
//     try {
//         console.log(data[key].PROPERTY, data[key].NAME)
//       await $b24.callMethod(
//           'entity.item.property.add',
//           {
//             ENTITY: 'events',
//             PROPERTY: data[key].PROPERTY,
//             NAME: data[key].NAME,
//             TYPE: 'S' // S - string, N - number, L - list и т.д.
//           }
//       );
//       console.log(`Свойство "${data[key].PROPERTY}" добавлено`);
//     } catch (error) {
//       console.error(`Ошибка при добавлении свойства "${key}":`, error);
//     }
//   }

  //   const responseUp = await $b24.callMethod(
  //       //     //   //  'entity.get',
  //       'entity.item.update',
  //       {
  //         ENTITY: 'setup_messager',
  //         ID: 13114,
  //         PROPERTY_VALUES: {
  //     CS_PROFILE_ID: '92bfec8e-80b7',
  //
  // }
  // });


//     const resData = await $b24.callMethod(
//         //     //   //  'entity.get',
//         // 'entity.item.get',
//           'entity.item.get',
//         {
//         //  ENTITY: 'setup_messager',
//          ENTITY: 'events',
//           filter:{
//             ACTIVE:'Y',
//             '!PROPERTY_crm':false
//           }
//         });
//    const arData = resData.getData().result
//     console.log(arData,'arData')
//     for (const key in arData) {
//         const item = arData[key].PROPERTY_VALUES;
// // let dataNew = ''
//       console.log(item,'crm')
//         function getValueAfterPipe(input: string): string | null {
//             if (input.includes('|')) {
//                 return input.split('|')[1] // Возвращаем часть после '|'
//             }
//             return null // Если '|' нет в строке, возвращаем null
//         }
//
//         const leadId = getValueAfterPipe(item.crm)
//
// //  dataNew = await addDate30(item.PROPERTY_VALUES.CS_DATE_CLOSE)
//      console.log(leadId,'leadId')
//
//
//         try {
// // Проверяем существование лида
//             const leadCheckResponse = await $b24.callMethod("crm.lead.get", { id: leadId })
//
//             if (leadCheckResponse && leadCheckResponse.getData()) {
//                 // Лид существует, выполняем обновление
//                 const response = await $b24.callMethod("crm.lead.update", {
//                     id: leadId,
//                     fields: {
//                         UF_CRM_CS_TG_ID_USER: item.telegram_id,
//                         UF_CRM_CS_TG_NANE_USER: item.username
//                     }
//                 })
//
//                 console.log("Лид успешно обновлен:", response.getData())
//             } else {
//                 console.warn(`Лид с ID ${leadId} не найден.`)
//             }
//         } catch (error) {
//             console.error("Ошибка при обработке лида:", error)
//         }
//      }

    // const paramsWidget = {
    //    //   CONNECTOR: 'cs_mcm_whatsapp',
    //       CONNECTOR: 'cs_mcm_telegram',
    //       LINE: 3,
    //    //   LINE: 1,
    //       DATA: {
    //         id: 'cs_mcm_telegramline3',
    //      //   id: 'cs_mcm_whatsappline1',
    //       //  url: 'https://wa.me/79936358058',
    //         url: 'https://t.me/cas_soft_01',
    //        // url_im: 'http://localhost',
    //         name: 'Отдел продаж'
    //       //  name: 'Tex.поддержка'
    //       }
    //     };
    //     const response =   $b24.callMethod(
    //         'imconnector.connector.data.set', paramsWidget);
    // const responseUp = $b24.callMethod(
    //     'entity.item.property.update',
    //     {
    //       ENTITY: 'events',
    //       PROPERTY: 'telegram_id',
    //       NAME: 'Telegram ID пользователя'
    //     })
    // console.log(responseUp,'responseUp')
    // const response = $b24.callMethod(
    //     'entity.item.property.get',
    //     {
    //       ENTITY: 'events'
    //     },)



   // return response
   // return response.getData();
   return 'Y';
  } catch (error) {
    console.error('Ошибка при вызове callMethod:', error);
    if (error instanceof Error) {
      console.error('Message:', error.message);
      console.error('Stack:', error.stack);
    }
    throw error;
  }
}
export async function execute2($b24: B24Frame) {
  try {
    const response = await $b24.callBatch({
      result: {
        method: 'imopenlines.config.list.get',
        params: {
          PARAMS: {
            select: { ID: 'ID', ACTIVE: 'ACTIVE', LINE_NAME: 'LINE_NAME' },
            order: { ID: 'ASC' },
            filter: {}
          },
          OPTIONS: {
            QUEUE: 'Y'
          }
        }
      }
    }, true);

    return response.getData();
  } catch (error) {
    console.error('Ошибка при вызове callBatch:', error);
    throw error;
  }
}
const data = {
    //   'subscription_date': 'Дата подписки',
    //   'subscription_time': 'Время подписки',
    //   'telegram_id': 'Уникальный Telegram ID пользователя',
    //   'first_name': 'Имя',
    //   'last_name': 'Фамилия',
    //   'username': 'Имя Telegram',
    //   'subscription_source': 'Источник подписки',
    //   'unsubscribe_date': 'Дата отписки',
    //   'unsubscribe_time': 'Время отписки',
    //   'days_in_channel': 'количество дней в канале',
    //   'activation_date': 'Дата первого запуска бота',
    //   'activation_time': 'Время запуска бота',
    //   'activation_source': 'Источник запуска',
    //   'search_date': 'Дата запроса',
    //   'search_time': 'Время запроса',
    //   'car_make': 'Марка авто',
    //   'car_model': 'Модель авто',
    //   'year_range': 'Желаемый год выпуска',
    //   'fuel_type': 'Тип двигателя',
    //   'transmission': 'Тип трансмиссии',
    //   'equipment': 'Комплектация',
    //   'budget': 'Бюджет',
    //   'body_type': 'Кузов',
    //   'search_result_count': 'Кол-во результатов',
    //   'track_date': 'Дата события',
    //   'track_time': 'Время события',
    //   'year': 'Год выпуска авто',
    //   'application_date': 'Дата отправки заявки',
    //   'application_time': 'Время отправки заявки',
    //   'phone': 'Номер телефона',
    //   'request_type': 'Из какого поля бота пришел лид',
    //   'car_link': 'Ссылка на авто',
    //   'source': 'Источник заявки',
    //    'CS_KEY_USERS': 'Ключ для площадки',
    //    'CS_KEY_AUTO': 'Ключ AUTO',
    //    'CS_PHONE': 'Номер телефона привязанный',
    //    'CS_BLOCK_GROUPS_ALL': 'Блокировка всех групп для Б24',
    //    'CS_BLOCK_BOTS': 'Блокировка ботов для Б24',
    //    'CS_BLOCK_GROUP': 'Блокировка групп для Б24',
    //    'CS_BLOCK_USERS': 'Блокировка контактов для Бс24',
};
// const dataDel =[34]
//    for (const key in dataDel) {
//        const id = dataDel[key];
//        console.log(id, 'id');
//        await $b24.callMethod(
//            'entity.item.delete',
//            {
//                ENTITY: 'ads_control',
//                ID: id,
//            }
//        );
//    }
// for (const key in data) {
//
//     try {
//       await $b24.callMethod(
//           'entity.item.property.add',
//           {
//             ENTITY: 'ads_control',
//             PROPERTY: key,
//             NAME: data[key],
//             TYPE: 'S' // S - string, N - number, L - list и т.д.
//           }
//       );
//       console.log(`Свойство "${key}" добавлено`);
//     } catch (error) {
//       console.error(`Ошибка при добавлении свойства "${key}":`, error);
//     }
//   }

//   'imconnector.register',
//       [
//         'ID' => 'CS_mcm_telegram',
//       'NAME' => 'CS MCM-Telegram',
//       "ICON" => [
//     "DATA_IMAGE" => $svgT,
//       "COLOR" => "#00ff2e54",
//       "SIZE" => "100%",
//       "POSITION" => "center",
//       "BACKGROUND-COLOR"=>"#f7df0b",
//       "BACKGROUND"=>"#42ecf369",
// ],
//   "ICON_DISABLED" => [
//     "DATA_IMAGE" => $svgT,
//       "SIZE" => "100%",
//       "POSITION" => "center",
//       "COLOR" => "#18ce2569",
//       "BACKGROUND-COLOR"=>"#18ce2569",
// ],
//   "PLACEMENT_HANDLER" => "https://app.cassoft.ru/cs-app/mcm/conector/",
// ]
//   var params = {
//     CONNECTOR: 'cs_mcm_telegram',
//     DATA: {
//
//       url: 'https://app.cassoft.ru/cs-app/mcm/conector/',
//       url_im: 'https://app.cassoft.ru/cs-app/mcm/conector/',
//       name: 'CS  MCM-Telegram'
//     }
//   };
//"cs_mcm_telegram": "CS  MCM-Telegram"
// const response = await $b24.callMethod(
//     'imconnector.connector.data.set',
//     params,);
// const response = await $b24.callMethod(  'imconnector.list')
// const responseDel = await $b24.callMethod(
//     'entity.item.delete',
//     {
//   ID: 23,
//        ENTITY: 'setup_messager',
//     });
//       const response = await $b24.callMethod(
//     'entity.get');
//
//       const responseItem = await $b24.callMethod(
//           'entity.item.get',
//           {
//             //    ENTITY: 'events',
//             ENTITY: 'setup_messager',
//             //       //   ENTITY: 'files',
//             //        //  FILTER: {
//             //        // //   PROPERTY_CS_PROFILE_ID:"cefda638-42f2",
//             //        //  }
//           })
// "payments"
//  await $b24.callMethod(
//      'entity.item.property.add',
//      {
//        ENTITY: 'candidates',
//        PROPERTY: 'responses',
//        NAME: 'Отклики',
//        TYPE: 'S' // S - string, N - number, L - list и т.д.
//      }
//  );
// await $b24.callMethod(
//     'entity.item.property.add',
//     {
//       ENTITY: 'candidates',
//       PROPERTY: 'company_id',
//       NAME: 'Компания CRM',
//       TYPE: 'N' // S - string, N - number, L - list и т.д.
//     }
// );
//
// 1. Получаем все записи из сущности 'payments'
// const responseUp = await $b24.callMethod('entity.item.get', {
//     ENTITY: 'payments',
// });

// const items = responseUp.getData().result || [];
//
// // 2. Перебираем каждую запись
// for (const item of items) {
//     const id = item.ID;
//     const propertyValues = item.PROPERTY_VALUES || {};
//
//     // 3. Преобразуем debet и credit в числа
//     const debet = parseFloat(propertyValues.debet || '0');
//     const credit = parseFloat(propertyValues.credit || '0');
//
//     // 4. Обновляем запись с числовыми значениями
//     await $b24.callMethod('entity.item.update', {
//         ENTITY: 'payments',
//         ID: id,
//         DATA: {
//             PROPERTY_VALUES: {
//                 debet: debet,
//                 credit: credit,
//             },
//         },
//     });
//
//     console.log(`Обновлена запись ID=${id}: debet=${debet}, credit=${credit}`);
// }
//
// console.log('✅ Все записи обновлены.');
//



// await $b24.callMethod(
//         'entity.item.add',
//         {
//           ENTITY: 'payments',
//             NAME: 'Расход',
//             PROPERTY_VALUES: {
//               payer: "ООО", //"Плательщик",
//               name: "продление профиля", // "Название",
//               date_s: "", // "Дата платежа строка",
//               date:  new Date(), //"Дата платежа",
//               credit:  "2000", //"Расход",
//               debet:  "", //"Приход",
//               profile: "", // "Профиль MCM",
//               target: "tets2", // "Назначение",
//               operation: "", // "Операция",
//               type_pay: "", // "Тип платежа",
//               number: "1112", // "Номер операции",
//               status:  "plan", //"Статус операции"
//           } // S - string, N - number, L - list и т.д.
//         }
//     );
// const robotAdd = await $b24.callMethod('bizproc.robot.update',   {
//     const robotAdd = await $b24.callMethod('bizproc.robot.add',   {
//         CODE: 'mcm_robot_warning',
//       //  FIELDS: {
//             HANDLER: 'https://app.cassoft.ru/cassoftApp/market/mcm/ajax/robot.php',
//             AUTH_USER_ID: 1,
//             USE_SUBSCRIPTION: 'Y',
//             NAME: {
//                 ru: 'Отправить сообщение в Telegram группу (МКМ)',
//                 en: 'Send a warning MCM'
//             },
//             DESCRIPTION: 'Отправить сообщение в Telegram группу',
//             PROPERTIES: {
//                 text: {
//                     Name: 'Текст сообщения',
//                     Type: 'text'
//                 },
//                 bot_key: {
//                     Name: 'Ключ от бота',
//                     Type: 'string',
//                     Required: 'Y',
//                     Multiple: 'N'
//                 },
//                 group_tg: {
//                     Name: 'id группы в Telegram',
//                     Type: 'string',
//                     Required: 'Y',
//                     Multiple: 'N'
//                 }
//             },
//             FILTER: {
//                 INCLUDE: [
//                     ['crm', 'CCrmDocumentDeal'],
//                     ['crm', 'CCrmDocumentContact'],
//                     ['crm', 'CCrmDocumentCompany'],
//                     ['crm', 'CCrmDocumentLead']
//                 ]
//             }
//         }
//   //  }
// );
// const responseRes = await $b24.callMethod(
//     //   //  'entity.get',
//    // 'entity.item.get',
//    'entity.item.property.get',
//     {
//       //   ENTITY: 'events',
//     //  ENTITY: 'payments',
//     //  ENTITY: 'setup',
//     //  ENTITY: 'ads_control',
//      // ENTITY: 'ads_report',
//       ENTITY: 'candidates',
//     //   FILTER: {
//   //   PROPERTY_status:"fact",
//    //   }
//     });
//const response = wappGet('/tapi/sync/contacts/get?profile_id=2d6b4542-9f18')
//  const entytiProp = responseRes.getData();
// const   response = createPropertyMap(entytiProp.result)