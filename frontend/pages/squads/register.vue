<script setup lang="ts">
import { IconTrash } from '@tabler/icons-vue'
import { setPageTitle } from '~/scripts/utils/general'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import Button from '~/components/Generics/Button.vue'
import Input from '~/components/Generics/Input.vue'
import API from '~/scripts/net/API'
import Dialog from '~/scripts/Dialog'
import SelectUsersModal from './components/SelectUsersModal.vue'
import { initialOf } from '~/scripts/utils/string'
import Textarea from '~/components/Generics/Textarea.vue'

interface SquadMember {
  id: number
  name: string
  role?: string
  avatar?: string
}

interface SquadPartner {
  id: number
  description: string
}

interface FormData {
  name: string
  description: string
  leader_id: number | null
  active: boolean
  members: SquadMember[]
  partners: SquadPartner[]
}

const form = ref<FormData>({
  name: '',
  description: '',
  leader_id: null,
  active: true,
  members: [],
  partners: []
})

const loading = ref(false)
const showUserModal = ref(false)
const route = useRoute()
const id = Number(route.query.id as string)
const errors = ref({});

onMounted(async () => {
  setPageTitle('Cadastro de Squad')
  if (id) await fetchSquad(id)
})

const fetchSquad = async (id: number) => {
  loading.value = true
  try {
    errors.value = {};
    const response = await API.get(`/squads/${id}`)
    const data = response.data.data

    form.value.name = data.name
    form.value.description = data.description
    form.value.leader_id = data.leader_id ?? null
    form.value.active = data.active
    form.value.members = data.members || []
    form.value.partners = data.partners || []
  } catch (error: any) {
    console.error('Erro ao carregar squad', error.response?.data || error.message)
    Dialog.getInstance().toastError('Não foi possível carregar os dados do squad')
  } finally {
    loading.value = false
  }
}

const onSubmit = async () => {
  loading.value = true

  try {
    const payload = {
      name: form.value.name,
      description: form.value.description,
      leader_id: form.value.leader_id,
      active: form.value.active ? 1 : 0,
      members: form.value.members.map(u => u.id),
    }

    if (id) {
      await API.put(`/squads/${id}`, payload)
      Dialog.getInstance().toastSuccess('Squad atualizado com sucesso!')
    } else {
      await API.post('/squads', payload)
      Dialog.getInstance().toastSuccess('Squad criado com sucesso!')
    }

    navigateTo('search')
  } catch (error: any) {
    const responseStatus = error?.status ?? error?.response?.status;
    if (responseStatus === 422) {
      Object.entries(error.response.data.errors).map(([field, responseErrors]) => {
        errors.value[field] = responseErrors[0];
      });
    } else {
      console.error('Erro ao salvar squad', error.response?.data || error.message)
      Dialog.getInstance().toastError('Ocorreu um erro ao salvar squad')
    }
  } finally {
    loading.value = false
  }
}

const removeMember = (index: number) => {
  form.value.members.splice(index, 1)
}

const addMember = () => {
  if (form.value.members.length >= 5) return
  showUserModal.value = true
}
</script>

<template>
  <div>
    <h1 class="text-3xl dark:text-white mb-4">Cadastro de Squad</h1>

    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-4">
      <form id="squadForm" @submit.prevent="onSubmit" class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="md:col-span-1 flex flex-col justify-center">
          <Input v-model="form.name" type="text" placeholder="Digite o nome do squad" label="Nome do Squad"
            :error="errors.name" />
        </div>

        <div class="md:col-span-1 flex flex-col justify-center">
          <label class="block text-sm font-medium mb-2 dark:text-gray-300">Status</label>
          <select v-model="form.active"
            class="w-full px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg focus:outline-none dark:bg-surface-dark dark:text-white">
            <option :value="true">Ativo</option>
            <option :value="false">Inativo</option>
          </select>
        </div>

        <div class="md:col-span-3">
          <Textarea v-model="form.description" label="Descrição" :rows="6"
            placeholder="Descreva os objetivos e responsabilidades do squad..." />

        </div>
      </form>
    </div>

    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-4">

      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg text-gray dark:text-white">Parceiros do Squad</h3>
      </div>

      <div class="grid grid-cols-3 sm:grid-cols-6 md:grid-cols-8 gap-3">
        <template v-if="form.partners.length">
          <div v-for="partner in form.partners" :key="partner.id"
            class="flex items-center justify-center px-3 py-2 border border-border-color dark:border-border-color-dark rounded-lg bg-surface dark:bg-surface-dark text-sm font-medium text-gray-800 dark:text-gray-200 shadow-sm">
            {{ partner.description }}
          </div>
        </template>
        <template v-else>
          <div class="col-span-full flex items-center justify-center py-6 text-gray-500 dark:text-gray-400">
            Nenhum parceiro associado a este squad
          </div>
        </template>
      </div>
    </div>

    <div
      class="bg-surface-secondary dark:bg-surface-secondary-dark rounded-lg border border-border-color dark:border-border-color-dark p-6 mb-4">

      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg text-gray dark:text-white">Membros do Squad</h3>

        <Button variant="primary" @click="addMember" :disabled="form.members.length >= 5"
          :title="form.members.length >= 5 ? 'Limite de 5 membros por squad atingido' : ''">
          <span class="text-lg leading-none mr-1">+</span>
          <span>Adicionar membros</span>
        </Button>
      </div>

      <div class="space-y-2">
        <template v-if="form.members.length">
          <div v-for="(member, index) in form.members" :key="member.id"
            class="flex items-center justify-between px-4 py-2 border border-border-color dark:border-border-color-dark rounded-lg">
            <div class="flex items-center space-x-4">
              <div
                class="mr-4 flex h-10 w-10 items-center justify-center rounded-full text-white font-bold bg-color-scale">
                {{ initialOf(member.name) }}
              </div>
              <div>
                <p class="font-medium text-gray-800 dark:text-white flex items-center space-x-2">
                  {{ member.name }}
                  <span v-if="member.id === form.leader_id"
                    class="bg-green-100 text-green-800 px-2 py-0.5 rounded text-xs font-medium ms-2">
                    Líder
                  </span>

                  <span v-else @click.stop="form.leader_id = member.id"
                    class=" px-2 py-0.5 rounded text-xs font-medium ms-2 cursor-pointer">
                    Tornar líder
                  </span>

                </p>
                <p class="text-sm text-gray-600 dark:text-muted">{{ member.role }}</p>
              </div>

            </div>

            <div class="flex items-center space-x-2">
              <button type="button" class="text-red-600 hover:text-red-800" @click="removeMember(index)">
                <IconTrash />
              </button>
            </div>
          </div>
        </template>

        <template v-else>
          <div class="flex items-center justify-center px-4 py-6 rounded-lg text-gray-500 dark:text-gray-400">
            Nenhum membro cadastrado
          </div>
        </template>
      </div>

    </div>

    <div class="flex justify-between">
      <Button variant="secondary" @click="navigateTo('search')">Voltar</Button>
      <Button variant="primary" type="submit" form="squadForm">Salvar</Button>
    </div>

    <Transition name="fade">
      <SelectUsersModal v-if="showUserModal" v-model="form.members" @update:modelValue="form.members = $event"
        @close="showUserModal = false" :disabled="form.members.length >= 5" />
    </Transition>
  </div>
</template>