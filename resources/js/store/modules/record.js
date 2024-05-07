import axios from "axios";

export default {
    namespaced: true,
    state: {
        classTypeRecords: [],
        classTypes: [],
        records: [],
        groups: [],
        disciplines: [],
    },
    getters: {
        getClassTypeRecords: s => s.classTypeRecords,
        getClassTypes: s => s.classTypes,
        getRecords: s => s.records,
        getGroups: s => s.groups,
        getDisciplines: s => s.disciplines,
    },
    mutations: {
        SET_CLASS_TYPE_RECORDS (state, value) {
            state.classTypeRecords = value
        },
        SET_CLASS_TYPES (state, value) {
            state.classTypes = value
        },
        SET_RECORDS (state, value) {
            state.records = value
        },
        SET_GROUPS (state, value) {
            state.groups = value
        },
        SET_DISCIPLINES (state, value) {
            state.disciplines = value
        },
        DELETE_RECORD (state, value) {
            state.classTypeRecords = [
                ...state.classTypeRecords.slice(0, value.classTypeRecordIndex),
                ...state.classTypeRecords.slice(value.classTypeRecordIndex + 1)
            ];

            state.records = [
                ...state.records.slice(0, value.recordIndex),
                ...state.records.slice(value.recordIndex + 1)
            ];
        },
    },
    actions: {
        fetchAllRecords({ commit }) {
            return axios.get('api/records/').then(({ data }) => {
                commit('SET_CLASS_TYPE_RECORDS', data.classTypeRecords);
                commit('SET_CLASS_TYPES', data.classTypes);
                commit('SET_RECORDS', data.records);
                commit('SET_GROUPS', data.groups);
                commit('SET_DISCIPLINES', data.disciplines);
            });
        },
        addNewOrUpdateRecord({ commit, state }, params) {
            const recordIndex = state.records.map((record) => record.id).indexOf(params.record.id);

            if (recordIndex >= 0) state.records[recordIndex] = params.record;
            else state.records.push(params.record);

            const groupIndex = state.groups.map((group) => group.id).indexOf(params.group.id);
            const disciplinesIndex = state.disciplines.map((discipline) => discipline.id).indexOf(params.discipline.id);

            if (groupIndex < 0) state.groups.push(params.group);
            if (disciplinesIndex < 0) state.disciplines.push(params.discipline);

            params.classTypeRecords.forEach((classTypeRecord) => {
                const classTypeRecordIndex = state.classTypeRecords.findIndex((item) =>
                    item['class_type_id'] === classTypeRecord['class_type_id']
                    && item['record_id'] === classTypeRecord['record_id']
                );

                if (classTypeRecordIndex < 0) state.classTypeRecords.push(classTypeRecord);
                else state.classTypeRecords[classTypeRecordIndex]['hours_spend'] = classTypeRecord['hours_spend'];
            });
        },
        deleteRecord({ commit, state }, params) {
            const classTypeRecordIndex = state.classTypeRecords.findIndex((item) =>
                item['class_type_id'] === params['class_type_id']
                && item['record_id'] === params['record_id']
            );
            const recordIndex = state.records.findIndex((item) =>
                item['id'] === params['record_id']
            );

            commit('DELETE_RECORD', { classTypeRecordIndex, recordIndex });
        },
    },
}
