<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import axios from 'axios'
import SubHeader from '@/components/SubHeader.vue'

const url = 'http://127.0.0.1:8000/api'

const loading = ref(false)
const letter_types = ref([])
const letter_type_index = ref('')

const reference_number = reactive({
    letter_type: "",
    prefix: "",
    suffix: "",
})

const preview = computed(() => {
    let pr = `${reference_number.prefix}XXX${reference_number.suffix}`;

    pr = pr.replaceAll("{tgl}", String(new Date().getDate()).padStart(2, "0"));
    pr = pr.replaceAll("{bln}", romanize(new Date().getMonth() + 1));
    pr = pr.replaceAll("{thn}", new Date().getFullYear());

    return pr;
})

function romanize(num) {
    if (!+num)
        return false;
    var digits = String(+num).split(""),
        key = ["", "C", "CC", "CCC", "CD", "D", "DC", "DCC", "DCCC", "CM",
            "", "X", "XX", "XXX", "XL", "L", "LX", "LXX", "LXXX", "XC",
            "", "I", "II", "III", "IV", "V", "VI", "VII", "VIII", "IX"],
        roman = "",
        i = 3;
    while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
    return Array(+digits.join("") + 1).join("M") + roman;
}

function getLetterTypes() {
    loading.value = true;
    axios.get(`${url}/reference-number-settings/letter-types`).then((response) => {
        letter_types.value = response.data.data;
        // reference_number.letter_type = response.data.data[0].letter_type;
        // reference_number.prefix = response.data.data[0].prefix;
        // reference_number.suffix = response.data.data[0].suffix;
        loading.value = false;
    });
}

function getReferenceNumber(index) {
    if (index == "" || index == null) {
        reference_number.letter_type = ""
        reference_number.prefix = ""
        reference_number.suffix = ""
        return
    }

    reference_number.letter_type = letter_types.value[index].letter_type;
    reference_number.prefix = letter_types.value[index].prefix;
    reference_number.suffix = letter_types.value[index].suffix;
}
function save() {
    axios.post(`${url}/reference-number-settings`, {
        _method: "PUT",
        letter_type: reference_number.letter_type,
        prefix: reference_number.prefix,
        suffix: reference_number.suffix,
    })
        .then((response) => {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: response.data.message,
                showConfirmButton: false,
                timer: 1500,
            });
            getLetterTypes()
        })
        .catch((error) => {
            console.log(error.response.data);
        });
}
function reset() {
    reference_number.letter_type = ""
    reference_number.prefix = ""
    reference_number.suffix = ""
    letter_type_index.value = ""
}

onMounted(() => {
    getLetterTypes();
});

</script>

<template>
    <SubHeader :title="`Format Nomor Surat`">
        <!-- <button type="button" class="btn btn-outline-primary inline-flex gap-2 justify-center items-center">
            Surat Keterangan Kerja
            <Icon class="text-lg" icon="fe:sync" />
        </button> -->
    </SubHeader>
    <div class="flex flex-col bg-white rounded-lg mb-4">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <h5 class="text-lg mb-2">Cara penulisan Format No Surat</h5>
            <ul class="list-disc list-inside text-gray-800 mb-2">
                <li>
                    Gunakan <code class="text-red-500">{tgl}</code> untuk mendapatkan <b>tanggal</b> (
                    contoh : <b class="text-teal-500">{{ String(new Date().getDate()).padStart(2, "0") }}</b> )
                </li>
                <!-- <li>
                    Gunakan <code class="text-red-500">{bln}</code> untuk mendapatkan <b>bulan</b> ( contoh
                    : <b class="text-teal-500">{{ String(new Date().getMonth()+1).padStart(2, "0") }}</b> )
                </li> -->
                <li>
                    Gunakan <code class="text-red-500">{bln}</code> untuk mendapatkan <b>bulan dalam romawi</b> (
                    contoh
                    : <b class="text-teal-500">{{ romanize(new Date().getMonth() + 1) }}</b> )
                </li>
                <li>
                    Gunakan <code class="text-red-500">{thn}</code> untuk mendapatkan <b>tahun</b> ( contoh
                    : <b class="text-teal-500">{{ String(new Date().getFullYear()) }}</b> )
                </li>
            </ul>
            <p class="text-danger">
                <span class="text-red-500">*</span>Catatan : Tanggal, bulan, dan tahun diambil secara dinamis sesuai
                tanggal surat dibuat
            </p>
        </div>
    </div>
    <div class="flex flex-col bg-white rounded-lg" v-if="letter_types.length > 0 && !loading">
        <div class="px-8 py-5 min-w-full inline-block align-middle">
            <form method="POST" @submit.prevent="save">
                <div class="card-body">
                    <div>
                        <div class="mb-3">
                            <label for="jenis_surat" class="form-label">Jenis Surat</label>
                            <select class="form-control" v-model="letter_type_index"
                                @change="(el) => getReferenceNumber(el.target.value)">
                                <option value="">--Pilih jenis surat--</option>
                                <option v-for="(letter_type, index) in letter_types" :value="index">
                                    {{ letter_type.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <hr class="mb-4" />
                    <div class="row" v-if="reference_number.letter_type != ''">
                        <div class="mb-3 col-md-6">
                            <label for="province" class="form-label">Prefix No Surat</label>
                            <input class="form-control" type="text" id="province" name="province" placeholder=""
                                v-model="reference_number.prefix" />
                        </div>
                        <div class="mb-3 col-md-6">
                            <label for="district" class="form-label">Suffix No Surat</label>
                            <input type="text" class="form-control" id="district" name="district" placeholder=""
                                v-model="reference_number.suffix" />
                        </div>
                        <div class="mb-3 col-lg-12">
                            <h4>
                                Preview No Surat :
                                <span class="text-primary">{{ preview }}</span>
                            </h4>
                            <small class="text-danger">Catatan : XXX adalah kode unik yang akan secara otomatis
                                digenerate oleh sistem.</small>
                        </div>
                    </div>
                    <div class="mt-2 flex" v-if="reference_number.letter_type != ''">
                        <button type="submit" class="btn btn-primary me-2">
                            Simpan Format
                        </button>
                        <button type="reset" class="btn btn-outline-danger" @click="reset">
                            Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="mt-2 text-center" v-else> Loading ... </div>
</template>