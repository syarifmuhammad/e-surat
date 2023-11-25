<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: "",
    },
    data: {
        type: Array,
        required: true,
    },
    required: {
        type: Boolean,
        default: false,
    }
})

const emit = defineEmits(["update:modelValue", "change"])

const focus = ref(false)

const parent = ref(null)

function onInput(data) {
    emit("update:modelValue", data);
    emit("change", data);
}

function onSelected(data) {
    onInput(data);
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
</script>

<template>
    <div tabindex="-1" class="" ref="parent">
        <input :required="props.required" type="text" :class="['form-control', { 'rounded-b-none': focus && data.length > 0 }]"
            :placeholder="props.placeholder" :value="modelValue" @input="onInput($event.target.value)" @focus="onFocus"
            @blur="onFocusOut($event)" />
        <Transition>
            <ul v-if="data.length > 0" class="w-full max-h-48 overflow-y-auto z-10 flex flex-col" v-show="focus">
                <li v-for="d in data"
                    class="items-center gap-x-2 py-3 px-4 text-sm font-medium hover:bg-slate-100 border border-t-0 border-gray-200 text-gray-800 -mt-px first:rounded-t-none first:mt-0 last:rounded-b-lg cursor-pointer"
                    :class="{'bg-primary-200/20': d === modelValue}"
                    @click="onSelected(d)">
                    {{ d }}
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