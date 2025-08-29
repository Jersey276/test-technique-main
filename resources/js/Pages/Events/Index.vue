<script setup>
import { onMounted, ref, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import moment from "moment";
import AppLayout from "@/Layouts/AppLayout";
import AddEditDialog from "./Partials/AddEditDialog";
import Button from "@/Components/Common/Button";
import Dialog from "@/Components/Common/DialogModal";
import Table from "@/Components/Common/Table";
import vueFeather from "vue-feather";
import DateRangePicker from "../../Components/Common/DateTimePickers/DateRangePicker.vue";
import Pagination from "../../Components/Common/Pagination.vue";
const format = "YYYY-MM-DD";

const props = defineProps({
    events: {
        type: Object,
        default: [],  
    },
    starts_at: String,
    ends_at: String,
    errors: Object,
});

const filters = ref({
    'starts_at' : null,
    'ends_at'   : null,
    'page'      : 1,
    'per_page'  : 10
})
watch(filters, (newFilters) => {
    Inertia.get(route('events.index'), newFilters, {
        preserveState:true,
        replace:true,
        preserveScroll:true,
    })
}, {deep: true});

const dateFilters = ref([null, null]);
watch(dateFilters, (newRange) => {
    filters.value = {
        ...filters.value,
        starts_at : newRange[0]? newRange[0].format("YYYY-MM-DD"):null,
        ends_at : newRange[1] && newRange[1] > newRange[0]? newRange[1].format("YYYY-MM-DD"):null,
        page: 1
    }
}, {deep : true});

const pageFilter = ref(1);
watch(pageFilter, (newPage) => {
    filters.value = {
        ...filters.value,
        page : newPage
    }
})

// Load url params into state if any existed on mount
onMounted(() => {
    if (props.starts_at || props.ends_at) {
        dateFilters.value = [
            props.starts_at ? moment(props.starts_at, format) : null,
            props.ends_at ? moment(props.ends_at, format) : null
        ];
    }
});

function onPageChanged(e) {
    filters.value = {
        ...filters.value,
        page: e,
    }}

function onPerPageChanged(e) {
    filters.value = {
        ...filters.value,
        per_page: e.per_page,
        page: 1
    }
}

const itemToEdit = ref(null);
const itemToDelete = ref(null);
const onDelete = () => {
    Inertia.delete(route("events.destroy", itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            itemToDelete.value = null;
        },
    });
};
</script>

<template>
    <AppLayout title="Events">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Events
            </h2>
        </template>

        <div class="card">
            <div class="card-header mb-6 flex flex-row justify-between items-end">
                <div class="w-3/12">
                    <AddEditDialog
                        :item-to-edit="itemToEdit"
                        @close="itemToEdit = null"
                    />
                </div>
                <div class="w-6/12">
                    <DateRangePicker v-model="dateFilters" is_clearable />
                    <div v-if="props.errors && Object.keys(props.errors).length" class="mb-4">
                        <div v-for="(err, key) in props.errors" :key="key" class="text-red-500 text-sm">
                            {{ err }}
                        </div>
                    </div>
                </div>
                <div class="w-3/12"></div>
                <Dialog
                    :show="itemToDelete != null"
                    @close="itemToDelete = null"
                >
                    <template #header>Deleting event</template>
                    Are you sure you want to delete this event ?
                    <template #footer>
                        <Button
                            variant="secondary"
                            class="mr-3"
                            @click="itemToDelete = null"
                            >Cancel</Button
                        >
                        <Button variant="danger" @click="onDelete"
                            >Submit</Button
                        >
                    </template>
                </Dialog>
            </div>
            <div class="card-body">
                <Table
                    :data="events.data"
                    :headings="['Title', 'Date', 'Actions']"
                >
                    <template #row="{ item }">
                        <td>{{ item.title }}</td>
                        <td>
                            {{ moment(item.starts_at).format("HH:mm DD/MM/YYYY") }}
                        </td>
                        <!--<td>
                            {{ moment(item.ends_at).format("HH:mm DD/MM/YYYY") }}
                        </td>-->
                        <td>
                            <span
                                class="px-2 text-gray-700 hover:text-blue-500 cursor-pointer transition"
                            >
                                <vueFeather
                                    type="edit"
                                    size="1.3rem"
                                    @click="itemToEdit = item"
                                />
                            </span>
                            <span
                                class="px-2 text-gray-700 hover:text-red-500 cursor-pointer transition"
                            >
                                <vueFeather
                                    type="trash"
                                    size="1.3rem"
                                    @click="itemToDelete = item"
                                />
                            </span>
                        </td>
                    </template>
                </Table>
                <Pagination
                    :total="props.events.total"
                    :perPage="props.events.per_page"
                    :currentPage="props.events.current_page"
                    :pages="props.events.links"
                    @page-changed="onPageChanged"
                    @per-page-changed="onPerPageChanged"
                />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped></style>
