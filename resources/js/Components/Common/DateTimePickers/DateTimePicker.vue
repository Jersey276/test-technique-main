<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from "vue";
import moment from "moment";
import Calendar from "./Partials/CalendarPopup";
import vueFeather from "vue-feather";

defineEmits(["update:modelValue"]);

const showPopup = ref(false);
const picker = ref(null);

const props = defineProps({
  modelValue: {
    type: Object,
    validator: moment.isMoment,
  },
  label: String,
  type: {
    type: String,
    validator: (val) => ["date", "datetime", "time"].includes(val),
    default: "datetime",
  },
  error: String,
  is_clearable: {
    type: Boolean,
    default: false,
  },
});

const clickOutside = (event) => {
  if (picker.value && !picker.value.contains(event.target)) {
    showPopup.value = false;
  }
};

const inputError = ref(props.error || "");

watch(() => props.error, (newError) => {
  inputError.value = newError || "";
});

const hasDate = computed(() => props.type !== 'time');
const hasTime = computed(() => props.type !== 'date');

onMounted(() => {
  document.addEventListener("mousedown", clickOutside);
});
onBeforeUnmount(() => {
  document.removeEventListener("mousedown", clickOutside);
});

// Format to be used to show the selected value
const format = computed(() => {
  switch (props.type) {
    case "time":
      return "HH:mm";
    case "date":
      return "DD/MM/YYYY";
    default:
      return "HH:mm DD/MM/YYYY"; // datetime
  }
});
</script>

<template>
  <div>
    <label class="block font-medium text-sm text-gray-700">
      <span v-if="label">{{ label }}</span>
    </label>
    <div class="relative" ref="picker">
      <div class="border rounded-md flex justify-between">
        <button
          class="px-3 h-12 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-l-md shadow-sm text-left"
          @click="showPopup = true"
        >
          {{modelValue?.format(format)}}
        </button>
        <button v-if="is_clearable" class="clear-button border-left mx-4" @click="onClearStartDate">
          <vueFeather
            type="trash-2"
            class="w-4 h-4 text-gray-500"
          />
        </button>
      </div>
      <div class="absolute z-10 left-0">
        <Calendar
          :show="showPopup"
          :model-value="modelValue"
          :with-date="hasDate"
          :with-time="hasTime"
          @change="$emit('update:modelValue', $event)"
        />
      </div>
      <span v-if="inputError" class="text-red-500 text-sm mt-1">{{ inputError }}</span>
    </div>
  </div>
</template>

<style lang="postcss" scoped>
</style>
