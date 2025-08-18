<script setup>
import moment from 'moment';
import { ref, watch } from 'vue';

const emit = defineEmits(["change"]);

const props = defineProps({
  show: Boolean,
  value: {
    type: Object,
    validator: moment.isMoment,
  },
  withDate: Boolean,
  withTime: Boolean,
});

const selected = ref(props.value ? moment(props.value) : moment());

watch(() => props.show, (show) => {
    if (show) {
        selected.value = props.value ? moment(props.value) : moment();
    }
});

const data = {
	date: selected.value.format('YYYY-MM-DD'),
	time: selected.value.format('HH:mm'),
	dateTime: selected.value.format('YYYY-MM-DDTHH:mm'),
}

const onDateChange = (e) => {
  const date = e.target.value;
  selected.value = moment(date);
  emit("change", selected.value.clone());
}
const onDateTimeChange = (e) => {
  const datetime = e.target.value;
  selected.value = moment(datetime);
  emit("change", selected.value.clone());
}
const onTimeChange = (e) => {
  const time = e.target.value;
  selected.value = moment(time);
  emit("change", selected.value.clone());
}

</script>
<template>
    <div v-show="show" class="bg-white border rounded shadow p-4 flex flex-row justify-between">
      <template v-if="withDate && withTime">
        <input type="datetime-local" @change="onDateTimeChange" v-model="data.datetime"/>
      </template>
      <template v-else-if="withTime">
        <input type="time" @change="onTimeChange" v-model="data.time"/>
      </template>
      <template v-else-if="withDate">
        <input type="date" @change="onDateChange" v-model="data.date"/>
      </template>
    </div>
</template>

<style lang="postcss" scoped></style>
