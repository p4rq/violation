<template>
        <div>
            <label for="newViolationName">Название нарушения:</label>
            <input type="text" id="name" v-model="newViolationName" placeholder="Введите название">
        </div>
        <div>
            <label for="isActive">Активно:</label>
            <input type="checkbox" id="isActive" v-model="isActive">
        </div>
        <div>
            <label for="isSelectable">Выбираемо:</label>
            <input type="checkbox" id="isSelectable" v-model="isSelectable">
        </div>
<!--        <div>-->
<!--            <label for="selectedParentId">Родительское нарушение:</label>-->
<!--            <select id="selectedParentId" v-model="selectedParentId">-->
<!--                <option value="">Выберите родительское нарушение</option>-->
<!--                <option v-for="violation in violations" :value="violation.id">@{{ violation.name }}</option>-->
<!--            </select>-->
<!--        </div>-->
        <div>
            <button @click.prevent="createViolation">Создать</button>
        </div>
</template>

<script>

import axios from "axios";

export default {
    name: "CreateComponent",

    data() {
        return {
            // violations: [],
            newViolationName: '',
            isActive: true,
            isSelectable: true,
            selectedParentId: null,
        }

    },

    methods: {
        createViolation(){
            axios.post('http://localhost:8000/violations', {
                name: this.newViolationName,
                is_active: this.isActive,
                parent_id: this.selectedParentId,
                is_selectable: this.isSelectable,
            })
                .then(response => {
                    console.log(response.data);
                    this.name = null;
                    this.isActive = true;
                    this.isSelectable = true;
                    this.selectedParentId = null;
                })
                .catch(error => {
                    console.error('Error creating violation:', error.response.data);
                });
        }
    }
}
</script>

<style scoped>
</style>
