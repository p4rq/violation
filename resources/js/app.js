import './bootstrap';
import {createApp} from 'vue';
import ViolationComponent from "./components/ViolationComponent.vue";

const app = createApp({});

app.component('violation-component', ViolationComponent);

app.mount('#app');
