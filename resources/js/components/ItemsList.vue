<template>
    <div class="overflow-auto mb-4">
        <table class="table table-striped table-hover text-center">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Дата</th>
                    <th scope="col">Курс</th>
                    <th scope="col">Группа</th>
                    <th scope="col">Название учебной дисциплины</th>
                    <th v-for="classType in classTypes" :key="classType.id">
                        {{ classType['class_type_name'] }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in formattedData" :key="item.id">
                    <td>{{ item.id }}</td>
                    <td>{{ formattedDate(item.date) }}</td>
                    <td>{{ item.course }}</td>
                    <td>{{ item.group['group_name'] }}</td>
                    <td>{{ item.discipline['discipline_name'] }}</td>
                    <td v-for="classType in classTypes" :key="classType.id">
                        {{ getHours(item.classTypeRecords, classType) }}
                    </td>
                    <td>
                        <div class="btn-group-vertical">
                            <button type="button" class="btn btn-primary" @click="updateRecord(item)">Обновить</button>
                            <button type="button" class="btn btn-danger" @click="deleteRecord(item)">Удалить</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <nav v-if="pageCount > 0">
        <ul class="pagination">
            <li class="page-item">
                <button class="page-link" aria-label="Предыдущая" :disabled="currentPage - 1 < 1" @click="selectPage(currentPage - 1)">
                    <span aria-hidden="true">&laquo;</span>
                </button>
            </li>
            <li class="page-item" v-for="value in paginationValues" :key="value">
                <button
                    class="page-link"
                    :class="{ 'active': value === currentPage }"
                    :disabled="value === null"
                    @click="selectPage(value)"
                >
                    {{ value === null ? '...' : value }}
                </button>
            </li>
            <li class="page-item">
                <button class="page-link" aria-label="Следующая" :disabled="currentPage + 1 > pageCount" @click="selectPage(currentPage + 1)">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </li>
            <li class="ms-auto">
                <button type="button" class="btn btn-secondary" @click="showMore">Показать больше...</button>
            </li>
        </ul>
    </nav>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const store = useStore();
const router = useRouter();
const emit = defineEmits(['callRecordModal']);

const classTypeRecords = computed(() => store.getters['record/getClassTypeRecords']);
const classTypes = computed(() => store.getters['record/getClassTypes']);
const records = computed(() => store.getters['record/getRecords']);
const groups = computed(() => store.getters['record/getGroups']);
const disciplines = computed(() => store.getters['record/getDisciplines']);

/* Pagination */
const pageCount = computed(() => Math.ceil(records.getter().length / 10));
const currentPage = ref(0);

const bottomSliceIndex = ref(0);
const topSliceIndex = ref(0);
/* Pagination */

const formattedData = computed(() => {
    const array = [];

    records.getter().forEach((record) => {
        const data = {
            id: record.id,
            date: record.date,
            course: record.course,
        };

        data.group = groups.getter().find((group) => group.id === record['group_id']);
        data.discipline = disciplines.getter().find((discipline) => discipline.id === record['discipline_id']);

        data.classTypeRecords = classTypeRecords.getter().filter((classTypeRecord) => classTypeRecord['record_id'] === record.id);

        array.push(data);
    });

    return array.slice(bottomSliceIndex.value, topSliceIndex.value);
})

const formattedDate = function (date) {
    const dateObject = new Date(date);
    const day = dateObject.getDate().toString().padStart(2, "0");
    const month = (dateObject.getMonth() + 1).toString().padStart(2, "0");
    const year = dateObject.getFullYear();

    return `${day}.${month}.${year}`;
};

const getHours = function (itemClassTypeRecords, classType) {
    const item = itemClassTypeRecords.find((classTypeRecord) => classTypeRecord['class_type_id'] === classType.id);
    return item ? item['hours_spend'] : 0;
}

const updateRecord = function (item) {
    console.log("Call update record function");
    emit('callRecordModal', { value: item, isUpdate: true });
};

const deleteRecord = function (item) {
    console.log("Delete next item: ", item);
    emit('callRecordModal', { value: item, isDelete: true });
};

const selectPage = function (page) {
    currentPage.value = page;

    bottomSliceIndex.value = (currentPage.value - 1) * 10;
    topSliceIndex.value = currentPage.value * 10;
}

const showMore = function () {
    topSliceIndex.value += 10;
}

const paginationValues = computed(() => {
    if (currentPage.value >= 1 && currentPage.value <= 3) return [1, 2, 3, 4, null, pageCount.getter()];
    if (currentPage.value <= pageCount.getter() && currentPage.value >= pageCount.getter() - 2) return [1, null, pageCount.getter() - 3, pageCount.getter() - 2, pageCount.getter() - 1, pageCount.getter()];

    return [1, null, currentPage.value - 1, currentPage.value, currentPage.value + 1, null, pageCount.getter()];
});

onMounted(async () => {
    selectPage(1);
});
</script>
