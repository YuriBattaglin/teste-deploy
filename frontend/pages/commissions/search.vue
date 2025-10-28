<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { setPageTitle } from '~/scripts/utils/general'
import DataTable from '~/components/Generics/DataTable.vue'

onMounted(() => {
  setPageTitle('Comissões')
})

const handleEdit = (partner: any) => {
  navigateTo(`register?id=${partner.id}`)
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
    {
    key: "squad",
    label: "Squad",
    formatter: (row: any) => row?.name ?? ''
  },
  { key: "users_count", label: "Usuários" },
  { 
    key: "active", 
    label: "Status",
  },
  { key: "actions_inactive", label: "Ações" }
]
</script>

<template>
  <DataTable :columns="columns" :end-point="'partners'" :object-name="'comissão'" :show-title="true" :viewMode="'table'" @edit="handleEdit" @new="handleNew" />
</template>
