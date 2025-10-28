<script setup lang="ts">
import Card from '~/components/Card.vue'
import API from '~/scripts/net/API'
import Dialog from '~/scripts/Dialog'
import { ref, computed, onMounted } from 'vue'
import { IconBuilding, IconUsers, IconHeartHandshake } from '@tabler/icons-vue'

onMounted(async () => {
  await fetchDashboardData();
})

interface OrganizationData {
  id: number;
  description: string;
  cpf_cnpj: string;
  email: string;
  phone?: string | null;
  active: boolean;
  logo?: string | null;
  created_at?: string;
  updated_at?: string;
}

interface TenantOrganization {
  tenant_id: string;
  organization: OrganizationData;
  users_count: number;
}

const organizations = ref<TenantOrganization[]>([])

const fetchDashboardData = async () => {
  try {
    const response = await API.get('/admin/tenants')
    organizations.value = response.data.data.data
  } catch (error: any) {
    console.error('Erro ao carregar dados do dashboard', error.response?.data || error.message)
    Dialog.getInstance().toastError('Não foi possível carregar os dados da dashboard')
  }
}

const totalOrganizations = computed(() => organizations.value.length)
const totalUsers = computed(() =>
  organizations.value.reduce((sum, org) => sum + org.users_count, 0)
)
const totalPartners = computed(() =>
  organizations.value.length
)

const stats = computed(() => [
  {
    title: "Total de Organizações Ativas",
    value: totalOrganizations.value,
    icon: IconBuilding,
  },
  {
    title: "Total de Parceiros Ativos",
    value: totalPartners.value,
    icon: IconHeartHandshake,
  },
  {
    title: "Total de Usuários Ativos",
    value: totalUsers.value,
    icon: IconUsers,
  },
])

</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <Card
      v-for="stat in stats"
      :key="stat.title"
      :title="stat.title"
      :value="stat.value"
      :icon="stat.icon"
    />
  </div>
</template>
