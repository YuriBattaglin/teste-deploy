// src/composables/useAuth.ts
import { ref, readonly } from 'vue'
import API from '../scripts/net/API'
import Cookie from 'js-cookie'
const user = ref(null)
const tenant = ref(null)

export const useAuth = () => {

  const initAuth = async (router?: any) => {
    try {
      const res = await API.get('/me');
      user.value = res.data.data
      tenant.value = Cookie.get('scale_tenant');
    } catch (err) {
      console.error('Não autenticado', err)
      user.value = null
      tenant.value = null

      // se router passado, usa ele, senão fallback
      if (router && router.push) {
        router.push('/login')
      } else {
        window.location.href = '/login'
      }
    }
  }

  const logout = async (router?: any) => {
    await API.post('/logout');
    user.value = null
    tenant.value = null
    Cookie.remove('scale_token')
    Cookie.remove('scale_tenant')
    if (router && router.push) {
      router.push('/login')
    } else {
      window.location.href = '/login'
    }
  }

  return {
    user: readonly(user),
    tenant: readonly(tenant),
    initAuth,
    logout
  }
}
