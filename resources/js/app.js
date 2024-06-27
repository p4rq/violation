import './bootstrap';
import {createApp} from 'vue';
import ViolationComponent from "./components/ViolationComponent.vue";
import '../css/app.css';
import 'bootstrap-icons/font/bootstrap-icons.css';



const app = createApp({});

app.component('violation-component', ViolationComponent);

app.mount('#app');
