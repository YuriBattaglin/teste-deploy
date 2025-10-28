<script setup lang="ts">
import { ref, computed } from "vue";
import dayjs from "dayjs";
import "dayjs/locale/pt-br";
dayjs.locale("pt-br");

const props = defineProps<{
  modelValue?: string | Date;
  placeholder?: string;
  disabled?: boolean;
}>();
const emit = defineEmits(["update:modelValue", "change"]);

const value = ref(props.modelValue ?? "");

function onChange(val: string | Date) {
  emit("update:modelValue", val);
  emit("change", val);
}

const displayValue = computed(() => {
  if (!value.value) return "";
  const date = dayjs(value.value);
  const start = date.startOf("isoWeek"); 
  const end = start.add(6, "day"); 
  return `${start.format("DD/MM/YYYY")} - ${end.format("DD/MM/YYYY")}`;
});

function clear() {
  value.value = "";
  emit("update:modelValue", "");
}

defineExpose({
  clear
});
</script>

<template>
  <el-date-picker
    v-model="value"
    type="week"
    :placeholder="props.placeholder || 'Selecione uma semana'"
    :disabled="props.disabled"
    clearable
    @change="onChange"
    class="custom-autocomplete"
    :format="displayValue"
  />
</template>

<style>
.el-date-editor.el-input,
.el-date-editor.el-input__wrapper {
  width: 100%;
  height: auto;
}
</style>
