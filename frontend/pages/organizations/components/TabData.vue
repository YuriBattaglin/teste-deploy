<script setup lang="ts">
import { IconFileUpload } from '@tabler/icons-vue'
import { Ref } from 'vue'
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

const props = defineProps<{
  form: Ref<FormData>
  logoPreview: string | null
}>()

const emit = defineEmits<{
  (e: 'file-change', event: Event): void
  (e: 'submit'): void
}>()

const updateCnpjData = (data: any) => {
props.form.nome = data.nome
props.form.email = data.email
props.form.phone = data.telefone
}
</script>

<template>
  <form id="orgForm" @submit.prevent="emit('submit')" class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="flex justify-center items-start">
      <label for="logoUpload"
        class="border border-border-color dark:border-border-color-dark flex flex-col items-center justify-center"
        style="height: 160px; width: 160px; cursor: pointer;">
        <input type="file" accept="image/*" class="hidden" id="logoUpload" @change="emit('file-change', $event)" />
        <template v-if="props.logoPreview">
          <img :src="props.logoPreview" alt="Logo Preview" class="object-cover rounded"
            style="height: 160px; width: 160px;" />
        </template>
        <template v-else>
          <IconFileUpload class="mb-2 text-gray-400" />
          <span class="text-gray-400 text-sm text-center">Clique para adicionar</span>
        </template>
      </label>
    </div>

    <div class="md:col-span-1 flex flex-col justify-center">
      <Input v-model="props.form.id" :type="'text'" :placeholder="'Digite a identificação da organização'"
        :label="'Identificação'" />
    </div>
    <div class="md:col-span-1 flex flex-col justify-center">
      <Input v-model="props.form.nome" :type="'text'" :placeholder="'Digite o nome da organização'" :label="'Nome'" />
    </div>
    <div class="md:col-span-1 flex flex-col justify-center">
      <Input v-model="props.form.cpf_cnpj" :type="'text'" :placeholder="'00.000.000/0000-00'" :use-mask="'cpf-cnpj'"
        :label="'CPF/CNPJ'" @cnpj-data="updateCnpjData" />
    </div>
    <div>
      <Input v-model="props.form.email" :type="'email'" :label="'Email'" :placeholder="'email@empresa.com'" />
    </div>
    <div>
      <Input v-model="props.form.phone" type="text" :placeholder="'(00) 00000-0000'" :use-mask="'phone'"
        :label="'Telefone'" />
    </div>
    <div>
      <label class="block text-sm font-medium mb-2 dark:text-gray-300">Status</label>
      <select v-model="props.form.active"
        class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none dark:bg-surface-dark dark:text-white">
        <option :value="1">Ativa</option>
        <option :value="0">Inativa</option>
      </select>
    </div>
  </form>
</template>
