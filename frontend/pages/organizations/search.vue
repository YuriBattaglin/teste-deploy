<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { setPageTitle } from '~/scripts/utils/general'
import DataTable from '~/components/Generics/DataTable.vue'

onMounted(() => {
  setPageTitle('Organizações')
})

const handleEdit = (object: any) => {
  navigateTo(`register?id=${object.tenant_id}`)
}

const handleNew = () => {
  navigateTo(`register`)
}

const statusColors: Record<string, string> = {
  'Ativo': 'bg-green-100 text-green-800',
  'Inativo': 'bg-red-100 text-red-800',
}

const columns = [
  { key: "description", label: "Descrição" },
  { key: "cpf_cnpj", label: "CPF/CNPJ" },
  { key: "users_count", label: "Usuários" },
  { 
    key: "active", 
    label: "Status",
  },
  { key: "actions_inactive", label: "Ações" }
]
</script>

<template>
  <div class="mt-7">
    <DataTable :columns="columns" :end-point="'admin/tenants'" :object-name="'organização'" :show-title="true" :viewMode="'table'" @edit="handleEdit" @new="handleNew" />
  </div>
</template>
