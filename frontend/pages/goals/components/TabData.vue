<script setup lang="ts">
import { IconFileUpload } from '@tabler/icons-vue'
import { Ref } from 'vue'
import AutoComplete from '~/components/Generics/AutoComplete.vue';
import Input from '~/components/Generics/Input.vue'

const props = defineProps<{
  form: Ref<FormData>
  logoPreview: string | null
  errors: any
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
  <form id="orgForm" @submit.prevent="emit('submit')">
    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg  border border-border-color dark:border-border-color-dark p-6 mb-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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
          <Input v-model="props.form.nome" :type="'text'" :placeholder="'Digite o nome do parceiro'"
            :label="'Nome do Parceiro'"  :error="errors.nome" />
        </div>
        <div class="md:col-span-1 flex flex-col justify-center">
          <Input v-model="props.form.cpf_cnpj" :type="'text'" :placeholder="'00.000.000/0000-00'" :label="'CPF/CNPJ'"
            :use-mask="'cpf-cnpj'" @cnpj-data="updateCnpjData"  :error="errors.cpf_cnpj" />
        </div>
              <div class="md:col-span-1 flex flex-col justify-center">
            <AutoComplete v-model="props.form.squad_id" label="Squad Responsável" endpoint="squads" valueKey="id"
            labelKey="name" placeholder="Buscar squad..." :error="errors.squad_id" />
          </div>
        <div>
          <Input v-model="props.form.email" :type="'email'" :placeholder="'email@empresa.com'" :label="'E-mail'" :error="errors.email" />
        </div>
        <div>
          <Input v-model="props.form.phone" :type="'text'" :placeholder="'(00) 00000-0000'" :label="'Telefone'"
            :use-mask="'phone'" :error="errors.phone"/>
        </div>
        <div>
          <label class="block text-sm font-medium mb-2 dark:text-gray-300">Status</label>
          <select v-model="props.form.active"
            class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none dark:bg-surface-dark dark:text-white">
            <option :value="1">Ativa</option>
            <option :value="0">Inativa</option>
          </select>
        </div>
      </div>
  </div>

  <div
    class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-4">

    <div class="flex justify-between items-center mb-4">
      <h3 class="text-lg text-gray dark:text-white">Cadastro de etapas</h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="md:col-span-1 flex flex-col justify-center">
        <Input v-model="props.form.firstStep" :type="'text'" :placeholder="'Digite a descrição da etapa 1'" :label="'Etapa 1'"
          disabled :error="errors.firstStep" />
      </div>
      <div class="md:col-span-1 flex flex-col justify-center">
        <Input v-model="props.form.secondStep" :type="'text'" :placeholder="'Digite a descrição da etapa 2'" :label="'Etapa 2'" :error="errors.secondStep" />
      </div>
      <div class="md:col-span-1 flex flex-col justify-center">
        <Input v-model="props.form.thirdStep" :type="'text'" :placeholder="'Digite a descrição da etapa 3'" :label="'Etapa 3'"  :error="errors.thirdStep"/>
      </div>
      <div>
        <Input v-model="props.form.fourthStep" :type="'text'" :placeholder="'Digite a descrição da etapa 4'" :label="'Etapa 4'" :error="errors.fourthStep"
          disabled />
      </div>
    </div>
  </div>
  </form>
</template>
