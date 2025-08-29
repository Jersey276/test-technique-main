<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import moment from "moment";
import Calendar from "./Partials/CalendarPopup";
import vueFeather from "vue-feather";

const format = "DD/MM/YYYY";

const emit = defineEmits(["update:modelValue"]);

const props = defineProps({
  modelValue: {
    type: Array,
    validator: (value) =>
      value?.every((date) => !date || moment.isMoment(date)),
    default: [null, null],
  },
  is_clearable: {
    type: Boolean,
    default: false,
  },
});

const rangePicker = ref(null);

const showRangeDatePopup = ref(false);

const clickOutside = (event) => {
  if (
    showRangeDatePopup.value &&
    rangePicker.value &&
    !rangePicker.value.contains(event.target)
  ) {
    showRangeDatePopup.value = false;
  }
};

function onClearRange() {
  emit("update:modelValue", [null, null]);
}

onMounted(() => {
  document.addEventListener("mousedown", clickOutside);
});
onBeforeUnmount(() => {
  document.removeEventListener("mousedown", clickOutside);
});

function onRangeChange(range) {
  emit("update:modelValue", range);
  showRangeDatePopup.value = false;
};

</script>

<template>
  <div class="grid grid-cols-2 gap-2">
    <label class="block font-medium text-sm text-gray-700 col-span-2">
      <span>To</span>
    </label>
    <div class="relative col-span-2">
      <div class="border rounded-md flex justify-between">
        <button
          ref="rangePicker"
          class="date-button"
          @click="showRangeDatePopup = true"
        >
          {{ modelValue?.[0] ? modelValue[0].format(format) : '--/--/----' }}{{ modelValue?.[1] && modelValue[1] > modelValue[0] ? ' - ' + modelValue[1].format(format) : '' }}
        </button>
        <button v-if="is_clearable" class="clear-button border-left mx-4" @click="onClearRange">
          <vueFeather type="trash-2" class="w-4 h-4 text-gray-500" />
        </button>
      </div>
        <div class="absolute z-10 left-0" ref="rangePicker">
          <Calendar
            :show="showRangeDatePopup"
            :model-value="modelValue"
            :with-time="false"
            :with-date="true"
            :range="true"
            @range-change="onRangeChange"
          />
        </div>
      </div>
  </div>
</template>

<style lang="postcss" scoped>
.date-button {
  @apply relative px-3 h-12 w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm;
}
</style>
