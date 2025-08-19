<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import moment from "moment";
import Calendar from "./Partials/CalendarPopup";

const format = "DD/MM/YYYY";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: {
    type: Array,
    validator: (value) =>
      value?.every((date) => !date || moment.isMoment(date)),
    default: [null, null],
  },
});

const startpicker = ref(null);
const endpicker = ref(null);

// Represents whether or not the datepicker popup is open for each field
const showStartDatePopup = ref(false);
const showEndDatePopup = ref(false);

const clickOutside = (event) => {
  if (
    showStartDatePopup.value &&
    startpicker.value &&
    !startpicker.value.contains(event.target)
  ) {
    showStartDatePopup.value = false;
  }
  if (
    showEndDatePopup.value &&
    endpicker.value &&
    !endpicker.value.contains(event.target)
  ) {
    showEndDatePopup.value = false;
  }
};

onMounted(() => {
  document.addEventListener("mousedown", clickOutside);
});
onBeforeUnmount(() => {
  document.removeEventListener("mousedown", clickOutside);
});

const onStartDateChange = (date) => {
  emit("update:modelValue", [date, props.modelValue?.[1]]);
};

const onEndDateChange = (date) => {
  emit("update:modelValue", [props.modelValue?.[0], date]);
};
</script>

<template>
  <div class="grid grid-cols-2">
    <label class="block font-medium text-sm text-gray-700">
      <span>From</span>
    </label>
    <label class="block font-medium text-sm text-gray-700">
      <span>To</span>
    </label>
    <div class="relative">
      <button class="date-button rounded-l-md" @click="showStartDatePopup = true">
        {{ modelValue?.[0] ? modelValue[0].format(format) : '--/--/--' }}
      </button>
      <div class="absolute z-10 left-0 w-full" ref="startpicker">
        <Calendar
          :show="showStartDatePopup"
          :value="modelValue?.[0]"
          :with-time="false"
          :with-date="true"
          @change="onStartDateChange"
        />
      </div>
    </div>
    <div class="relative">
      <button
        ref="endDatePicker"
        class="date-button rounded-r-md"
        @click="showEndDatePopup = true"
      >
        {{ modelValue?.[1] ? modelValue[1].format(format) : '--/--/--' }}
      </button>
        <div class="absolute z-10 left-0 w-full" ref="endpicker">
          <Calendar
            :show="showEndDatePopup"
            :value="modelValue?.[1]"
            :with-time="false"
            :with-date="true"
            @change="onEndDateChange"
          />
        </div>
      </div>
  </div>
</template>

<style lang="postcss" scoped>
.date-button {
  @apply relative px-3 h-12 w-full border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm;
}
</style>
