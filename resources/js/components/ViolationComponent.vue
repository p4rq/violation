<template>
    <div>
        <IndexComponent></IndexComponent>
    </div>
</template>

<script>
import axios from 'axios';
import IndexComponent from "./IndexComponent.vue";

export default {
    name: "PostComponent",
    data() {
        return {
            violations: [],
        }
    },
    mounted() {
    },
    methods: {

    },
    components: {
        IndexComponent
    }
}
</script>

<style scoped>
</style>
