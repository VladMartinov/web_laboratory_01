<template>
    <div class="btn-toolbar justify-content-end mb-3">
        <button class="btn btn-primary" @click="createRecord">Создать</button>
    </div>
</template>

<script setup>
const emit = defineEmits(['callRecordModal']);

const createRecord = function () {
    console.log('Call create record function.');
    emit('callRecordModal');
};
</script>
