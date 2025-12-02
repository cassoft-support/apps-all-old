import { B24Frame } from '@bitrix24/b24jssdk';

export async function execute($b24: B24Frame) {
  try {
    // const data = {
    //   'subscription_date': 'Дата подписки',
    //   'subscription_time': 'Время подписки',
    //   'telegram_id': 'Уникальный Telegram ID пользователя',
    //   'first_name': 'Имя пользователя',
    //   'last_name': 'Фамилия пользователя (может быть пустым)',
    //   'username': 'Telegram username (если указан)',
    //   'subscription_source': 'Источник подписки — текст или код пригласительной ссылки',
    //   'unsubscribe_date': 'Дата отписки',
    //   'unsubscribe_time': 'Время отписки',
    //   'days_in_channel': 'количество дней в канале',
    //   'activation_date': 'Дата первого запуска бота (в формате дд.мм.гггг)',
    //   'activation_time': 'Время запуска бота (в формате чч:мм)',
    //   'activation_source': 'Источник запуска (если передаётся invite link, deep link и т.п.)',
    //   'search_date': 'Дата запроса',
    //   'search_time': 'Время запроса',
    //   'car_make': 'Марка авто (например: Toyota)',
    //   'car_model': 'Модель авто (если указана)',
    //   'year_range': 'Желаемый год выпуска (например: 2018–2022)',
    //   'fuel_type': 'Тип двигателя (бензин, дизель, электро и т.п.)',
    //   'transmission': 'Тип трансмиссии (если выбран)',
    //   'equipment': 'комплектация',
    //   'budget': 'Бюджет на покупку (например: до 2 млн руб.)',
    //   'body_type': 'Кузов (если выбрано: седан, SUV и т.п.)',
    //   'search_result_count': 'Кол-во результатов, полученных по фильтрам (опционально)',
    //   'track_date': 'Дата события',
    //   'track_time': 'Время события',
    //   'year': 'Год выпуска авто',
    //   'application_date': 'Дата отправки заявки',
    //   'application_time': 'Время отправки заявки',
    //   'phone': 'Номер телефона (если пользователь его указал)',
    //   'request_type': 'Из какого поля бота пришел лид',
    //   'car_link': 'Ссылка на авто',
    //   'source': 'Источник заявки (например: бот, канал, реклама и т.п.)',
    // };
    // for (const key in data) {
    //
    //   try {
    //     await $b24.callMethod(
    //         'entity.item.property.add',
    //         {
    //           ENTITY: 'events',
    //           PROPERTY: key,
    //           NAME: data[key],
    //           TYPE: 'S' // S - string, N - number, L - list и т.д.
    //         }
    //     );
    //     console.log(`Свойство "${key}" добавлено`);
    //   } catch (error) {
    //     console.error(`Ошибка при добавлении свойства "${key}":`, error);
    //   }
    // }

        // await $b24.callMethod(
        //     'entity.item.property.add',
        //     {
        //       ENTITY: 'payments',
        //       PROPERTY: status,
        //       NAME: 'Статус операции',
        //       TYPE: 'S' // S - string, N - number, L - list и т.д.
        //     }
        // );

   //  const response = await $b24.callMethod(
    //     //   //  'entity.get',
    //     //  'entity.item.get',
    //     'entity.item.property.get',
    //     {
    //    //   ENTITY: 'events',
    //         ENTITY: 'payments',
    //       //  FILTER: {
    //       // //   PROPERTY_CS_PROFILE_ID:"cefda638-42f2",
    //       //  }
    //     });
 //   const paramsWidget = {
//           CONNECTOR: $b24.placement.options.CONNECTOR,
//           LINE: $b24.placement.options.LINE,
//           DATA: {
//             id: $b24.placement.options.CONNECTOR+'line'+$b24.placement.options.LINE,
//             url: 'https://wa.me/7993903579',
//            // url_im: 'http://localhost',
//             name: selectedProfile.label
//           }
//         };
//         const imConnectorWidget =   $b24.callMethod(
//             'imconnector.connector.data.set', paramsWidget);


    const response = await $b24.callMethod(
        'imconnector.get',
        {
          CONNECTOR: 'cs_mcm_whatsApp',
  }
  );
    return response.getData();
  } catch (error) {
    console.error('Ошибка при вызове callBatch:', error);
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
