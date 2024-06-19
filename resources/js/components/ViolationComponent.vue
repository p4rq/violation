<template>
    <div>
<!--        <CreateComponent></CreateComponent>-->
        <IndexComponent></IndexComponent>
<!--        <ul>-->
<!--            <li v-for="violation in violations" :key="violation.id">-->
<!--                {{ violation.name }}-->
<!--            </li>-->
<!--        </ul>-->
    </div>
</template>

<script>
import axios from 'axios';
import CreateComponent from "./CreateComponent.vue";
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
        CreateComponent,
        IndexComponent
    }
}
</script>

<style scoped>
</style>
