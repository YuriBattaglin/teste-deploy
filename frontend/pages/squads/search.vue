<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { setPageTitle } from '~/scripts/utils/general'
import DataTable from '~/components/Generics/DataTable.vue'

onMounted(() => {
  setPageTitle('Squads')
})

const handleEdit = (squad: any) => {
  navigateTo(`register?id=${squad.id}`)
}

const handleNew = () => {
  navigateTo(`register`)
}

const columns = [
  { key: "name", label: "Name" },
  { key: "description", label: "Descrição" },
  { 
    key: "active", 
    label: "Status",
  }
]
</script>

<template>
  <DataTable :columns="columns" :end-point="'squads'" :object-name="'squad'" :show-title="true" :viewMode="'cards'" @edit="handleEdit" @new="handleNew" />
</template>
