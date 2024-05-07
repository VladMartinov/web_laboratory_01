<template>
    <div class="mb-3">
        <label class="form-label">Номер страницы для парсинга (0 или пусто - все страницы)</label>
        <input type="number" class="form-control" v-model="pageValue" min="0">
    </div>

    <div class="input-group mb-3">
        <input type="file" class="form-control" accept=".xls,.xlsx" @change="onChangeFile">
        <button class="btn btn-outline-secondary" :disabled="isFileEmpty" @click="tryLoadFileToDb">Load file to DB</button>
    </div>
    <BaseModal :visible="modalErrorFile" @closeModal="ChangeStateModal()">
        <template v-slot:header>
            <h5 class="modal-title">Некорректный файл!</h5>
        </template>

        <template v-slot:body>
            <p>Вы выбрали не корректный файл (доступные форматы: .xls,.xlsx)! Пожалуйста измените файл и повторите еще раз.</p>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-secondary" @click="ChangeStateModal()">ОК</button>
        </template>
    </BaseModal>
</template>

<script>
import { ref, reactive, computed } from "vue";
import BaseModal from "@/components/BaseModal.vue";

export default {
    name: "LoadFile",
    components: {BaseModal},
    setup() {
        let fileObj = reactive({});
        const pageValue = ref("");

        const modalErrorFile = ref(false);

        const onChangeFile = function (event) {
            if (
                event.target.files[0] &&
                event.target.files[0]['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' &&
                event.target.files[0]['type'] !== 'application/vnd.ms-excel'
            ) {
                event.target.value = "";
                if (fileObj.file) delete fileObj.file;

                modalErrorFile.value = true;
                return;
            }

            const reader = new FileReader();

            reader.addEventListener('load', () => {
                localStorage.setItem('file', reader.result);
            });

            reader.readAsDataURL(event.target.files[0]);

            fileObj.file = event.target.files[0];
        };

        const tryLoadFileToDb = function () {
            const dataFile = new FormData();
            dataFile.append('excel_file', fileObj.file);
            dataFile.append('pageValue', pageValue.value);

            window.axios.default.get('/sanctum/csrf-cookie').then(() => {
                window.axios.default.post('/api/file', dataFile);
            });
        };

        const ChangeStateModal = function () {
            modalErrorFile.value = false;
        };

        const isFileEmpty = computed(() => {
            return Object.keys(fileObj).length === 0 || typeof fileObj.file === "undefined";
        });

        return {
            fileObj,

            pageValue,
            modalErrorFile,

            onChangeFile,
            tryLoadFileToDb,
            ChangeStateModal,

            isFileEmpty,
        };
    },
}
</script>
