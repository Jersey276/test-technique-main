<script setup>
import { ref, watch, computed } from "vue";
import moment from "moment";

const emit = defineEmits(['change']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    modelValue: {
        type: Object,
        validator: moment.isMoment,
    },
    withDate: {
        type: Boolean,
        default: false
    },
    withTime: {
        type: Boolean,
        default: false
    }
    
})

const today = moment();
const selected = ref(props.modelValue && moment.isMoment(props.modelValue) ? props.modelValue.clone() : today.clone());
const currentMonth = ref(selected.value.clone().startOf('month'));

const time = ref(selected.value.format('HH:mm'));

console.log(currentMonth);

watch(() => props.value, (newVal) => {
    if (newVal && moment.isMoment(newVal)) {
        selected.value = newVal.clone();
        currentMonth.value = selected.value.clone().startOf('month');
        time.value = selected.value.time;
    }
});

const daysInMonth = computed(() => {
    return currentMonth.value.daysInMonth();
});
const firstDayOfWeek = computed(() => {
    return currentMonth.value.day();
});

const weeks = computed(() => {
    const weeks = [];
    const days = Array.from({ length: daysInMonth.value }, (_, i) => {
        return currentMonth.value.clone().date(i + 1);
    });
    while (days.length) {
        weeks.push(days.splice(0, 7));
    }
    return weeks;
});

function selectDate(day) {
    if (!day) return;
    if (props.withDate && props.withTime) {
        const [hour, minute] = time.value.split(':');
        selected.value = day.clone().hour(Number(hour)).minute(Number(minute));
        emit('change', selected.value.clone());
    }
    else if (props.withDate) {
        selected.value = day.clone();
        emit('change', selected.value.clone());
    }
}

function selectTime() {
    if (props.withTime && !props.withDate) {
        const [hour, minute] = time.value.split(':');
        selected.value = today.clone().hour(Number(hour)).minute(Number(minute));
        emit('change', selected.value.clone());
    }
}

function prevMonth() {
    if (currentMonth.value) {
        currentMonth.value = currentMonth.value.clone().subtract(1, 'month');
    }
}
function nextMonth() {
    if (currentMonth.value) {
        currentMonth.value = currentMonth.value.clone().add(1, 'month');
    }
}
</script>
<template>
    <div v-show="show" class="inline-calendar bg-white border rounded p-4 w-80">
        <div v-if="withDate">
            <div class="flex justify-between items-center mb-2 h-20">
                <button class="self-start" @click="prevMonth">Previous</button>
                <div class="font-bold">{{ currentMonth.format('MMMM YYYY') }}</div>
                <button class="self-start" @click="nextMonth">Next</button>
            </div>
            <div>
                <div class="grid grid-cols-7 text-center text-sm font-semibold mb-2">
                    <span v-for="(day, index) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="index">{{ day }}</span>
                </div>
                <div class="grid grid-cols-7 gap-1">
                    <span v-for="n in firstDayOfWeek" :key="`empty-${n}`" class="text-transparent">.</span>
                    <span v-for="day in weeks.flat()" :key="day.format('YYYY-MM-DD')" 
                        :class="{'bg-blue-500 text-white': selected.isSame(day, 'day'), 'cursor-pointer hover:bg-gray-200': true, ' text-center p-2 rounded': true}"
                        @click="selectDate(day)">
                        {{ day.date() }}
                    </span>
                </div>
            </div>
        </div>
        <div v-if="withTime">
            <div class="flex w-full justify-around items-center">
                <input type="time" v-model="time" class="border rounded p-1" />
                <button v-if="!props.withDate && props.withTime" @click="selectTime" class="ml-2 bg-blue-500 text-white rounded px-2 py-1">Set Time</button>
            </div>
        </div>
    </div>
</template>