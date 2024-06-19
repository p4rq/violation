<template>
    <div class="container mt-4">
        <!-- Панель инструментов -->
        <div class="toolbar mb-3 d-flex align-items-center justify-content-start">
            <h5 class="me-3 mb-0">Панель инструментов:</h5>
            <button type="button" class="btn btn-success me-2" @click="addViolation">Добавить</button>
            <button type="button" class="btn btn-danger me-2" @click="deleteViolation">Удалить</button>
            <button type="button" class="btn btn-primary" @click="editViolation">Редактировать</button>
        </div>

        <div class="row">
            <!-- Левая часть с деревом элементов -->
            <div class="col-6">
                <div class="tree">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span @click="toggleSubViolations(null)" style="cursor: pointer;">
                                Root
                                <span v-html="expandIcon(null)"></span>
                            </span>
                            <ul v-if="isExpanded(null)" class="list-group">
                                <violation-item
                                    v-for="violation in parentViolations"
                                    :key="violation.id"
                                    :violation="violation"
                                    :violations="violations"
                                    :expanded-violations="expandedViolations"
                                    @toggle="toggleSubViolations"
                                    @edit="startEditViolation"
                                />
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Правая часть с панелью редактирования -->
            <div class="col-6" v-if="selectedViolation">
                <div class="edit-panel border p-3 rounded">
                    <h3>Редактировать элемент</h3>
                    <div class="form-group mb-3">
                        <label>Название:</label>
                        <input v-model="selectedViolation.name" class="form-control" />
                    </div>
                    <div class="form-group mb-3 form-check">
                        <input type="checkbox" v-model="selectedViolation.is_active" class="form-check-input" />
                        <label class="form-check-label">is_active</label>
                    </div>
                    <div class="form-group mb-3 form-check">
                        <input type="checkbox" v-model="selectedViolation.is_selectable" class="form-check-input" />
                        <label class="form-check-label">is_selectable</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-success me-2" @click="saveViolation">
                        Сохранить
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" @click="cancelEdit">
                        Отменить
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
//q
import axios from 'axios';
import ViolationItem from './ViolationItem.vue';

export default {
    name: 'IndexComponent',
    components: { ViolationItem },
    data() {
        return {
            violations: [],
            expandedViolations: [],
            selectedViolation: null,
        };
    },
    computed: {
        parentViolations() {
            return this.violations.filter(v => v.parent_id === null);
        },
    },
    methods: {
        getViolations() {
            axios.get('http://localhost:8000/violations')
                .then(response => {
                    this.violations = response.data;
                })
                .catch(error => {
                    console.error('Error fetching violations:', error);
                });
        },
        toggleSubViolations(violationId) {
            if (this.isExpanded(violationId)) {
                this.expandedViolations = this.expandedViolations.filter(id => id !== violationId);
            } else {
                this.expandedViolations.push(violationId);
            }
        },
        isExpanded(violationId) {
            return this.expandedViolations.includes(violationId);
        },
        expandIcon(violationId) {
            return this.isExpanded(violationId) ? '&#9660;' : '&#9658;';
        },
        addViolation() {
            console.log('Add violation');
        },
        deleteViolation() {
            if (this.selectedViolation) {
                axios.delete(`http://localhost:8000/violations/${this.selectedViolation.id}`)
                    .then(() => {
                        this.violations = this.violations.filter(v => v.id !== this.selectedViolation.id);
                        this.selectedViolation = null;
                    })
                    .catch(error => {
                        console.error('Error deleting violation:', error);
                    });
            }
        },
        editViolation() {
            if (this.selectedViolation) {
                this.startEditViolation(this.selectedViolation);
            }
        },
        startEditViolation(violation) {
            this.selectedViolation = { ...violation };
        },
        saveViolation() {
            if (this.selectedViolation) {
                axios.put(`http://localhost:8000/violations/${this.selectedViolation.id}`, this.selectedViolation)
                    .then(response => {
                        const index = this.violations.findIndex(v => v.id === this.selectedViolation.id);
                        this.$set(this.violations, index, response.data);
                        this.selectedViolation = null;
                    })
                    .catch(error => {
                        console.error('Error updating violation:', error);
                    });
            }
        },
        cancelEdit() {
            this.selectedViolation = null;
        }
    },
    mounted() {
        this.getViolations();
    }
}
</script>

<style scoped>
.toolbar {
    display: flex;
    justify-content: flex-start;
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.tree {
    margin-top: 20px;
}
.edit-panel {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 5px;
    background-color: #f8f9fa;
}
</style>
