<script setup>
import { ref, watch, computed } from "vue";
import moment from "moment";
import { isArray, isObject, range } from "lodash";

const emit = defineEmits(['change', 'range-change']);

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    modelValue: {
        type: [Object, Array],
        validator: (value) => 
            moment.isMoment(value) || 
            (Array.isArray(value) && value.every(date => !date || moment.isMoment(date))),
    },
    withDate: {
        type: Boolean,
        default: false
    },
    withTime: {
        type: Boolean,
        default: false
    },
    range: {
        type: Boolean,
        default: false
    }
})

const today = moment();
const selectedStart = ref(today.clone());
const selectedEnd = ref(isArray(props.modelValue) && props.modelValue[1] && moment.isMoment(props.modelValue[1]) ? props.modelValue[1].clone() : null);
const currentMonth = ref(selectedStart.value.clone().startOf('month'));
const selecting = ref(false);

const monthValue = ref(currentMonth.value.month());
const yearValue = ref(currentMonth.value.year());

const months = computed(() => {
    return moment.months().map((month, index) => ({
        text: month,
        value: index
    }));
});

const years = computed(() => {
    const arr = [];
    for (let y = 1970; y <= 2100; y++) arr.push(y);
    return arr;
});

const time = ref(selectedStart.value.format('HH:mm'));


watch(() => props.show, (visible) => {
    if (visible && props.modelValue) {
        if (props.range && Array.isArray(props.modelValue) && moment.isMoment(props.modelValue[0])) {
            selectedStart.value = props.modelValue[0].clone();
            selecting.value = false;
            if (props.modelValue[1] && moment.isMoment(props.modelValue[1])) {
                selectedEnd.value = props.modelValue[1].clone();
            } else {
                selectedEnd.value = null;
            }
        } else if (!props.range && moment.isMoment(props.modelValue)) {
            selectedStart.value = props.modelValue.clone();
            selectedEnd.value = null;
        }
        if (props.withDate){
            currentMonth.value = selectedStart.value.clone().startOf('month');
            monthValue.value = currentMonth.value.month();
            yearValue.value = currentMonth.value.year();
        }
        if (props.withTime) {
            time.value = selectedStart.value.format('HH:mm');
        }
    }
});

const daysInMonth = computed(() => {
    return currentMonth.value.daysInMonth();
});
const firstDayOfWeek = computed(() => {
    return currentMonth.value.day();
});

watch([monthValue, yearValue], ([newMonth, newYear]) => {
    currentMonth.value = currentMonth.value.clone().year(newYear).month(newMonth).date(1);
});

watch(currentMonth, (newVal) => {
    monthValue.value = newVal.month();
    yearValue.value = newVal.year();
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
    if (props.range) {
        selectRange(day);
    } else {
        if (props.withDate && props.withTime) {
            const [hour, minute] = time.value.split(':');
            selectedStart.value = day.clone().hour(Number(hour)).minute(Number(minute));
            emit('change', selectedStart.value.clone());
        }
        else if (props.withDate) {
            selectedStart.value = day.clone();
            emit('change', selectedStart.value.clone());
        }
    }
}

function selectRange(day) {
    if (selecting.value && day.isSameOrAfter(selectedStart.value, 'day')) {
        selectedEnd.value = day.clone();
        emit('range-change', [selectedStart.value.clone(), selectedEnd.value.clone()]);
        selecting.value = false;
    } else {
        selectedStart.value = day.clone();
        selectedEnd.value = null;
        selecting.value = true;
    }
}

function selectTime() {
    if (props.withTime && !props.withDate) {
        const [hour, minute] = time.value.split(':');
        selectedStart.value = today.clone().hour(Number(hour)).minute(Number(minute));
        emit('change', selectedStart.value.clone());
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
                  <div class="font-bold flex items-center">
                    <select v-model="monthValue" class="p-0 bg-none border-none hover:bg-gray-200 cursor-pointer">
                        <option v-for="month in months" :key="month.value" :value="month.value">
                            {{ month.text }}
                        </option>
                    </select>
                    <select v-model="yearValue" class="p-0 bg-none border-none hover:bg-gray-200 cursor-pointer">
                        <option v-for="year in years" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                  </div>
                <button class="self-start" @click="nextMonth">Next</button>
            </div>
            <div>
                <div class="grid grid-cols-7 text-center text-sm font-semibold mb-2">
                    <span v-for="(day, index) in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="index">{{ day }}</span>
                </div>
                <div class="grid grid-cols-7">
                    <span v-for="n in firstDayOfWeek" :key="`empty-${n}`" class="text-transparent">.</span>
                    <span v-for="day in weeks.flat()" :key="day.format('YYYY-MM-DD')" 
                        :class="{
                            'bg-blue-500 text-white': selectedStart.isSame(day, 'day') || (selectedEnd && selectedEnd.isSame(day, 'day')),
                            'bg-blue-300 text-white': selectedStart.isBefore(day, 'day') && selectedEnd && selectedEnd.isAfter(day, 'day'),
                            'rounded-full bg-blue-600 text-white font-bold': today.isSame(day, 'day') && !selectedStart.isSame(day, 'day') && !(!range || (selectedEnd && selectedEnd.isSame(day, 'day'))),
                            'rounded-md': selectedStart.isSame(day, 'day') && (!range || !selectedEnd),
                            'rounded-l-md': selectedStart.isSame(day, 'day') && range,
                            'rounded-r-md': selectedEnd && selectedEnd.isSame(day, 'day'),
                            'cursor-pointer hover:bg-gray-200': true,
                            'text-center p-2': true
                        }"
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