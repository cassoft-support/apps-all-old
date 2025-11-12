import { B24Frame } from '@bitrix24/b24jssdk';

export async function execute($b24: B24Frame) {
  try {
    // const authManager = $b24.auth;
    // const authData = authManager.getAuthData();
    // const resProfile = await fetch('https://app.cassoft.ru/local/CSlibs/classes/app/mcm/functionVue.php', {
    //   method: 'POST',
    //   headers: {
    //     'Content-Type': 'application/json',
    //   },
    //   body: JSON.stringify({
    //     member_id: authData.member_id,
    //     fn: 'profileCsMcm',
    //   }),
    // });
    // const arProfile = await resProfile.json();
    // console.log(arProfile[0])
  //       $arMessage =      array(
  //           'user' => array(
  //       'id',//ID пользователя во внешней системе *
  //       'name',//Имя
  //   ),
  //   //Массив описания сообщения
  //   'message' => array(
  //       'id', //ID сообщения во внешней системе.*
  //       'date', //Время сообщения в формате timestamp *
  //       'disable_crm' => 'Y' ,//отключить чат трекер (CRM трекер)
  //       'text', //Текст сообщения. Должен быть указан элемент text или files.
  // )
  // ),
  //   //Массив описания чата
  //   'chat' => array(
  //       'id',//ID чата во внешней системе *
  //   ),
  // ),
  //
  //   'imconnector.send.messages',
  //       [
  //         'CONNECTOR' => $mcmConnector['UF_CONNECTOR'],
  //       'LINE' => $mcmConnector['UF_LINE_B24'],
  //       'MESSAGES' => [$arMessage],
  // ]



    // const del = await $b24.callMethod(
    //       'placement.unbind',
    //       {
    //         "placement": "SETTING_CONNECTOR",
    //         "handler": "https://app.cassoft.ru/cs-app/mcm/placementTabs.php",
    //       }
    //   );
    // const add = await $b24.callMethod(
    //       'placement.bind',
    //       {
    //         "placement": "SETTING_CONNECTOR",
    //         "handler": "https://app.cassoft.ru/cs-app/mcm/placementTabs",
    //         "title": "МКМ",
    //         "description": "",
    //         "langAll": {
    //           "en": {
    //             "TITLE": "MCM",
    //           },
    //           "ru": {
    //             "TITLE": "МКМ",
    //           }
    //         }
    //       }
    //   );
    // const add = await $b24.callMethod(
    //       'placement.bind',
    //       {
    //         "placement": "IM_NAVIGATION",
    //         "handler": "https://app.cassoft.ru/cs-app/mcm/placementTabs",
    //         "title": "МКМ",
    //         "description": "",
    //         'OPTIONS' : {
    //           'iconName': 'fa-comments-o',
    //     // 'context':'USER;LINES',
    //     // 'role': 'ADMIN',
    //     // 'extranet':'N'
    //         },
    //         "langAll": {
    //           "en": {
    //             "title": "MCM",
    //           },
    //           "ru": {
    //             "title": "МКМ",
    //           }
    //         }
    //       }
    //   );


    // const itemAdd = $b24.callMethod(
    //     'entity.item.add',
    //     {
    //       ENTITY: 'setup_messager',
    //       DATE_ACTIVE_FROM: new Date(),
    //       NAME: arProfile[0].UF_NAME,
    //       PROPERTY_VALUES: {
    //         CS_LINE:arProfile[0].UF_LINE_B24,
    //         CS_CONNECTOR:arProfile[0].UF_CONNECTOR,
    //         CS_STATUS:arProfile[0].UF_ACTIVE,
    //         CS_PROFILE_ID:arProfile[0].UF_PROFILE_ID,
    //         CS_PROFILE_NAME:arProfile[0].UF_PROFILE_NAME,
    //         CS_CODE:arProfile[0].UF_CS_CODE,
    //         CS_DATE_CREATE:arProfile[0].UF_DATE_CREATE,
    //         CS_DATE_CLOSE:arProfile[0].UF_DATE_CLOSE,
    //         CS_RESOURCE:arProfile[0].UF_RESOURCE,
    //         CS_TYPE:arProfile[0].UF_TYPE,
    //       },
    //     }
    // );
    //   const itemAdd = $b24.callMethod(
    //     'entity.item.update',
    //     {
    //       ENTITY: 'setup_messager',
    //       ID: 5881,
    //       PROPERTY_VALUES: {
    //         CS_RESOURCE:'wappi',
    //         CS_TYPE:'Whatsapp',
    //       },
    //     }
    // );
    //   const itemAdd = $b24.callMethod(
    //     'entity.item.delete',
    //     {
    //       ENTITY: 'setup_messager',
    //       ID: 5883,
    //     }
    // );
    // $b24.callMethod(
    //     'entity.item.property.add',
    //     {
    //       ENTITY: 'setup_messager',
    //       PROPERTY: 'CS_TYPE',
    //       NAME: 'Тип соединения',
    //       TYPE: 'S'
    //     }
    // );

    // const response = await $b24.callMethod(
    //   //  'entity.get',
    //     //  'entity.item.get',
    //       'entity.item.property.get',
    //     {
    //        ENTITY: 'files',
    //      //  FILTER: {
    //      // //   PROPERTY_CS_PROFILE_ID:"cefda638-42f2",
    //      //  }
    //     });
    //

        // "ENTITY": "setup",
        // "NAME": "Общие настройки"
        // "ENTITY": "setup_log",
        // "NAME": "Настройки (история)"
        // "ENTITY": "setup_messager",
        // "NAME": "Настройки  мессенджеров"
        // "ENTITY": "messages",
        // "NAME": "Рассылка"
        // "ENTITY": "payments",
        // "NAME": "Платежи"
        // "ENTITY": "cascades",
        // "NAME": "Каскады"
        // "ENTITY": "files",
        // "NAME": "Файлы мессенджера"

    // const responseUp = await $b24.callMethod('userfieldtype.update',
    //     {
    //       "USER_TYPE_ID": "cs_mcm_form",
    //       "HANDLER": "https://app.cassoft.ru/cs-app/mcm/mcmForm",
    //       "TITLE": "CS Многоканальный мессенджер (форма)",
    //     });
    //const response = await $b24.callMethod('userfieldtype.list');
    const response = await $b24.callMethod('placement.get');
    // const response = await $b24.callBatch({
    //   result: {
    //     method: 'userfieldconfig.list',
    //   //  method: 'placement.get',
    //   //  method: 'event.get',
    // //     params: {
    // //       SCOPE: "user"
    // // }
    //   }
    // }, true);
    // const data = response.getData();
    // const processedData = data.result.map((item: any) => {
    //   const event = item.event;
    //   const handler = item.handler.replace('.php', ''); // Удаляем .php из handler
    //   const authType = parseInt(item.auth_type, 10);
    // {
    //   "event": "ONIMCONNECTORMESSAGEADD",
    //     "handler": "https://app.cassoft.ru/cs-app/mcm/OnImConnectorMessageAdd",
    //     "auth_type": "0",
    //     "offline": 0
    // }
    //   $b24.callMethod(
    //       'event.unbind',
    //       {
    //         event: "ONIMCONNECTORMESSAGEADD",
    //         handler: "https://app.cassoft.ru/cs-app/mcm/ajax/OnImConnectorMessageAdd.php",
    //         auth_type: "0",
    //       }
    //   );
    // //   // Пример вызова BX24.callBind
    //   $b24.callMethod(
    //       'event.bind',
    //       {
    //         event: "ONIMCONNECTORMESSAGEADD",
    //         handler: "https://app.cassoft.ru/cassoftApp/market/mcm/ajax/OnImConnectorMessageAdd.php",
    //         auth_type: "0",
    //       }
    //   );
    //
    // });
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
