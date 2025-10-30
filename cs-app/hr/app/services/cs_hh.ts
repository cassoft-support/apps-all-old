export async function handlerAdd(member: string, userId: string,): Promise<Response> {
    const data = {
        member_id: member,
        userId: userId,
        fn: 'handlerAdd',
    };

    const response = await fetch('/cs-app/cs-core/app/hr_pro/apiHH.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const result = await response.text(); // или .json(), если это JSON
    return result; // возвращает "Y" или "N"
}

export async function hhKeyGet() {
    const data = {
        fn: "hhKey",
        app: "hr_pro"
    };

    const response = await fetch('/cs-app/cs-core/app/hr_pro/apiHH.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });

    const resultGet = await response.json(); // Используем .json(), если сервер возвращает JSON
    console.log(resultGet, 'resultGet');

// Проверяем, что ответ содержит нужное поле
    if (resultGet && resultGet.ID) {
        return resultGet.ID;
    } else {
        throw new Error('Ответ не содержит ID');
    }
}
export async function authorizationHH(member_id: string, userId: string,): Promise<Response>{

    try {
        const clientId = await hhKeyGet(); // Теперь await
        const redirectUri = 'https://app.cassoft.ru/cassoftApp/market/hr/ajax/handlerHh.php';
        const authUrl = `https://hh.ru/oauth/authorize?client_id=${clientId}&response_type=code&redirect_uri=${redirectUri}&state=${member_id}|${userId}`;
        window.open(authUrl, '_blank');
    } catch (error) {
        console.error('Ошибка получения client_id:', error);
    }
}


// Обновление токена
export async function refreshTokenHH(userId: string, refresh_token: string, access_token: string): Promise<Response>{
        const clientId = await hhKeyGet(); // Теперь await
        const redirectUri = 'https://app.cassoft.ru/cassoftApp/market/hr/ajax/handlerHh.php';
        const response = await fetch('https://hh.ru/oauth/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                grant_type: 'refresh_token',
                client_id: clientId,
                client_secret: access_token,
                redirect_uri: redirectUri,
                refresh_token: refresh_token,
            }),
        });
        console.log(response,'response')
        return response
}
export async function postHhApi(
    continuationUrl: string,
    accessToken: string,
    payload: any
): Promise<any> {
    const fullUrl = `https://api.hh.ru${continuationUrl}`;

    const response = await fetch(fullUrl, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
    });

    if (!response.ok) {
        throw new Error(`Ошибка POST-запроса к HH.ru: ${response.statusText}`);
    }

    return await response.json();
}
export async function getHhApi(
    continuationUrl: string,
    accessToken: string
): Promise<any> {
    const fullUrl = `https://api.hh.ru${continuationUrl}`;
console.log(fullUrl,'fullUrl')
console.log(accessToken,'accessToken')
    const response = await fetch(fullUrl, {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Content-Type': 'application/json',
        },
    });

    if (!response.ok) {
        throw new Error(`Ошибка GET-запроса к HH.ru: ${response.statusText}`);
    }

    return await response.json();
}


export async function tokenUserHH(userId: string, $b24: any) {
    const itemResponse = await $b24.callMethod('entity.item.get', {
        ENTITY: 'setup', PARAMS: {filter: {}, select: ['*']}
    })
    const itemData = itemResponse.getData().result[0]
    const hhKey = itemData?.PROPERTY_VALUES?.CS_HH_KEY
    let hhKeyArray: any[] = []

    if (typeof hhKey === 'string') {
        try {
            hhKeyArray = JSON.parse(hhKey)
        } catch (error) {
            console.error('Ошибка при разжатии JSON:', error)
            hhKeyArray = [] // или обработать ошибку по-другому
        }
    }
    console.log(userId,'userId')
    console.log(hhKeyArray[userId]['access_token'],'hhKeyArray')
    return hhKeyArray[userId]['access_token'];
}

interface Vacancy {
    id: string;
    name: string;
// Добавьте остальные поля по необходимости
}

interface HhVacancy {
    id: string;
    name: string;
    description: string;
    key_skills: Array<{ name: string }>;
    working_hours: Array<{ id: string }>;
    work_schedule_by_days: Array<{ id: string }>;
    professional_roles: Array<{ id: string }>;
    experience: { id: string };
    employment_form: { id: string };
    internship: boolean;
    accept_temporary: boolean;
    work_format: { id: string };
    address: { id: string };
    type: { id: string };
    night_shifts: boolean;
    accept_handicapped: boolean;
    accept_kids: boolean;
    languages: Array<{ level: string }>;
    alternate_url: string;
    expires_at: string;
    published_at: string;
    counters: {
        responses: number;
        views: number;
        invitations: number;
        unread_responses: number;
        resumes_in_progress: number;
        invitations_and_responses: number;
    };
    salary_range: {
        from: number;
        to: number;
        currency: string;
    };
}

interface B24 {
    callMethod(method: string, params: any): Promise<any>;
}

export  async function vacancyAddHh(
    $b24: B24,
    token: any,
    managers:any,
    vacancyAll: Vacancy[],
    active: string
): Promise<number> {
    let i = 0;
    const user = 1;

    for (const vacancy of vacancyAll) {
        console.log(vacancy,'vacancy')
        const paramsSearch = {
            ENTITY: 'ads_report',
            FILTER: {
                PROPERTY_site_code: vacancy,
            },
        };

        const itemResponse = await $b24.callMethod('entity.item.get', paramsSearch);
       console.log(itemResponse,'itemResponse')
        const item = itemResponse._data.result?.[0];
console.log(item,'item')
        if (!item) {
            const url = `/vacancies/${vacancy}`;
            console.log(url,'url')
            const hhVacancy = await getHhApi(url, token); // Предполагается, что curlGet возвращает промис
console.log(hhVacancy,'hhVacancy')
            let skills = '';
            for (const skill of hhVacancy.key_skills) {
                skills += skill.name + ', ';
            }

            let professional_roles = '';
            for (const role of hhVacancy.professional_roles) {
                professional_roles += role.id + ', ';
            }

            let working_hours = '';
            for (const wh of hhVacancy.working_hours) {
                working_hours += wh.id + ', ';
            }

            let work_schedule_by_days = '';
            for (const ws of hhVacancy.work_schedule_by_days) {
                work_schedule_by_days += ws.id + ', ';
            }

            const params = {
                ENTITY: 'vacancy',
                NAME: hhVacancy.name,
                ACTIVE: active,
                PROPERTY_VALUES: {
                    desc: JSON.stringify(hhVacancy.description),
                    smart_id: '',
                    stage: '',
                    category: '',
                    requirement: '',
                    conditions: '',
                    company_info: '',
                    company_id: '',
                    assigned: user,
                    number_staff: '',
                    code: '',
                    education: '',
                    specialization: JSON.stringify(professional_roles),
                    experience: hhVacancy.experience.id,
                    employment: hhVacancy.employment_form.id,
                    internship: hhVacancy.internship ? 'Y' : 'N',
                    part_time_job: hhVacancy.accept_temporary ? 'Y' : 'N',
                    format_work: hhVacancy.work_format.id,
                    chart_work: JSON.stringify(work_schedule_by_days),
                    clock_work: JSON.stringify(working_hours),
                    address_work: hhVacancy.address.id,
                    city_publish: '', // hhVacancy['']['id'],
                    salary: JSON.stringify(hhVacancy.salary_range),
                    skills: JSON.stringify(skills),
                    preview: '',
                    fits: '',
                    type: hhVacancy.type.id,
                    night_shifts: hhVacancy.night_shifts ? 'Y' : 'N',
                    disabled_pensioner: hhVacancy.accept_handicapped ? 'Y' : 'N',
                    kids: hhVacancy.accept_kids ? 'Y' : 'N',
                    language_level: JSON.stringify(hhVacancy.languages),
                },
            };

            const vacancyAddResponse = await $b24.callMethod('entity.item.add', params);
            console.log(vacancyAddResponse,'vacancyAddResponse')
            const vacancyId = vacancyAddResponse._data.result;
            console.log(vacancyId,'vacancyId')

            if (vacancyId) {
                const paramsControl = {
                    ENTITY: 'ads_control',
                    NAME: hhVacancy.name,
                    ACTIVE: active,
                    PROPERTY_VALUES: {
                        hh_control: true, // Выгрузка на hh
                        hh_date_open: hhVacancy.published_at, // Дата начала выгрузки на hh
                        hh_date_close: hhVacancy.expires_at, // Дата окончания выгрузки на hh
                        rabota_control: "", // Выгрузка на rabota.ru
                        rabota_date_open: "", // Дата начала выгрузки на rabota
                        rabota_date_close: "", // Дата окончания выгрузки на rabota
                        superjob_control: "", // Выгрузка на superjob
                        superjob_date_open: "", // Дата начала выгрузки на superjob
                        superjob_date_close: "", // Дата окончания выгрузки на superjob
                        trudvsem_control: "", // Выгрузка на trudvsem
                        trudvsem_date_open: "", // Дата начала выгрузки на trudvsem
                        trudvsem_date_close: "", // Дата окончания выгрузки на trudvsem
                        avito_control: "", // Выгрузка на avito
                        avito_date_open: "", // Дата начала выгрузки на avito
                        avito_date_close: "", // Дата окончания выгрузки на avito
                        zarplata_control: "", // Выгрузка на zarplata
                        zarplata_date_open: "", // Дата начала выгрузки на zarplata
                        zarplata_date_close: "", // Дата окончания выгрузки на zarplata
                        gorodrabot_control: "", // Выгрузка на gorodrabot
                        gorodrabot_date_open: "", // Дата начала выгрузки на gorodrabot
                        gorodrabot_date_close: "", // Дата окончания выгрузки на gorodrabot
                        manager_id: managers[hhVacancy.manager.id], // Ответственный менеджер
                        vacancy_id: vacancyId, // Вакансия
                        element_id: "", // Элемент CRM
                    }
                }
                const conntolAddResponse =await $b24.callMethod('entity.item.add', paramsControl);
                const controlId = conntolAddResponse._data.result;
                console.log(controlId,'controlId')
                if (controlId) {
                const paramsReport = {
                    ENTITY: 'ads_report',
                    NAME: hhVacancy.name,
                    ACTIVE: active,
                    PROPERTY_VALUES: {
                        date_end: hhVacancy.expires_at,
                        date_open: hhVacancy.published_at,
                        site: 'hh',
                        link: hhVacancy.alternate_url,
                        responses: hhVacancy.counters.responses,
                        views: hhVacancy.counters.views,
                        invitations: hhVacancy.counters.invitations,
                        unread_responses: hhVacancy.counters.unread_responses,
                        resumes_in_progress: hhVacancy.counters.resumes_in_progress,
                        invitations_and_responses: hhVacancy.counters.invitations_and_responses,
                        id: controlId,
                        site_code: hhVacancy.id,
                    },
                };

                await $b24.callMethod('entity.item.add', paramsReport);
                i++;
            }
            }
        }
    }

    console.log(`Создано: ${i}`);
    return i;
}