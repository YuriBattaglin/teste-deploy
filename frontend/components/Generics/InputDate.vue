<script setup lang="ts">
import { ref, computed, watch } from "vue";
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

// Atualiza o valor local se o modelValue mudar externamente
watch(
  () => props.modelValue,
  (newVal) => {
    value.value = newVal ?? "";
  }
);

function onChange(val: string | Date) {
  emit("update:modelValue", val);
  emit("change", val);
}

const displayValue = computed(() => {
  if (!value.value) return "";
  return dayjs(value.value).format("DD/MM/YYYY");
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
    type="date"
    :placeholder="props.placeholder || 'Selecione uma data'"
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
