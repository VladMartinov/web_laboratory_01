<template>
    <ActionsBar @callRecordModal="openModal()" />
    <ItemsList @callRecordModal="openModal($event)" />
    <BaseModal :visible="modalState" @closeModal="modalState = false">
        <template v-slot:header>{{ modalValue.isUpdate ? "Обновление записи" : "Создание записи" }}</template>
        <template v-slot:body>
            <form>
                <div class="mb-3">
                    <label for="inputDate" class="form-label">Дата</label>
                    <input type="date" class="form-control" id="inputDate" v-model="date">
                </div>
                <div class="mb-3">
                    <label for="inputCourse" class="form-label">Курс</label>
                    <input type="number" class="form-control" id="inputCourse" min="1" max="6"  v-model="course">
                </div>

                <!-- Select the group -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="radioGroup" id="formRadioGroupSelect" :value="false" v-model="groupValue.isNew">
                            <label class="form-check-label" for="formRadioGroupSelect">Выбрать группу</label>
                        </div>
                        <select class="form-select" aria-label="Group select" v-model="groupValue.id" :disabled="groupValue.isNew">
                            <option :value="null" disabled>Выберите группу</option>
                            <option :value="group.id" v-for="group in groups">{{ group['group_name'] }}</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="radioGroup" id="formRadioGroupNew" :value="true" v-model="groupValue.isNew">
                            <label class="form-check-label" for="formRadioGroupNew">Создать новую группу</label>
                        </div>
                        <input type="text" class="form-control" placeholder="Наименование группы" v-model="groupValue.value" :disabled="!groupValue.isNew">
                    </div>
                </div>

                <!-- Select the discipline -->
                <div class="row mb-3">
                    <div class="col">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="radioDiscipline" id="formRadioDisciplineSelect" :value="false" v-model="disciplineValue.isNew">
                            <label class="form-check-label" for="formRadioDisciplineSelect">Выбрать дисциплину</label>
                        </div>
                        <select class="form-select" aria-label="Discipline select" v-model="disciplineValue.id" :disabled="disciplineValue.isNew">
                            <option :value="null" disabled>Выберите дисциплину</option>
                            <option :value="discipline.id" v-for="discipline in disciplines">{{ discipline['discipline_name'] }}</option>
                        </select>
                    </div>
                    <div class="col">
                        <div class="form-check mb-1">
                            <input class="form-check-input" type="radio" name="radioDiscipline" id="formRadioDisciplineNew" :value="true" v-model="disciplineValue.isNew">
                            <label class="form-check-label" for="formRadioDisciplineNew">Создать новую дисциплину</label>
                        </div>
                        <input type="text" class="form-control" placeholder="Наименование дисциплины" v-model="disciplineValue.value" :disabled="!disciplineValue.isNew">
                    </div>
                </div>

                <!-- Hours spend group -->
                <div class="row mb-2 align-items-end" v-for="(classTypeInput, index) in classTypeInputs.values" :key="index">
                    <div class="col" v-for="(input, inputIndex) in classTypeInput" :key="input.id">
                        <label :for="'inputClass' + ((index * classTypeInput.length) + inputIndex)" class="form-label">{{ input.title }}</label>
                        <input type="number" class="form-control" :id="'inputClass' + ((index * classTypeInput.length) + inputIndex)" v-model="input.value" min="0">
                    </div>
                </div>
            </form>
        </template>
        <template v-slot:footer>
            <button type="button" class="btn btn-primary" @click="callModalAction">{{ modalValue.isUpdate ? "Обновить" : "Создать" }}</button>
            <button type="button" class="btn btn-danger" @click="modalState = false">Отменить</button>
        </template>
    </BaseModal>
    <BaseModal :visible="modalConfirmState" @closeModal="modalConfirmState = false">
        <template v-slot:header>
            <h5 class="modal-title">Подтверждение удаления</h5>
        </template>

        <template v-slot:body>
            <p>Вы уверены что хотите удалить данную запись?</p>
            <p>Номер записи: {{ modalValue.value.id }}<br />Дата: {{ modalValue.value.date }}<br />Курс: {{ modalValue.value.course }}<br />Группа: {{ modalValue.value.group['group_name'] }}<br />Дисциплина: {{ modalValue.value.discipline['discipline_name'] }}</p>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-primary" @click="callModalDeleteAction">Подтвердить</button>
            <button type="button" class="btn btn-secondary" @click="modalConfirmState = false">Отмена</button>
        </template>
    </BaseModal>
</template>

<script setup>
import BaseModal from "@/components/BaseModal.vue";

import { computed, onMounted, reactive, ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const store = useStore();
const router = useRouter();

const date = ref(null);
const course = ref(null);

const groupValue = reactive({
    isNew: false,
    id: null,
    value: null,
});
const disciplineValue = reactive({
    isNew: false,
    id: null,
    value: null,
});
const classTypeInputs = reactive({
    values: [],
});

const modalValue = reactive({
    value: {},
    isDelete: false,
    isUpdate: false,
});
const modalState = ref(false);
const modalConfirmState = ref(false);

const classTypes = computed(() => store.getters['record/getClassTypes']);
const groups = computed(() => store.getters['record/getGroups']);
const disciplines = computed(() => store.getters['record/getDisciplines']);

const openModal = function (item) {
    if (item && item.isDelete) {
        modalValue.value = item.value;
        modalValue.isDelete = true;

        modalConfirmState.value = true;
    }
    else if (item && item.isUpdate) {
        modalValue.value = item.value;
        modalValue.isUpdate = true;
        updateValues();

        date.value = item.value.date.slice(0, 10);
        course.value = item.value.course;

        groupValue.id = item.value.group.id;
        disciplineValue.id = item.value.discipline.id;

        item.value.classTypeRecords.forEach((classTypeRecord) => {
            classTypeInputs.values.forEach((classTypeInputsRow, rowIndex) => {
                classTypeInputsRow.forEach((classTypeInput, index) => {
                    if (classTypeRecord['class_type_id'] !== classTypeInput.id) return;

                    classTypeInputs.values[rowIndex][index].value = classTypeRecord['hours_spend'];
                });
            });
        });

        modalState.value = true;
    }
    else {
        updateValues();
        modalState.value = true;
    }
};

const callModalAction = function () {
    console.log("Create object...");

    const data = {
        'recordData': {
            'date': date.value,
            'course': course.value,
            'group_id': groupValue.isNew ? null : groupValue.id,
            'discipline_id': disciplineValue.isNew ? null : disciplineValue.id,
        },
        ...(groupValue.isNew && { 'groupData': { 'group_name': groupValue.value }}),
        ...(disciplineValue.isNew && { 'disciplineData': { 'discipline_name': disciplineValue.value }}),
    };

    const classTypeRecordData = [];

    classTypeInputs.values.forEach((classTypeInput) => {
        classTypeInput.forEach((input) => {
            if (input.value > 0) classTypeRecordData.push({ 'class_type_id': input.id, 'hours_spend': input.value });
        })
    })

    data.classTypeRecordData = classTypeRecordData;

    console.log("Object created: ", data);

    window.axios.default.get('/sanctum/csrf-cookie').then(() => {
        if (modalValue.isUpdate) {
            data.recordData.id = modalValue.value.id;

            window.axios.default.put('api/records/', data).then(({ data }) => {
                if (data.group === null) data.group = groupValue.id;
                if (data.discipline === null) data.discipline = disciplineValue.id;

                store.dispatch('record/addNewOrUpdateRecord', data);
            });
        } else {
            window.axios.default.post('api/records/', data).then(({ data }) => {
                if (data.group === null) data.group = groupValue.id;
                if (data.discipline === null) data.discipline = disciplineValue.id;

                store.dispatch('record/addNewOrUpdateRecord', data);
            });
        }
        modalState.value = false;
    });
};

const callModalDeleteAction = function () {
    window.axios.default.get('/sanctum/csrf-cookie').then(() => {
        window.axios.default.delete(`api/records/${modalValue.value.id}`).then(({ data }) => {
            store.dispatch('record/deleteRecord', modalValue.value.classTypeRecords[0]);
        });
    });
    modalConfirmState.value = false;
};

const updateValues = function () {
    date.value = null;
    course.value = null;

    groupValue.isNew = false;
    groupValue.value = null;
    groupValue.id = null;

    disciplineValue.isNew = false;
    disciplineValue.value = null;
    disciplineValue.id = null;

    classTypeInputs.values.forEach((classTypeInputsRow, rowIndex) => {
        classTypeInputsRow.forEach((classTypeInput, index) => {
            classTypeInputs.values[rowIndex][index].value = null;
        });
    });
}

onMounted(async () => {
    await store.dispatch('record/fetchAllRecords');

    classTypes.getter().forEach((classType, index) => {
        if (index % 3 === 0) classTypeInputs.values[Math.floor(index / 3)] = [];
        classTypeInputs.values[Math.floor(index / 3)].push({ value: null, id: classType.id, title: classType['class_type_name'] });
    });
});
</script>
