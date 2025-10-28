<script setup lang="ts">
import { ref, onMounted } from "vue"
import AutoComplete from "~/components/Generics/AutoComplete.vue"
import Button from "~/components/Generics/Button.vue"
import Input from "~/components/Generics/Input.vue"
import InputWeek from "~/components/Generics/InputWeek.vue"
import Textarea from "~/components/Generics/Textarea.vue"
import Dialog from "~/scripts/Dialog"
import API from "~/scripts/net/API"
import { setPageTitle } from "~/scripts/utils/general"
import { extractDatesFromWeek } from "~/scripts/utils/date"
import LastTraffics from "./components/LastTraffics.vue"
import InputMoney from "~/components/Generics/InputMoney.vue"

const week = ref("")
const id = ref(0)
const weekRef = ref(null);
const partner = ref<any>("")
const impressions = ref<number | null>(0)
const clicks = ref<number | null>(0)
const investment = ref<number | null>(0)
const leads = ref<number | null>(0)
const notes = ref("")
const loading = ref(false)
const errors = ref<Record<string, string>>({})
const historyRef = ref(null)

onMounted(async () => {
  setPageTitle('Cadastro de Tráfego')
})

const clearForm = async (clearPartner: boolean) => {
  await historyRef.value?.refresh()
  id.value = 0
  week.value = ""
  weekRef.value?.clear()
  partner.value = clearPartner ? "" : partner.value
  impressions.value = 0
  clicks.value = 0 
  investment.value = 0
  leads.value = 0
  notes.value = ""
}

const isDisabled = computed(() => {
  return !partner.value || (typeof partner.value === 'string' && !partner.value.trim())
})

const onWeekChange = async (newWeek: string | Date) => {
  if (!newWeek) return;

  const data = await searchDataPerWeekAndPartner();

  if (data && data.length > 0) {
    const confirmed = await Dialog.getInstance().question({
      html: "Você selecionou uma semana com dados já cadastrados para este parceiro.<br><br>Deseja editar esses dados?"
    });
    if (confirmed.isConfirmed) {
      const traffic = data[0];
      id.value = traffic.id;
      impressions.value = traffic.impressions ?? 0;
      clicks.value = traffic.clicks ?? 0;
      investment.value = traffic.ad_investment ?? 0;
      leads.value = traffic.leads ?? 0;
      notes.value = traffic.observation ?? "";
      partner.value = traffic.partner ?? partner.value;
    } else {
    clearForm(false);
    }
  }
}


const searchDataPerWeekAndPartner = async () => {
  loading.value = true
  const { start_date, end_date } = extractDatesFromWeek(week.value)
  const partnerToSearch = typeof partner.value === 'object' ? partner.value?.id : partner.value;
  try {
    const response = await API.get(`/traffics?start_date=${start_date}&end_date=${end_date}&partner_id=${partnerToSearch}`)
    return response?.data?.data?.data;
  } catch (err: any) {
    Dialog.getInstance().toastError(err?.response?.data?.message || err?.message || "Erro ao buscar dados por semana");
    return false;
  } finally {
    loading.value = false
  }
}

const onSubmit = async () => {
  loading.value = true
  errors.value = {}
  try {
    const { start_date, end_date } = extractDatesFromWeek(week.value)

    const payload = {
      partner_id: typeof partner.value === 'object' ? partner.value?.id : partner.value || null,
      impressions: impressions.value ?? null,
      clicks: clicks.value ?? null,
      ad_investment: investment.value ?? null,
      leads: leads.value ?? null,
      observation: notes.value || null,
      start_date,
      end_date,
    }

    if(id.value){
      await API.put(`/traffics/${id.value}`, payload)
    }else{
      await API.post('/traffics', payload)
    }
    Dialog.getInstance().toastSuccess(`Tráfego ${id.value ? 'atualizado' : 'registrado'} com sucesso!`)
    await clearForm(true);
  } catch (error: any) {
    const responseStatus = error?.status ?? error?.response?.status
    if (responseStatus === 422) {
      const vErrs = error?.response?.data?.errors || {}
      Object.entries(vErrs).forEach(([field, responseErrors]: any) => {
        errors.value[field] = responseErrors?.[0]
      })
    } else {
      console.error('Erro ao salvar tráfego', error?.response?.data || error?.message)
      Dialog.getInstance().toastError('Ocorreu um erro ao salvar os dados de tráfego')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <h1 class="text-3xl dark:text-white mb-4">Cadastro de Tráfego</h1>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-xl border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-lg font-semibold mb-1 dark:text-white">Cadastrar Dados Semanais</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Preencha os resultados de tráfego</p>

      <div class="mb-4">
        <AutoComplete v-model="partner" label="Parceiro" endpoint="partners" valueKey="id" labelKey="description"
          placeholder="Buscar parceiro..." :error="errors.partner_id" :disabled="id"/>
      </div>

      <div class="mb-4 w-full">
        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Semana:</label>
        <InputWeek v-model="week" ref="weekRef" @change="onWeekChange" :disabled="isDisabled || id" />
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <Input v-model="impressions" type="number" label="Impressões" :error="errors.impressions"
            :disabled="isDisabled" />
        </div>
        <div>
          <Input v-model="clicks" type="number" label="Cliques" :error="errors.clicks" :disabled="isDisabled" />
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <InputMoney v-model="investment" label="Investimento (R$)" :error="errors.ad_investment || errors.investment"
            :disabled="isDisabled" />
        </div>
        <div>
          <Input v-model="leads" type="number" label="Leads Gerados" :error="errors.leads" :disabled="isDisabled" />
        </div>
      </div>

      <div class="mb-10">
        <Textarea v-model="notes" label="Observações" :rows="3" placeholder="Adicione observações sobre esta semana..."
          :disabled="isDisabled" />
      </div>

      <div class="flex gap-3">
        <Button v-if="id" type="button" variant="secondary"  @click="clearForm(true)"
        class="w-full py-3 px-4 font-medium flex items-center justify-center gap-2">
       Cancelar Edição
      </Button>
      <Button type="submit" variant="primary" :loading="loading" :disabled="!partner || !week" @click="onSubmit"
      class="w-full py-3 px-4 font-medium flex items-center justify-center gap-2">
      {{ id ? 'Atualizar' : 'Registrar'}}
    </Button>
  </div>
    </div>

    <LastTraffics ref="historyRef" />
  </div>
</template>
