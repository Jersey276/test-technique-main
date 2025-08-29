<script setup>
import { ref, watch } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import Dialog from "@/Components/Common/DialogModal";
import Button from "@/Components/Common/Button";
import Input from "@/Components/Common/Input";
import DateTimePicker from "@/Components/Common/DateTimePickers/DateTimePicker.vue";
import moment from "moment";
import vueFeather from "vue-feather";

const emit = defineEmits(["close"]);

const props = defineProps({
    itemToEdit: {
        type: Object,
        default: null,
    },
});

const show = ref(false);
const editing = ref(false);

const form = useForm({
    title: "",
    starts_at: null,
    ends_at: null,
});

watch (() => props.itemToEdit, (itemSelected) => {
    if (itemSelected !== null) {
        form.reset();
        form.title = itemSelected.title;
        form.starts_at = moment(itemSelected.starts_at);
        form.ends_at = moment(itemSelected.ends_at);
        show.value = true;
        editing.value = true;
    }
})

// Called when the user clicks on the "Add new" button
const onAddNew = () => {
    form.reset();
    show.value = true;
    editing.value = false;
};

// Called when the user submits the form
const onSubmit = () => {
    const transform = (data) => ({
        ...data,
        starts_at: data.starts_at.format("YYYY-MM-DD HH:mm"),
        ends_at: data.ends_at.format("YY-MM-DD HH:mm"),
    });

    const requestParams = {
        preserveScroll: true,
        onSuccess: onClose,
    };

    // Stores or updates the item
    if (editing.value) {
        form.transform(transform).put(
            route("events.update", props.itemToEdit.id),
            requestParams,
        )
    } else {
        form.transform(transform).post(route("events.store"), requestParams);
    }
};

// Called when the dialog closes
const onClose = () => {
    form.reset();
    show.value = false;
    emit("close");
};
</script>

<template>
    <div>
        <Button
            @click="onAddNew"
        >
            <vueFeather
                type="plus"
            />
            <span class="ml-2">Add new</span>
        </Button>
        <Dialog
            :show="show"
            @close="onClose"
        >
            <template #header>{{
                editing ? "Edit event" : "Add new event"
            }}</template>
            <div class="grid grid-cols-2 gap-2">
            <Input
                name="title"
                label="Title"
                v-model="form.title"
                class="mb-6 col-span-2"
                :error="form.errors.title"
            />
                <DateTimePicker
                    name="starts_at"
                    label="Starts at"
                    v-model="form.starts_at"
                    class="mb-6"
                    :error="form.errors.starts_at"
                />

                <DateTimePicker
                    name="ends_at"
                    label="Ends at"
                    v-model="form.ends_at"
                    class="mb-6"
                    :error="form.errors.ends_at"
                />
            </div>
            <template #footer>
                <Button
                    variant="secondary"
                    class="mr-3"
                    @click="onClose"
                >
                    Cancel
                </Button>
                <Button
                    @click="onSubmit"
                    :disabled="form.title === '' || form.starts_at === null"
                >
                    Submit
                </Button>
            </template>
        </Dialog>
    </div>
</template>

<style scoped></style>
