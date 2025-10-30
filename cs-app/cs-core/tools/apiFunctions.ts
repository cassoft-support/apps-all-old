import { B24Frame } from '@bitrix24/b24jssdk';
import { wappGet } from '@/services/cs-wappi'

export async function execute($b24: B24Frame) {
  try {

     // "IM"



      // const response = await $b24.callMethod(
      // 'imopenlines.session.history.get',
      //     {
      //         CHAT_ID: 3439
      //     });
      const response = await $b24.callMethod(
      'imbot.bot.list',
         );
      return   response
      console.log(response,'response')
  //  return response;
  } catch (error) {
    console.error('Ошибка при вызове callBatch:', error);
    throw error;
  }
}

type PropertyItem = {
    PROPERTY: string;
    NAME: string;
};

type PropertyMap = Record<string, string>;

export async function createPropertyMap(data: any): Record<string, string> {
    if (!Array.isArray(data)) {
        console.error('Expected an array, but got:', data);
        return {};
    }

    return data.reduce((acc, item) => {
        acc[item.PROPERTY] = "'',//" +item.NAME;
        return acc;
    }, {} as Record<string, string>);
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