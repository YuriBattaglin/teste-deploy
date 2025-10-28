<script setup lang="ts">
import { ref, computed } from "vue";
import { IconFilter } from "@tabler/icons-vue";
import Button from "~/components/Generics/Button.vue";
import TabGeneral from "./components/TabGeneral.vue";
import TabPartner from "./components/TabPartner.vue";
import Select from "~/components/Generics/Select.vue";
import { setPageTitle } from "~/scripts/utils/general";

onMounted(() => {
  setPageTitle('Painel PGM')
})
const { user } = useAuth()
const activeTab = ref("general");
const squadOrPartnerFilter = ref("");
const monthFilter = ref("");
const yearFilter = ref("");

const squadOptions = [
  { label: 'Selecione um Squad', value: '' },
  { label: 'Alpha Squad', value: 'alpha' },
  { label: 'Beta Squad', value: 'beta' },
  { label: 'Gamma Squad', value: 'gamma' },
  { label: 'Delta Squad', value: 'delta' },
  { label: 'Omega Squad', value: 'omega' }
];

const partnerOptions = [
  { label: 'Selecione um Parceiro', value: '' },
  { label: 'Parceiro A', value: 'partnerA' },
  { label: 'Parceiro B', value: 'partnerB' },
  { label: 'Parceiro C', value: 'partnerC' }
];

const currentOptions = computed(() => activeTab.value === 'general' ? squadOptions : partnerOptions);

const monthOptions = computed(() => [
  { label: 'Mês', value: '' },
  ...Array.from({ length: 12 }, (_, i) => ({ label: (i + 1).toString(), value: (i + 1).toString() }))
]);

const yearOptions = [
  { label: 'Ano', value: '' },
  { label: '2024', value: '2024' },
  { label: '2023', value: '2023' },
  { label: '2022', value: '2022' },
  { label: '2021', value: '2021' }
];

function saveUser() {
  console.log("Aplicar filtros", { squadOrPartnerFilter, monthFilter, yearFilter });
}

function close() {
  squadOrPartnerFilter.value = "";
  monthFilter.value = "";
  yearFilter.value = "";
}
</script>

<template>
  <h1 class="text-3xl dark:text-white mb-4">Painel PGM </h1>

  <div class="flex items-center justify-between mb-6" v-if="!user?.partner?.id">
    <div class="border-b border-border-color dark:border-border-color-dark flex-1">
      <nav class="-mb-px flex space-x-8">
        <button
          class="py-2 px-1 text-sm font-medium border-b-2"
          :class="activeTab === 'general' ? 'border-color-scale text-dark dark:text-white' : 'border-transparent text-gray-500'"
          @click="activeTab = 'general'"
        >
          Geral
        </button>
        <button
          class="py-2 px-1 text-sm font-medium border-b-2"
          :class="activeTab === 'partner' ? 'border-color-scale text-color-dark dark:text-white' : 'border-transparent text-gray-500'"
          @click="activeTab = 'partner'"
        >
          Parceiro
        </button>
      </nav>
    </div>

    <div class="flex items-center space-x-3 ml-6">
      <div class="flex items-center space-x-2">
        <IconFilter class="w-4 h-4 text-gray-600 dark:text-white" />
        <span class="text-sm text-gray-600 dark:text-white font-medium">Filtros:</span>
      </div>

      <Select v-model="squadOrPartnerFilter" :options="currentOptions" />
      <Select v-model="monthFilter" :options="monthOptions" />
      <Select v-model="yearFilter" :options="yearOptions" />

      <Button variant="primary" @click="saveUser">Aplicar</Button>
      <Button variant="secondary" @click="close">Limpar</Button>
    </div>
  </div>
  <div v-if="activeTab === 'general' && !user?.partner?.id">
    <TabGeneral />
  </div>
  <div v-else>
    <TabPartner />
  </div>
</template>
