<template>
    <div>
        <NavBar />
        <main class="mt-5 container">
            <router-view></router-view>
        </main>
    </div>
</template>

<script>
import { Suspense } from "vue";

export default {
    name: "default-layout",
}
</script>
