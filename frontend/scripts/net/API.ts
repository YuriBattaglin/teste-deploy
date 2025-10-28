import axios from 'axios'
import Cookie from 'js-cookie'

const API = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
})

API.interceptors.request.use(config => {
  const account = typeof window !== 'undefined' ? Cookie.get('scale_tenant') : null
  if (account) {
    config.headers['X-Tenant'] = account
  }

  const token = typeof window !== 'undefined' ? Cookie.get('scale_token') : null
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
})

API.interceptors.response.use(
  response => response,
  error => {
    if (error.response?.status === 401) {
      Cookie.remove('scale_token')
      Cookie.remove('scale_tenant')

      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default API
