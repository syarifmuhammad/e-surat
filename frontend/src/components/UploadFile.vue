<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import { Icon } from '@iconify/vue'
import 'docx-preview/dist/docx-preview.js'

const props = defineProps({
    accepted_file_type: {
        type: Array,
        default: () => ['application/pdf'],
    },
    default_files: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['updated_files'])

const fileform = ref(null)
const inputfile = ref(null)
const dragAndDropCapable = ref(false)
const dragenter = ref(false)
const files = ref(props.default_files)
const preview = ref(null)
const uploadPercentage = ref(0)
const docx_preview = ref(null)

function determineDragAndDropCapable() {
    var div = document.createElement("div");

    return (
        ("draggable" in div || ("ondragstart" in div && "ondrop" in div)) &&
        "FormData" in window &&
        "FileReader" in window
    );
}

function handleFileUploaded(file) {
    dragenter.value = false;
    if (props.accepted_file_type.length < 1 || props.accepted_file_type.some((t) => t == file.type)) {
        files.value = file;
        preview.value = URL.createObjectURL(files.value);
        if (file.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            nextTick(() => {
                docx.renderAsync(files.value, docx_preview.value) 
            })
        }
    } else {
        Swal.fire({
            icon: "error",
            title: "Upload File",
            text: "Format file tidak sesuai!",
            showConfirmButton: false,
            timer: 1500,
        });
    }
}

function uploadFile(event) {
    if (event.target.files.length > 0) {
        handleFileUploaded(event.target.files[0]);
        emit('updated_files')
    }
}

function reset() {
    files.value = null;
    preview.value = null;
    inputfile.value.value = null;
}

onMounted(() => {
    dragAndDropCapable.value = determineDragAndDropCapable();

    if (dragAndDropCapable.value) {
        [
            "drag",
            "dragstart",
            "dragend",
            "dragover",
            "dragenter",
            "dragleave",
            "drop",
        ].forEach(
            function (evt) {
                fileform.value.addEventListener(
                    evt,
                    function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    }.bind(this),
                    false
                );
            }.bind(this)
        );

        fileform.value.addEventListener(
            "drop",
            function (e) {
                handleFileUploaded(e.dataTransfer.files[0]);
            }.bind(this)
        );
    }
})

watch(() => props.default_files, (first, second) => {
    if (first != second) {
        handleFileUploaded(props.default_files)
        // files.value = props.default_files
        // preview.value = URL.createObjectURL(props.default_files)
    }
}, { immediate: true });

onMounted(() => {
    // console.log(docx)
})


defineExpose({
    files, reset,
})
</script>
<template>
    <div class="w-full h-full flex flex-col items-center justify-center min-h-[300px] bg-slate-100 text-gray-500" ref="fileform"
        @dragenter="dragenter = true" @dragleave="dragenter = false">
        <input type="file" @change="uploadFile" ref="inputfile" class="hidden"
            :accept="props.accepted_file_type.join(',')" />
        <div class="flex flex-col justify-center items-center w-full" v-if="!dragenter && files == null">
            <Icon class="text-8xl" icon="octicon:upload-16"></Icon>
            <button type="button" class="btn btn-primary flex items-center rounded-3xl" @click="inputfile.click()">
                <Icon icon="octicon:upload-16"></Icon>&nbsp; Pilih File
            </button>
            <p class="mt-2">Atau drag file kesini</p>
        </div>
        <div class="flex flex-col justify-center items-center w-full" v-else-if="dragenter">
            Letakkan file disini
        </div>
        <div class="flex flex-col items-center w-full" v-else-if="files != null">
            <button type="button"
                class="btn cursor-pointer rounded-3xl btn-warning flex items-center mt-3 mb-2 w-25 justify-center"
                @click="reset()">
                Ubah File
            </button>
            <iframe v-if="files.type == 'application/pdf'" :src="preview" class="w-full min-h-[400px]"> </iframe>
            <div ref="docx_preview" id="docx_preview" v-else-if="files.type == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'"></div>
            <img v-else class="min-h-[400px]" :src="preview" />
        </div>
    </div>
</template>

<style scoped>
#docx_preview {
    width: 100%;
    max-height: 500px;
    overflow-y: auto;
}
</style>