import './bootstrap';
import {createApp} from 'vue';
import ViolationComponent from "./components/ViolationComponent.vue";
import SingleViolationComponent from "./components/SingleViolationComponent.vue";

const app = createApp({});

app.component('violation-component', ViolationComponent);
app.component('single-violation-component', SingleViolationComponent);

app.mount('#app');
