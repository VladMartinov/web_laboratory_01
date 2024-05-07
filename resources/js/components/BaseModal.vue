<template>
    <div v-if="state" class="modal fade show d-block">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <slot name="header"></slot>
                    <button type="button" class="btn-close" @click="CloseModal()" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <slot  name="body"></slot>
                </div>
                <div class="modal-footer">
                    <slot  name="footer"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { toRefs } from "vue";

export default {
    name: 'BaseModal',
    props: {
        visible: {
            type: Boolean,
            default: false,
        },
    },
    setup(props, { emit }) {
        const state = toRefs(props).visible;

        const CloseModal = function () {
            emit('closeModal');
        };

        return {
            state,

            CloseModal,
        };
    },
};
</script>

<style scoped>
</style>
