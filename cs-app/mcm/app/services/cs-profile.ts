
import {  resizeWindow } from '@/tools/bitrix';
export async function makeProfileClose(itemProfileId: string, $b24: B24Frame, itemProfile: string): Promise<void> {
    if (itemProfileId) {
        const imConnectorDeactivate = await $b24.callMethod('imconnector.activate', {
            CONNECTOR: $b24.placement.options.CONNECTOR,
            LINE: $b24.placement.options.LINE,
            ACTIVE: 0,
        });

        const resImConnectorDeactivate = await imConnectorDeactivate.getData().result;

        if (resImConnectorDeactivate === true) {
            const setupItemUpdate = await $b24.callMethod('entity.item.update', {
                ENTITY: 'setup_messager',
                ID: itemProfileId,
                PROPERTY_VALUES: {
                    CS_LINE: false,
                    CS_CONNECTOR: false,
                    CS_STATUS: false,
                },
            });

            const resSetupItemUpdate = await setupItemUpdate.getData().result;

            if (resSetupItemUpdate === true && ($b24.placement.options.CONNECTOR === 'cs_mcm_whatsapp' || $b24.placement.options.CONNECTOR === 'cs_mcm_telegram')) {
                const updateProfile = await fetch('/local/CSlibs/classes/app/mcm/functionVue.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        CONNECTOR: false,
                        LINE: false,
                        ACTIVE_STATUS: false,
                        profile: itemProfile,
                        fn: 'profileUpdate',
                    }),
                });

                const resUpdateProfile = await updateProfile.text();

                if (resUpdateProfile === 'Y') {
                    await refreshProfiles(itemProfile, $b24);
                }
            }
        }
    }
}

export async function refreshProfiles(connector: string, $b24: B24Frame): Promise<{
    profileList: { label: string; value: string; id: string }[];
    hasActiveProfiles: boolean;
    statusActiveProfiles: boolean;
}> {
    // Шаг 1: Получаем текущие профили
    let setupMesGet = await $b24.callMethod(
        'entity.item.get',
        {
            entity: 'setup_messager',
            filter: {
                ACTIVE: 'Y',
                PROPERTY_CS_TYPE: connector
            }
        }
    );

    let setup = setupMesGet.getData().result as SetupMessagerItem[];
    console.log(setup, 'Initial setup');

    // Шаг 2: Если профилей нет и это не WhatsApp/Telegram — создаём новый
    if (!setup || setup.length === 0 && connector !== 'Whatsapp' && connector !== 'Telegram') {
        try {
            await ProfileItemAdd(connector, $b24);
        } catch (error) {
            console.error('Ошибка при создании профиля:', error);
            // Можно выбросить ошибку или обработать как-то иначе
        }

        // Шаг 3: После создания — повторно получаем данные
        setupMesGet = await $b24.callMethod(
            'entity.item.get',
            {
                entity: 'setup_messager',
                filter: {
                    ACTIVE: 'Y',
                    PROPERTY_CS_TYPE: connector
                }
            }
        );

        setup = setupMesGet.getData().result as SetupMessagerItem[];
        console.log(setup, 'Setup after creation');
    }

    // Шаг 4: Поиск активного профиля
    const activeProfile = setup.find(item => {
        return (
            item.PROPERTY_VALUES?.CS_LINE === $b24.placement.options.LINE &&
            item.PROPERTY_VALUES?.CS_CONNECTOR === $b24.placement.options.CONNECTOR
        );
    });

    let profileList: { label: string; value: string; id: string }[] = [];
    let hasActiveProfiles = false;
    let statusActiveProfiles = false;

    if (activeProfile) {
        statusActiveProfiles = activeProfile.PROPERTY_VALUES;

    } else {
        profileList = setup
            .filter(item => !item.PROPERTY_VALUES?.CS_LINE || item.PROPERTY_VALUES?.CS_LINE === '')
            .map(item => ({
                label: item.NAME,
                value: item.PROPERTY_VALUES?.CS_PROFILE_ID || '',
                id: item.ID
            }));
        hasActiveProfiles = profileList.length > 0;
    }

    return {
        profileList,
        hasActiveProfiles,
        statusActiveProfiles
    };
}
export async function handleConnectProfile(state: any, $b24: B24Frame, profileKey: string, schema: any, toast: any) {
    const result = schema.safeParse(state);
console.log(result,'result')
    if (!result.success) {
        toast.error(result.error.errors[0].message);
        return;
    }

    const selectedProfile = result.data.selectMenu;
console.log(selectedProfile,'selectedProfile')
    if (selectedProfile && selectedProfile.id) {
        const imConnectorActivate = await $b24.callMethod('imconnector.activate', {
            CONNECTOR: $b24.placement.options.CONNECTOR,
            LINE: $b24.placement.options.LINE,
            ACTIVE: 1,
        });

        const resImConnectorActivate = imConnectorActivate.getData().result;
console.log(resImConnectorActivate,'resImConnectorActivate')
        if (resImConnectorActivate === true) {

            const setupItemUpdate = await $b24.callMethod('entity.item.update', {
                ENTITY: 'setup_messager',
                ID: selectedProfile.id,
                PROPERTY_VALUES: {
                    CS_LINE: $b24.placement.options.LINE,
                    CS_CONNECTOR: $b24.placement.options.CONNECTOR,
                    CS_STATUS: true,
                    CS_KEY_USERS: profileKey,
                },
            });

            const resSetupItemUpdate = setupItemUpdate.getData().result;
console.log(resSetupItemUpdate,'resSetupItemUpdate')
            if (resSetupItemUpdate === true) {
                const authManager = $b24.auth;
                const authData = authManager.getAuthData();
                const updateProfile = await fetch('/cs-app/cs-core/app/autoRu/tools/connector.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        profile: selectedProfile.value,
                        app_code: 'auto_ru',
                        member_id: authData.member_id,
                    }),
                });

                const resUpdateProfile = await updateProfile.text();
console.log(resUpdateProfile,'resUpdateProfile')
                if (resUpdateProfile === 'Y') {

                    await refreshProfiles(selectedProfile.value, $b24);
                    resizeWindow();
                    await onMounted(); // ✅ Перезапускаем onMounted для обновления данных
                }
            }
        }
    }
}

export async function ProfilesWappiAdd($b24: B24Frame, props: any) {
    const authManager = $b24.auth;
    const authData = authManager.getAuthData();
    if (authData) {
        try {
            const resProfile = await fetch('/local/CSlibs/classes/app/mcm/functionVue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    member_id: authData.member_id,
                    domain: authData.domain,
                    profile: props,
                    fn: 'profileWappiAdd',
                }),
            });

            const resultAddJson = await resProfile.text();
            const resultAdd = JSON.parse(resultAddJson);
console.log(resultAdd, 'resultAdd')
            if (resultAdd) {
                const itemAdd = await $b24.callMethod(
                    'entity.item.update',
                    {
                        ENTITY: 'setup_messager',
                        ID: props.id,
                        PROPERTY_VALUES: {
                            CS_PROFILE_ID: resultAdd.profile_id,
                            CS_DATE_CLOSE:resultAdd.date_close
                        },
                    }
                );
console.log(itemAdd,'itemAdd')
                const resItemAdd = itemAdd.getData().result;

                if (resItemAdd) {

                    return resultAdd;
                } else {
                    throw new Error('Ошибка при изменения профиля.');
                }
            } else {
                throw new Error('Ошибка при добавлении профиля');
            }
        } catch (error) {
            throw error;
        }
    } else {
        throw new Error('Данные аутентификации истекли или недоступны.');
    }
}
export async function ProfilesAdd(name: string, type: string, $b24: B24Frame, props: any) {
    const authManager = $b24.auth;
    const authData = authManager.getAuthData();
    if (authData) {
        try {
            const resProfile = await fetch('/local/CSlibs/classes/app/mcm/functionVue.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    member_id: authData.member_id,
                    domain: authData.domain,
                    type: type,
                    profile_name: name,
                    fn: 'profileAdd',
                }),
            });

            const resultAddJson = await resProfile.text();
            const resultAdd = JSON.parse(resultAddJson);

            if (resultAdd) {
                const itemAdd = await $b24.callMethod(
                    'entity.item.add',
                    {
                        ENTITY: 'setup_messager',
                        DATE_ACTIVE_FROM: new Date(),
                        NAME: resultAdd.name,
                        PROPERTY_VALUES: {
                            CS_PROFILE_ID: resultAdd.profile_id,
                            CS_PROFILE_NAME: resultAdd.profile_name,
                            CS_CODE: resultAdd.cs_code,
                            CS_DATE_CREATE: resultAdd.date_creat,
                            CS_DATE_CLOSE: resultAdd.date_close,
                            CS_RESOURCE: 'wappi',
                            CS_TYPE: resultAdd.type,
                        },
                    }
                );

                const resItemAdd = itemAdd.getData().result;

                if (resItemAdd) {
                    props.onRefresh?.();
                    return true;
                } else {
                    throw new Error('Ошибка при добавлении профиля.');
                }
            } else {
                throw new Error('Ошибка при добавлении профиля');
            }
        } catch (error) {
            throw error;
        }
    } else {
        throw new Error('Данные аутентификации истекли или недоступны.');
    }
}
export async function ProfileItemAdd(type: string, $b24: B24Frame): Promise<void> {
    if ($b24 && type) {
        try {
            const name = `${type}Line${$b24.placement.options.LINE}`;
            const code = await generateCodeCS(16, $b24);

            const itemAdd = await $b24.callMethod(
                'entity.item.add',
                {
                    ENTITY: 'setup_messager',
                    DATE_ACTIVE_FROM: new Date(),
                    NAME: name,
                    PROPERTY_VALUES: {
                        CS_PROFILE_NAME: name,
                        CS_PROFILE_ID: code,
                        CS_CODE: code,
                        CS_DATE_CREATE: new Date(),
                        CS_RESOURCE: type,
                        CS_TYPE: type,
                    },
                }
            );

            const resItemAdd = itemAdd.getData().result;

            if (!resItemAdd) {
                throw new Error('Ошибка при добавлении профиля.');
            }

        } catch (error) {
            console.error('Ошибка в ProfileItemAdd:', error);
            throw error;
        }
    } else {
        throw new Error('Данные аутентификации истекли или недоступны.');
    }
}

async function generateCodeCS(length = 16, $b24: B24Frame): Promise<string> {
    const characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const charactersLength = characters.length;

    const getRandomCode = () => {
        let code = '';
        for (let i = 0; i < length; i++) {
            code += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return code;
    };

    let randomCode = getRandomCode();

// Получаем все значения CS_CODE из сущности setup_messager
    const existingCodes = await $b24.callMethod('entity.item.get', {
        entity: 'setup_messager',
        select: ['PROPERTY_VALUES.CS_CODE'],
    });

    const existingCodesList = existingCodes.getData().result.map((item: any) => item.PROPERTY_VALUES?.CS_CODE || '');

// Генерируем новый код, пока не найдём уникальный
    do {
        randomCode = getRandomCode();
    } while (existingCodesList.includes(randomCode));

    return randomCode;
}