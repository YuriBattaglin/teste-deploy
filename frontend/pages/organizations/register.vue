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
  id: string
  nome: string
  cpf_cnpj: string
  email: string
  phone: string
  active: boolean | number
  logo: File | null
}

const form = ref<FormData>({
  id: '',
  nome: '',
  cpf_cnpj: '',
  email: '',
  phone: '',
  active: 1,
  logo: null
})

const users = ref<any[]>([])
const logoPreview = ref<string | null>(null)
const loading = ref(false)
const route = useRoute()
const activeTab = ref<'general' | 'users'>('general')
const id = route.query.id as string

onMounted(async () => {
  setPageTitle('Cadastro de organização')

  if (id) {
    await fetchTenant(id)
  }
})

const handleDelete = async (user: any) => {
  await API.delete(`/admin/tenants/${id}/users/${user.id}`)
  await fetchTenant(id)
}

const fetchTenant = async (id: string) => {
  loading.value = true
  try {
    const response = await API.get(`/admin/tenants/${id}`)
    const data = response.data.data
    users.value = data.users || []
    form.value.id = data.id
    form.value.nome = data.name
    form.value.cpf_cnpj = data.cpf_cnpj
    form.value.email = data.email
    form.value.phone = data.phone
    form.value.active = data.active ? 1 : 0

    if (data.logo_base64) {
      logoPreview.value = `data:image/png;base64,${data.logo_base64}`
    }
  } catch (error: any) {
    console.error('Erro ao carregar organização', error.response?.data || error.message)
    Dialog.getInstance().toastError('Não foi possível carregar os dados da organização')
  } finally {
    loading.value = false
  }
}

const onSubmit = async () => {
  if (!form.value.nome || !form.value.cpf_cnpj || !form.value.id) return
  loading.value = true

  try {
    const formData = new FormData()
    formData.append('name', form.value.nome)
    formData.append('email', form.value.email)
    formData.append('cpf_cnpj', form.value.cpf_cnpj)
    formData.append('phone', form.value.phone ?? '')
    formData.append('active', form.value.active ? '1' : '0')
    if (form.value.logo) formData.append('logo', form.value.logo)

    if (id) {
      formData.append('_method', 'PUT')
      await API.post(`admin/tenants/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      Dialog.getInstance().toastSuccess('Organização atualizada com sucesso!')
    } else {
      formData.append('id', form.value.id)
      await API.post('admin/tenants', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      Dialog.getInstance().toastSuccess('Organização criada com sucesso!')
    }

    navigateTo('search')
  } catch (error: any) {
    console.error('Erro ao salvar organização', error.response?.data || error.message)
    Dialog.getInstance().toastError('Ocorreu um erro ao salvar organização')
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
    <h1 class="text-3xl dark:text-white mb-4">Cadastro de organização</h1>

    <Tabs v-model="activeTab" :tabs="[
      { key: 'general', label: 'Dados Gerais' },
      { key: 'users', label: 'Usuários', disabled: !id }
    ]" />

    <div v-if="activeTab === 'general'">
      <div
        class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg  border border-border-color dark:border-border-color-dark p-6 mb-4">
        <TabData :form="form" :logo-preview="logoPreview" @file-change="onFileChange" @submit="onSubmit" />
      </div>
    </div>
    <div v-else-if="activeTab === 'users'">
      <TabUsers :tenant-id="id" />
    </div>

     <div class="flex justify-between gap-4 mt-4">
      <Button variant="secondary" @click="navigateTo('search')">Voltar</Button>
      <Button variant="primary" type="submit" form="orgForm">Salvar</Button>
    </div>
  </div>
</template>
