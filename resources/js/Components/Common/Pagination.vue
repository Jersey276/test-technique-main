<script setup>

import { computed, ref, watch } from "vue";
import Button from "./Button.vue";

const props = defineProps({
    total: {
        type: Number,
        required: true,
    },
    perPage: {
        type: Number,
        default: 10,
    },
    currentPage: {
        type: Number,
        default: 1,
    },
    pages: {
        type: Array,
        default: () => [],
    },
});

const perPage = ref(props.perPage);

watch(() => props.perPage, (val) => {
    perPage.value = val;
});

const emit = defineEmits(["page-changed", "per-page-changed"]);

const totalPages = computed(() => {
    return Math.ceil(props.total / perPage.value);
});
function changePage(page) {
    page = parseInt(page);
    if (page >= 1 && page <= totalPages.value) {
        emit("page-changed", page);
    }
}
function changePerPage(event) {
    emit("per-page-changed", { per_page: event.target.value });
}

</script>
<template>
    <div class="flex flex-row justify-between">
        <div>
            <span>Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results</span>
        </div>
        <div  v-if="totalPages > 1">
            <Button @click="changePage(currentPage - 1)" class="btn" :disabled="currentPage === 1">
                <
            </Button>
            <template v-for="page in pages.filter(p => !p.label.includes('Previous') && !p.label.includes('Next'))" :key="page.label">
                <Button @click="changePage(page.label)" class="btn" :disabled="currentPage == parseInt(page.label)">{{ page.label }}</Button>
            </template>
            <Button @click="changePage(currentPage + 1)" class="btn" :disabled="currentPage === totalPages.value">
                >
            </Button>
        </div>
        <div>
        <label for="per_page">Items per page:</label>
        <select id="per_page" class="form-select" @change="changePerPage" v-model.number="perPage">
            <option :value='10' selected>10</option>
            <option :value='20'>20</option>
            <option :value='50'>50</option>
            <option :value='100'>100</option>
            <option :value='250'>250</option>
            <option :value='500'>500</option>
        </select>
        </div>
    </div>
</template>