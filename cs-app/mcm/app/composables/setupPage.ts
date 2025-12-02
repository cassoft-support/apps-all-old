
import { onMounted, onUnmounted } from 'vue'
import {
  initializeB24Frame,
  LoggerBrowser,
  B24Frame,
  EnumCrmEntityTypeId,
  Text,
  type ISODate
} from '@bitrix24/b24jssdk'

const $logger = LoggerBrowser.build('MyApp', import.meta.env?.DEV === true)
let $b24: B24Frame

onMounted(async () => {
  try
  {
    $b24 = await initializeB24Frame()

    const response = await $b24.callBatch({
      CompanyList: {
        method: 'crm.item.list',
        params: {
          entityTypeId: EnumCrmEntityTypeId.company,
          order: { id: 'desc' },
          select: [
            'id',
            'title',
            'createdTime'
          ]
        }
      }
    }, true)

    const data = response.getData()
    const dataList = (data.CompanyList.items || []).map((item: any) => {
      return {
        id: Number(item.id),
        title: item.title,
        createdTime: Text.toDateTime(item.createdTime as ISODate)
      }
    })

    $logger.info('response >> ', dataList)
    $logger.info('load >> stop ')
  }
  catch (error)
  {
    $logger.error(error)
  }
})

onUnmounted(() => {
  $b24?.destroy()
})
