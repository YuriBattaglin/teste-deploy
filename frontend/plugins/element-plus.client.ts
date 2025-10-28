// plugins/element-plus.ts
import { defineNuxtPlugin } from '#app'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import ptBr from 'element-plus/dist/locale/pt-br.mjs'
import dayjs from 'dayjs'
import 'dayjs/locale/pt-br'
import updateLocale from 'dayjs/plugin/updateLocale'
import isoWeek from "dayjs/plugin/isoWeek"
import weekOfYear from "dayjs/plugin/weekOfYear"
import localeData from "dayjs/plugin/localeData"

dayjs.extend(isoWeek)
dayjs.extend(weekOfYear)
dayjs.extend(localeData)
dayjs.locale("pt-br")

dayjs.extend(updateLocale)
dayjs.locale('pt-br')
dayjs.updateLocale('pt-br', {
  weekStart: 1
})

export default defineNuxtPlugin((nuxtApp) => {
  nuxtApp.vueApp.use(ElementPlus, {
    locale: ptBr
  })
})
