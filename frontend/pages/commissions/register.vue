<script setup lang="ts">
import { IconFileUpload } from '@tabler/icons-vue'
import { setPageTitle } from '~/scripts/utils/general'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import Button from '~/components/Generics/Button.vue'
import API from '~/scripts/net/API'
import Dialog from '~/scripts/Dialog'
import TabUsers from './components/TabUsers.vue'
import TabData from './components/TabData.vue'
import Tabs from '~/components/Generics/Tabs.vue'

interface FormData {
  nome: string
  cpf_cnpj: string
  email: string
  phone: string
  firstStep: string
  secondStep: string
  thirdStep: string
  fourthStep: string
  active: boolean | number
  logo: File | null
  squad_id: number
}

const form = ref<FormData>({
  nome: '',
  cpf_cnpj: '',
  email: '',
  phone: '',
  firstStep: 'Captação de lead',
  secondStep: '',
  thirdStep: '',
  fourthStep: 'Venda',
  active: 1,
  logo: null,
  squad_id: 0
})

const users = ref<any[]>([])
const logoPreview = ref<string | null>(null)
const loading = ref(false)
const route = useRoute()
const activeTab = ref<'general' | 'users'>('general')
const id = Number(route.query.id as string)
const errors = ref({});

onMounted(async () => {
  setPageTitle('Cadastro de parceiro')

  if (id) {
    await fetchTenant(id)
  }
})

const fetchTenant = async (id: number) => {
  loading.value = true
  try {
    const response = await API.get(`/partners/${id}`)
    const data = response.data.data
    users.value = data.users || []
    form.value.nome = data.name
    form.value.cpf_cnpj = data.cpf_cnpj
    form.value.squad_id = data.squad_id
    form.value.email = data.email
    form.value.phone = data.phone
    form.value.active = data.active ? 1 : 0
    form.value.firstStep = data.metrics.step_one
    form.value.secondStep = data.metrics.step_two
    form.value.thirdStep = data.metrics.step_three
    form.value.fourthStep = data.metrics.step_four

    if (data.logo_base64) {
      logoPreview.value = `data:image/png;base64,${data.logo_base64}`
    }
  } catch (error: any) {
    console.error('Erro ao carregar parceiro', error.response?.data || error.message)
    Dialog.getInstance().toastError('Não foi possível carregar os dados do parceiro')
  } finally {
    loading.value = false
  }
}

const onSubmit = async () => {
  if (!form.value.nome || !form.value.cpf_cnpj) return
  loading.value = true

  try {
    const formData = new FormData()
    formData.append('name', form.value.nome)
    formData.append('squad_id', form.value.squad_id)
    formData.append('email', form.value.email)
    formData.append('cpf_cnpj', form.value.cpf_cnpj)
    formData.append('phone', form.value.phone ?? '')
    formData.append('active', form.value.active ? '1' : '0')
    formData.append('firstStep', form.value.firstStep)
    formData.append('secondStep', form.value.secondStep)
    formData.append('thirdStep', form.value.thirdStep)
    formData.append('fourthStep', form.value.fourthStep)
    if (form.value.logo) formData.append('logo', form.value.logo)

    if (id) {
      formData.append('_method', 'PUT')
      await API.post(`partners/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      Dialog.getInstance().toastSuccess('Parceiro atualizado com sucesso!')
    } else {
      await API.post('partners', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      Dialog.getInstance().toastSuccess('Parceiro criado com sucesso!')
    }

    navigateTo('search')
  } catch (error: any) {
     const responseStatus = error?.status ?? error?.response?.status;
    if (responseStatus === 422) {
      Object.entries(error.response.data.errors).map(([field, responseErrors]) => {
        errors.value[field] = responseErrors[0];
      });
    } else {
      console.error('Erro ao salvar parceiro', error.response?.data || error.message)
      Dialog.getInstance().toastError('Ocorreu um erro ao salvar parceiro')
    }
  } finally {
    loading.value = false
  }
}

const onFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    form.value.logo = target.files[0]
    logoPreview.value = URL.createObjectURL(target.files[0])
  }
}
</script>

<template>
  <div>
    <h1 class="text-3xl dark:text-white mb-4">Cadastro de parceiro</h1>

    <Tabs v-model="activeTab" :tabs="[
      { key: 'general', label: 'Dados Gerais' },
      { key: 'users', label: 'Usuários', disabled: !id }
    ]" />

    <div v-if="activeTab === 'general'">
      <TabData :form="form" :logo-preview="logoPreview" @file-change="onFileChange" @submit="onSubmit" :errors="errors" />
    </div>
    <div v-else-if="activeTab === 'users'">
      <TabUsers :partner-id="id"/>
    </div>

    <div class="flex justify-between gap-4 mt-4">
      <Button variant="secondary" @click="navigateTo('search')">Voltar</Button>
      <Button variant="primary" type="submit" form="orgForm">Salvar</Button>
    </div>
  </div>
</template>
