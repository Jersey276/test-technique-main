<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from "vue";
import moment from "moment";
import Calendar from "./Partials/CalendarPopup";

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
});

const clickOutside = (event) => {
  if (picker.value && !picker.value.contains(event.target)) {
    showPopup.value = false;
  }
};

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
      <button
        class="px-3 h-12 w-full border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm text-left"
        @click="showPopup = true"
      >
        {{modelValue?.format(format)}}
      </button>
      <div class="absolute z-10 left-0 w-full">
        <Calendar
        :show="showPopup"
        :value="modelValue"
        :with-date="hasDate"
        :with-time="hasTime"
        @change="$emit('update:modelValue', $event)"
      />
      </div>
    </div>
  </div>
</template>

<style lang="postcss" scoped>
</style>
