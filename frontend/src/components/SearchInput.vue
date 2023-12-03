<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from "axios"

const props = defineProps({
    defaultValue: {
        type: String,
        default: ""
    },
    modelValue: Object | Array | String | Number,
    placeholder: {
        type: String,
        default: "",
    },
    url: {
        type: String,
        required: true,
    },
    filter: Object,
})

const emit = defineEmits(["update:modelValue", "selected", "inputValue", "click_default_first"])

const result_search = ref([])
const loading = ref(true)
const value = ref(props.defaultValue)
const focus = ref(false)

const parent = ref(null)
const list_search = ref(null)
const next = ref(props.url)
const success_search = ref(null)
const timeout_search = ref(null)
const busy = ref(false)

watch([value, focus], ([newValue, newFocus]) => {
    if (newValue == "" || newValue != props.defaultValue) {
        emit('inputValue', newValue)
    }
    if (newFocus && newValue != success_search.value && (newValue == "" || newValue != props.defaultValue)) {
        onSearch(newValue);
        list_search.value.addEventListener("scroll", handleScroll);
    }
}, { immediate: true })

watch(() => props.defaultValue, (first, second) => {
    if (first != second) {
        value.value = first
    }
});

function handleScroll() {
    const { scrollHeight, scrollTop, clientHeight } = list_search;
    if (scrollTop + clientHeight >= scrollHeight - 3) {
        getData();
    }
}

function search() {
    onSearch(value.value);
    list_search.value.addEventListener("scroll", handleScroll);
}

function onSearch(value_args) {
    if (timeout_search.value != null) {
        clearTimeout(timeout_search.value);
    }
    timeout_search.value = setTimeout(() => {
        loading.value = true;
        result_search.value = [];
        busy.value = false;
        next.value = `${props.url}?search=${value_args}`;
        getData();
    }, 500);
}

function fetchData() {
    busy.value = true;
    let filter = "";
    if (props.filter) {
        for (let [key, value] of Object.entries(props.filter)) {
            filter += `&${key}=${value}`;
        }
    }
    let url = props.url
    if (next.value != null) {
        url = next.value + filter    
    }
    axios.get(url).then((response) => {
        next.value = response.data.links
            ? (response.data.links.next ?? "") + "&search=" + value.value + filter
            : null;
        result_search.value = []
        response.data.data.forEach((p) => {
            result_search.value.push(p);
        });
        busy.value = false;
        loading.value = false;
        success_search.value = value.value;
    });
}

function getData() {
    if (next.value && !busy.value) {
        fetchData()
    }
}
function onSelected(data) {
    let result = props.modelValue;
    if (Array.isArray(props.modelValue)) {
        if (!props.modelValue.some(p => p.id == data.id)) {
            result.push(data);
        }
    } else {
        result = data;
    }
    emit("update:modelValue", result);
    emit("selected", result);
    focus.value = false;
}
function onFocus() {
    focus.value = true;
}
function onFocusOut(event) {
    if (!parent.value.contains(event.relatedTarget)) {
        focus.value = false;
    }
}

defineExpose({ fetchData })
</script>

<template>
    <div tabindex="-1" class="" ref="parent">
        <input type="text" :class="['form-control', { 'rounded-b-none': focus }]" :placeholder="props.placeholder"
            v-model="value" @focus="onFocus" @blur="onFocusOut($event)" />
        <Transition>
            <ul ref="list_search" class="w-full max-h-48 overflow-y-auto z-10 flex flex-col divide-y" v-show="focus">
                <template v-if="!loading">
                    <li v-if="$slots.default_first" @click="emit('click_default_first')"
                        class="items-center gap-x-2 py-3 px-4 text-sm font-medium bg-white border hover:bg-green-100 border-t-0 border-gray-200 text-gray-800 -mt-px first:rounded-t-none first:mt-0 last:rounded-b-lg cursor-pointer">
                        <slot name="default_first"></slot>
                    </li>
                    <li class="items-center gap-x-2 py-3 px-4 text-sm font-medium bg-white hover:bg-slate-100 border border-t-0 border-gray-200 text-gray-800 -mt-px first:rounded-t-none first:mt-0 last:rounded-b-lg cursor-pointer"
                        v-for="(data, n) in result_search" @click="onSelected(data)"
                        v-if="$slots.default && result_search.length != 0">
                        <slot :data="data" :key="n"></slot>
                    </li>
                    <li class="items-center text-center gap-x-2 py-3 px-4 text-sm font-medium bg-white hover:bg-slate-100 border border-t-0 border-gray-200 text-gray-800 -mt-px first:rounded-t-none first:mt-0 last:rounded-b-lg cursor-pointer"
                        v-else>
                        Data tidak ada
                    </li>
                </template>
                <li class="items-center gap-x-2 py-3 px-4 text-sm font-medium bg-white border border-t-0 border-gray-200 text-gray-800 -mt-px first:rounded-t-none first:mt-0 last:rounded-b-lg cursor-pointer"
                    v-else>
                    Loading ...
                </li>
            </ul>
        </Transition>
    </div>
</template>

<style scoped>
.v-enter-active,
.v-leave-active {
    transition: max-height 0.5s ease;
    max-height: 12rem;
    overflow-y: hidden;
}

.v-enter-from,
.v-leave-to {
    overflow-y: hidden;
    max-height: 0;
    transition: max-height 0.5s ease;
}
</style>