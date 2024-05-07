import { createStore } from 'vuex' // Vuex's createStore function to create a store
import auth from "@/store/modules/auth.js";
import record from "@/store/modules/record.js";

const store = createStore({
    modules: {
        auth,
        record,
    },
})

export default store;
