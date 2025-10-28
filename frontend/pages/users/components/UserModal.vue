<script setup lang="ts">
import { IconEye, IconEyeOff } from '@tabler/icons-vue'
import { ref, watch, computed } from 'vue'
import AutoComplete from '~/components/Generics/AutoComplete.vue'
import Button from '~/components/Generics/Button.vue'
import Input from '~/components/Generics/Input.vue'
import Select from '~/components/Generics/Select.vue'
import Dialog from '~/scripts/Dialog'
import API from '~/scripts/net/API'

interface ModalUser {
  id?: number
  name: string
  email: string
  tenant?: string
  partnerId?: number
  role?: object
  active?: string
}

const props = defineProps<{ user?: ModalUser, tenant?: string, partnerId?: number }>()
const baseUrl = computed(() => {
  return props.tenant
    ? `/admin/${props.tenant}/users`
    : `/users`
})
const emit = defineEmits(['close', 'saved'])
const name = ref('')
const email = ref('')
const password = ref('')
const role = ref(0)
const loading = ref(false)
const showPassword = ref(false)
const errors = ref({});
const active = ref('1');

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

watch(
  () => props.user,
  (u) => {
    name.value = u?.name || ''
    email.value = u?.email || ''
    role.value = u?.role?.id || null
    active.value = u?.active || '1'
    password.value = ''
  },
  { immediate: true }
)

const close = () => {
  emit('close')
  name.value = ''
  email.value = ''
  password.value = ''
}
const saveUser = async () => {

  loading.value = true
  try {
    if (props.user?.id) {
      await API.put(`${baseUrl.value}/${props.user.id}`, {
        name: name.value,
        email: email.value,
        role_id: role.value,
        partner_id: props.partnerId,
        active: active.value
      })
    } else {
      await API.post(baseUrl.value, {
        name: name.value,
        email: email.value,
        password: password.value,
        role_id: role.value,
        partner_id: props.partnerId,
        active: active.value
      })
    }
    emit('saved')
    close()
  } catch (error: any) {
    const responseStatus = error?.status ?? error?.response?.status;
    if (responseStatus === 422) {
      Object.entries(error.response.data.errors).map(([field, responseErrors]) => {
        errors.value[field] = responseErrors[0];
      });
    } else {
      console.error('Erro ao salvar squad', error.response?.data || error.message)
      Dialog.getInstance().toastError('Ocorreu um erro ao salvar usuário')
    }
  } finally {
    loading.value = false
  }
}
const title = computed(() => (props.user ? 'Editar Usuário' : 'Novo Usuário'))
const rolesEndpoint = computed(() => {
  if (props.tenant) {
    return 'roles?type=organization';
  } else if (props.partnerId) {
    return 'roles?type=partner';
  }
  return 'roles'; 
});
</script>

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-surface-secondary-dark rounded-lg p-6 w-full max-w-md">
      <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">{{ title }}</h2>
      <div class="space-y-4">
        <Input v-model="name" :placeholder="'Nome'" :error="errors.name" />
        <Input v-model="email" :type="'email'" :placeholder="'Email'" :error="errors.email" />
        <div class="relative">
          <Input v-model="password"  :type="showPassword ? 'text' : 'password'" :placeholder="'Senha'"
            :error="errors.password" />

          <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            @click="togglePassword">
            <IconEye v-if="!showPassword" class="w-5 h-5" />
            <IconEyeOff v-else class="w-5 h-5" />
          </button>
        </div>
        <Select class-name="w-full" v-model="active" :options="[
          { label: 'Ativo', value: '1' },
          { label: 'Inativo', value: '0' },
        ]"></Select>
        <AutoComplete v-model="role" :endpoint="rolesEndpoint"  valueKey="id" labelKey="name"
          placeholder="Tipo de acesso" :error="errors.role_id" />
      </div>
      <div class="mt-6 flex justify-end gap-2">
        <Button variant="secondary" @click="close">Cancelar</Button>
        <Button variant="primary" :disabled="loading" @click="saveUser">Salvar</Button>
      </div>
    </div>
  </div>
</template>
