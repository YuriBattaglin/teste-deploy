<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { setPageTitle } from '~/scripts/utils/general'
import DataTable from '~/components/Generics/DataTable.vue'
import UserModal from './components/UserModal.vue'

onMounted(() => {
  setPageTitle('Usuários')
})

const props = withDefaults(defineProps<{
  partnerId?: number
  tenantId?: string
  showTitle?: boolean
}>(), {
  showTitle: true
})

const selectedUser = ref(null);
const isModalOpen = ref(false);
const dataTableRef = ref(null)
const filterPartner = props.partnerId ? `partner_id=${props.partnerId}` : ''

const handleEdit = (user: any) => {
  selectedUser.value = { ...user }
  isModalOpen.value = true
}

const handleNew = () => {
  selectedUser.value = null
  isModalOpen.value = true
}

const handleSaved = async () => {
  isModalOpen.value = false
  dataTableRef.value?.reload()
}

const columns = [
  {key: "name", label: "Nome"},
  {key: "email", label: "Email"},
  {key: "active", label: "Ativo"},
  {key: "role", label: "Acesso", formatter: (row: any) => row?.name ?? ''},
  {key: "actions_inactive", label: "Ações"}
]
</script>

<template>
    <Transition name="fade">
      <UserModal v-if="isModalOpen" :user="selectedUser || undefined" @close="isModalOpen = false" @saved="handleSaved" :tenant="props.tenantId" :partner-id="partnerId"/>
    </Transition>
  <DataTable ref="dataTableRef" :columns="columns" :end-point="props.tenantId ? `admin/${props.tenantId}/users` : 'users'" :object-name="'usuário'" :show-title="showTitle" :filter="filterPartner" :viewMode="'table'" @edit="handleEdit" @new="handleNew" />
</template>
