<script setup lang="ts">
import { ref, onMounted } from "vue"
import Button from "~/components/Generics/Button.vue"
import Input from "~/components/Generics/Input.vue"
import Textarea from "~/components/Generics/Textarea.vue"
import Dialog from "~/scripts/Dialog"
import API from "~/scripts/net/API"
import { setPageTitle } from "~/scripts/utils/general"
import InputDate from "~/components/Generics/InputDate.vue"
import LastRegisters from "./components/LastRegisters.vue"
import { dayjs } from "element-plus"
const id = ref<number | null>(null)
const date = ref<string>("")
const step_one = ref<number | null>(0)
const step_two = ref<number | null>(0)
const step_three = ref<number | null>(0)
const step_four = ref<number | null>(0)
const notes = ref<string>("")
const loading = ref(false)
const errors = ref<Record<string, string>>({})
const historyRef = ref(null)
const { user } = useAuth()

onMounted(() => {
  setPageTitle('Cadastro Diário')
})

/**
 * Limpa o formulário
 */
const clearForm = async () => {
  await historyRef.value?.refresh()
  id.value = null
  date.value = ""
  step_one.value = 0
  step_two.value = 0
  step_three.value = 0
  step_four.value = 0
  notes.value = ""
}

/**
 * Busca registro existente por data
 */
const onDateChange = async (newDate: string | Date) => {
  if (!newDate) return
  try {
    loading.value = true
     const formattedDate = dayjs(newDate).format("YYYY-MM-DD")
    const response = await API.get(`/daily-registers?date=${formattedDate}`)
    const data = response?.data?.data?.data || []
    const existing = data.find((r: any) => r.date === formattedDate)

    if (existing) {
      const confirmed = await Dialog.getInstance().question({
        html: "Já existe um registro para essa data.<br><br>Deseja editar os dados existentes?"
      })

      if (confirmed.isConfirmed) {
        id.value = existing.id
        step_one.value = existing.step_one ?? 0
        step_two.value = existing.step_two ?? 0
        step_three.value = existing.step_three ?? 0
        step_four.value = existing.step_four ?? 0
        notes.value = existing.notes ?? ""
      } else {
        await clearForm()
      }
    }
  } catch (err: any) {
    Dialog.getInstance().toastError(err?.response?.data?.message || "Erro ao buscar dados por data")
  } finally {
    loading.value = false
  }
}


const onSubmit = async () => {
  loading.value = true
  errors.value = {}

  try {
    const payload = {
      partner_id: user.value.partner.id,
      user_id: user.value.id,
      date: date.value,
      step_one: step_one.value ?? 0,
      step_two: step_two.value ?? 0,
      step_three: step_three.value ?? 0,
      step_four: step_four.value ?? 0,
    }

    if (id.value) {
      await API.put(`/daily-registers/${id.value}`, payload)
    } else {
      await API.post('/daily-registers', payload)
    }

    Dialog.getInstance().toastSuccess(`Registro diário ${id.value ? 'atualizado' : 'criado'} com sucesso!`)
    await clearForm()
  } catch (error: any) {
    const responseStatus = error?.status ?? error?.response?.status
    if (responseStatus === 422) {
      const vErrs = error?.response?.data?.errors || {}
      Object.entries(vErrs).forEach(([field, msgs]: any) => {
        errors.value[field] = msgs?.[0]
      })
    } else {
      console.error('Erro ao salvar registro diário', error)
      Dialog.getInstance().toastError('Erro ao salvar registro diário')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <h1 class="text-3xl dark:text-white mb-4">Cadastro Diário</h1>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-xl border border-gray-200 dark:border-gray-700 p-6">
      <h2 class="text-lg font-semibold mb-1 dark:text-white">Cadastrar Dados Diários</h2>
      <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Preencha os dados referentes ao dia selecionado</p>

      <div class="mb-4 w-full">
        <label class="block text-sm font-medium mb-2 dark:text-gray-300">Data:</label>
        <InputDate v-model="date" @change="onDateChange" :disabled="id" />
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <Input
            v-model="step_one"
            type="number"
            :label="user?.partner?.metrics?.step_one || 'Etapa 1'"
            :error="errors.step_one"
            />
          </div>
          <div>
            <Input
            v-model="step_two"
            type="number"
            :label="user?.partner?.metrics?.step_two || 'Etapa 2'"
            :error="errors.step_two"
            />
          </div>
      </div>

      <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <Input
            v-model="step_three"
            type="number"
            :label="user?.partner?.metrics?.step_three || 'Etapa 3'"
            :error="errors.step_three"
            />
          </div>
        <div>
          <Input
          v-model="step_four"
          type="number"
          :label="user?.partner?.metrics?.step_four || 'Etapa 4'"
          :error="errors.step_four"
          />
        </div>
      </div>

      <div class="mb-10">
        <Textarea
          v-model="notes"
          label="Observações"
          rows="3"
          placeholder="Adicione observações sobre este dia..."
        />
      </div>

      <div class="flex gap-3">
        <Button
          v-if="id"
          type="button"
          variant="secondary"
          @click="clearForm"
          class="w-full py-3 px-4 font-medium flex items-center justify-center gap-2"
        >
          Cancelar Edição
        </Button>

        <Button
          type="submit"
          variant="primary"
          :loading="loading"
          :disabled="!date"
          @click="onSubmit"
          class="w-full py-3 px-4 font-medium flex items-center justify-center gap-2"
        >
          {{ id ? 'Atualizar' : 'Registrar' }}
        </Button>
      </div>
    </div>

    <!-- Histórico (opcional) -->
    <LastRegisters ref="historyRef" />
  </div>
</template>
