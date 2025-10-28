<script setup lang="ts">
import { IconFileUpload } from '@tabler/icons-vue'
import { setPageTitle } from '~/scripts/utils/general'
import { onMounted, ref } from 'vue'
import Dialog from '~/scripts/Dialog'
import { useAuth } from '~/composables/useAuth' 
import Input from '~/components/Generics/Input.vue'

interface FormData {
  id: string
  nome: string
  cpf_cnpj: string
  email: string
  phone: string
  active: boolean | number
  logo: File | null
}

const { user } = useAuth() 

const form = ref<FormData>({
  id: '',
  nome: '',
  cpf_cnpj: '',
  email: '',
  phone: '',
  active: 1,
  logo: null
})

const logoPreview = ref<string | null>(null)

onMounted(() => {
  setPageTitle('Cadastro de organização')

  if (user.value) {
    loadUserData()
  }
})

const loadUserData = () => {
  try {
    form.value.id = user.value.tenant.id ?? ''
    form.value.nome = user.value.partner?.name ?? user.value.organization?.name ?? ''
    form.value.cpf_cnpj = user.value.partner?.cpf_cnpj ?? user.value.organization?.cpf_cnpj ?? ''
    form.value.email = user.value.partner?.email ?? user.value.organization?.email ?? ''
    form.value.phone = user.value.partner?.phone ?? user.value.organization?.phone ?? ''
    form.value.active = user.value.partner?.active ?? user.value.organization?.active ? 1 : 0
    if (user.value.organization?.logo || user.value.partner?.logo) {
      logoPreview.value = `data:image/png;base64,${user.value.partner?.logo ?? user.value.organization?.logo}`
    }
  } catch (error: any) {
    console.error('Erro ao carregar dados do usuário', error)
    Dialog.getInstance().toastError('Não foi possível carregar os dados do usuário')
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
    <h1 class="text-3xl dark:text-white mb-6">Meu perfil</h1>
    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-6">
      <form id="profileForm" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="flex justify-center items-start">
          <label for="logoUpload"
            class="border border-border-color dark:border-border-color-dark flex flex-col items-center justify-center"
            style="height: 160px; width: 160px; cursor: pointer;">
            <input type="file" accept="image/*" class="hidden" id="logoUpload"
              @change="onFileChange" />
            <template v-if="logoPreview">
              <img :src="logoPreview" alt="Logo Preview" class="object-cover rounded"
                style="height: 160px; width: 160px;" />
            </template>
            <template v-else>
              <IconFileUpload class="mb-2 text-gray-400" />
              <span class="text-gray-400 text-sm text-center">Clique para adicionar</span>
            </template>
          </label>
        </div>

        <div class="md:col-span-1 flex flex-col justify-center">
          <Input v-model="form.id" :type="'text'" :label="'Identificação'" disabled />
        </div>
        <div class="md:col-span-1 flex flex-col justify-center">
          <Input v-model="form.nome" :type="'text'" :placeholder="'Digite o nome da organização'" :label="'Nome'" disabled/>
        </div>
        <div class="md:col-span-1 flex flex-col justify-center">
          <Input v-model="form.cpf_cnpj" :type="'text'" :label="'CPF/CNPJ'" :use-mask="'cpf-cnpj'" disabled/>
        </div>
        <div>
          <Input v-model="form.email" type="'email'" :label="'E-mail'" disabled/>
        </div>
        <div>
          <Input v-model="form.phone" type="'text'" :label="'Telefone'" :use-mask="'phone'" disabled/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2 dark:text-gray-300">Status</label>
          <select v-model="form.active" disabled
            class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none dark:bg-surface-dark dark:text-white">
            <option :value="1">Ativa</option>
            <option :value="0">Inativa</option>
          </select>
        </div>
      </form>
    </div>

  </div>
</template>
