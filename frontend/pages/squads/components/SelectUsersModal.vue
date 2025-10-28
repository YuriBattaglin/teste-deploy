<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import Button from '~/components/Generics/Button.vue'
import Dialog from '~/scripts/Dialog'
import API from '~/scripts/net/API'

interface User {
  id: number
  name: string
  email: string
  avatar?: string
  role?: string
}

const props = defineProps<{
  modelValue: User[]
}>()

const emit = defineEmits(['update:modelValue', 'close'])

const users = ref<User[]>([])
const selectedUsers = ref<User[]>([])
const loading = ref(false)

const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await API.get('/users')
    const allUsers: User[] = response.data.data.data

    users.value = allUsers.filter(u =>
      !props.modelValue.some(m => m.id === u.id)
    )

    selectedUsers.value = []
  } catch (error: any) {
    console.error('Erro ao buscar usuários', error.response?.data || error.message)
    Dialog.getInstance().toastError('Não foi possível buscar usuários')
  } finally {
    loading.value = false
  }
}

const maxMembers = 5;
const toggleSelect = (user: User) => {
  const exists = selectedUsers.value.find(u => u.id === user.id)
  if (exists) {
    selectedUsers.value = selectedUsers.value.filter(u => u.id !== user.id)
  } else {
    if (props.modelValue.length + selectedUsers.value.length >= maxMembers) {
      Dialog.getInstance().toastError(`Só é permitido ter no máximo ${maxMembers} usuários por squad.`)
      return
    }
    selectedUsers.value.push(user)
  }
}
const confirmSelection = () => {
  const newMembers = selectedUsers.value.filter(
    u => !props.modelValue.find(m => m.id === u.id)
  )
  emit('update:modelValue', [...props.modelValue, ...newMembers])
  close()
}

const close = () => {
  emit('close')
  selectedUsers.value = []
}

onMounted(() => {
  fetchUsers()
})
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-surface-secondary-dark rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">Selecionar Usuários</h2>

      <div class="max-h-64 overflow-y-auto space-y-2">
        <div v-for="user in users" :key="user.id"
          class="flex items-center justify-between p-2 border-b border-border-color dark:border-border-color-dark cursor-pointer hover:bg-color-scale-hover"
          @click="toggleSelect(user)">
          <div class="flex items-center gap-2">
            <img v-if="user.avatar" :src="user.avatar" class="w-8 h-8 rounded-full" />
            <div class="text-gray-800 dark:text-white">{{ user.name }}</div>
          </div>
          <input type="checkbox" :value="user.id" @change.stop="toggleSelect(user)"
            :checked="selectedUsers.some(u => u.id === user.id)"
            :disabled="!selectedUsers.some(u => u.id === user.id) && props.modelValue.length + selectedUsers?.length >= maxMembers" />
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-2">
        <Button variant="secondary" @click="close">Cancelar</Button>
        <Button variant="primary" :disabled="loading || selectedUsers?.length <= 0"
          @click="confirmSelection">Adicionar</Button>
      </div>
    </div>
  </div>
</template>
