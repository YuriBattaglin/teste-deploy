<script setup lang="ts">
import { ref } from 'vue'
import { IconBuilding, IconEye, IconEyeOff, IconLock, IconMail } from "@tabler/icons-vue"
import { setPageTitle } from '~/scripts/utils/general'
import API from '~/scripts/net/API';
import Cookie from 'js-cookie';
import Button from '~/components/Generics/Button.vue';
import Logo from '~/components/Logo.vue';
import Input from '~/components/Generics/Input.vue';
import Dialog from '~/scripts/Dialog';

definePageMeta({
  layout: 'auth'
})

onMounted(() => {
  setPageTitle('Login')
  const saved = localStorage.getItem('darkMode')
  const isDark = saved === 'true'
  document.documentElement.classList.toggle('dark', isDark)
  company.value = localStorage.getItem('lastTenant') ?? '';
})

const showPassword = ref(false)
const isLoading = ref(false)
const password = ref('')
const email = ref('')
const company = ref('')
const remember = ref('off')
const errors = ref({});

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const onSubmit = async (e: Event) => {
  e.preventDefault()
  if(isLoading.value) return;

  try {
    errors.value = {};
    isLoading.value = true
    const response = await API.post('/login',
      {
        tenant: company.value,
        email: email.value,
        password: password.value,
        remember: remember.value === 'on' ? 1 : 0
      },
    );
    Cookie.set('scale_token', response.data.access_token);
    Cookie.set('scale_tenant', company.value);
    localStorage.setItem('lastTenant', company.value)

    navigateTo('dashboard')
  } catch (error: any) {
    const responseStatus = error?.status ?? error?.response?.status;
    if (responseStatus === 422) {
      Object.entries(error.response.data.errors).map(([field, responseErrors]) => {
        errors.value[field] = responseErrors[0];
      });
    } else {
      await Dialog.getInstance().toastError('Dados inválidos para a autenticação!');
    }
  } finally {
    isLoading.value = false
  }
}

</script>


<template>
  <main class="flex items-center justify-center min-h-screen dark:bg-surface-dark bg-surface">
    <div
      class="shadow-2xl rounded-xl max-w-md w-full mx-4 dark:bg-surface-secondary-dark dark:text-white bg-surface-secondary text-gray-900">
      <div class="p-8 lg:p-8">
        <div class="mb-6 text-center">
          <Logo class="mb-6 mt-3" />
          <h2 class="text-2xl font-bold mb-2 dark:text-white text-gray-900">Seja bem-vindo</h2>
          <p class="text-sm dark:text-gray-400 text-gray-600">Faça login para acessar o sistema</p>
        </div>

        <form class="space-y-5" @submit="onSubmit">
          <div class="space-y-2">
            <label for="company" class="block text-sm font-medium dark:text-gray-300 text-gray-700">Empresa</label>
            <div class="relative">
              <Input :type="'text'" id="company" name="company" :placeholder="'Nome da empresa'"
                v-model="company" :error="errors?.tenant" :icon="IconBuilding"/>
            </div>
          </div>

          <div class="space-y-2">
            <label for="email" class="block text-sm font-medium dark:text-gray-300 text-gray-700">Email</label>
            <div class="relative">
              <Input :type="'email'" id="email" name="email"  :placeholder="'seu@email.com'"
                v-model="email" :error="errors?.email" :icon="IconMail"/>
            </div>
          </div>

          <div class="space-y-2">
            <label for="password" class="block text-sm font-medium dark:text-gray-300 text-gray-700">Senha</label>
            <div class="relative">
              <Input :type="showPassword ? 'text' : 'password'" id="password" name="password" v-model="password"
                :placeholder="'••••••••'" :error="errors?.password" :icon="IconLock"/>
              <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                @click="togglePassword">
                <IconEye v-if="!showPassword" class="w-5 h-5" />
                <IconEyeOff v-else class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" id="remember" name="remember" v-model="remember"
                class="h-4 w-4 text-color-scale focus:ring-color-scale border-gray-300 rounded" />
              <span class="ml-2 text-sm dark:text-gray-300 text-gray-700">Lembrar de mim</span>
            </label>
            <a href="#" class="text-color-scale hover:text-color-scale-hover text-sm ">Esqueceu a senha?</a>
          </div>

          <Button type="submit" variant="primary"
            class="w-full py-3 px-4 font-medium flex items-center justify-center gap-2">
            <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
              <path class="opacity-75" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                d="M22 12a10 10 0 0 1-10 10" />
            </svg>
            <span>{{ isLoading ? 'Entrando...' : 'Entrar' }}</span>
          </Button>
          <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-sm dark:text-gray-400 text-gray-500">
              <span class="px-2">ou</span>
            </div>
          </div>
        </form>

        <div class="mt-5 text-center">
          <p class="text-sm dark:text-gray-400 text-gray-600">
            Não tem uma conta?
            <a href="#" class="text-color-scale hover:text-color-scale-hover">Entre em contato</a>
          </p>
        </div>
      </div>
    </div>
  </main>
</template>
