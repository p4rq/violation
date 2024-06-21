<template>
    <div class="container mt-4">
        <!-- Панель инструментов -->
        <div class="toolbar mb-3 d-flex align-items-center justify-content-start">
            <h5 class="me-3 mb-0">Панель инструментов:</h5>
            <button type="button" class="btn btn-success me-2" @click="addViolation">Добавить</button>
            <button type="button" class="btn btn-danger me-2" @click="toggleDeleteMode">Удалить</button>
            <button type="button" class="btn btn-primary" @click="editViolation">Редактировать</button>
        </div>

        <div class="row">
            <!-- Левая часть с деревом элементов -->
            <div class="col-6 tree-panel">
                <div class="tree">
                    <ul class="list-group">
                        <li class="list-group-item" >
                            <span @click="toggleSubViolations(null)" style="cursor: pointer;">
                                Root
                                <span v-html="expandIcon(null)"></span>
                            </span>
                            <ul v-if="isExpanded(null)" class="list-group" >
                                <violation-item
                                    v-for="violation in sortedParentViolations"
                                    :key="violation.id"
                                    :violation="violation"
                                    :violations="violations"
                                    :expanded-violations="expandedViolations"
                                    :delete-mode="deleteMode"
                                    @toggle="toggleSubViolations"
                                    @edit="startEditViolation"
                                    @delete="deleteViolation"
                                />
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Правая часть с панелью редактирования -->
            <div class="col-6 edit-panel" v-if="selectedViolation || creatingNewViolation">
                <div class="edit-panel-content border p-3 rounded">
                    <h3>{{ creatingNewViolation ? 'Создать новый элемент' : 'Редактировать элемент' }}</h3>
                    <div class="form-group mb-3">
                        <label>Название:</label>
                        <input v-model="currentViolation.name" class="form-control" />
                    </div>
                    <div class="form-group mb-3 form-check">
                        <input type="checkbox" v-model="currentViolation.is_active" class="form-check-input" />
                        <label class="form-check-label">is_active</label>
                    </div>
                    <div class="form-group mb-3 form-check">
                        <input type="checkbox" v-model="currentViolation.is_selectable" class="form-check-input" />
                        <label class="form-check-label">is_selectable</label>
                    </div>
                    <div class="form-group mb-3">
                        <label>Родительский элемент:</label>
                        <select v-model="currentViolation.parent_id" class="form-control">
                            <option :value="null">Нет родительского элемента</option>
                            <option v-for="violation in sortedValidParentViolations" :key="violation.id" :value="violation.id">
                                {{ violation.name }}
                            </option>
                        </select>
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
            creatingNewViolation: false,
            currentViolation: {
                name: '',
                is_active: false,
                is_selectable: false,
                parent_id: null,
            },
            deleteMode: false,
        };
    },
    computed: {
        parentViolations() {
            return this.violations.filter(v => v.parent_id === null);
        },
        sortedParentViolations() {
            return this.parentViolations.sort((a, b) => {
                const nameA = a.name.toLowerCase();
                const nameB = b.name.toLowerCase();
                return nameA.localeCompare(nameB, undefined, { numeric: true });
            });
        },
        validParentViolations() {
            return this.violations.filter(v => v.parent_id === null);
        },
        sortedValidParentViolations() {
            return this.validParentViolations.sort((a, b) => {
                const nameA = a.name.toLowerCase();
                const nameB = b.name.toLowerCase();
                return nameA.localeCompare(nameB, undefined, { numeric: true });
            });
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
            this.creatingNewViolation = true;
            this.selectedViolation = null;
            this.currentViolation = {
                name: '',
                is_active: false,
                is_selectable: false,
                parent_id: null,
            };
        },
        deleteViolation(violation) {
            axios.delete(`http://localhost:8000/violations/${violation.id}`)
                .then(() => {
                    this.violations = this.violations.filter(v => v.id !== violation.id);
                    this.selectedViolation = null;
                })
                .catch(error => {
                    console.error('Error deleting violation:', error);
                });
        },
        editViolation() {
            if (this.selectedViolation) {
                this.startEditViolation(this.selectedViolation);
            }
        },
        startEditViolation(violation) {
            this.selectedViolation = { ...violation };
            this.creatingNewViolation = false;
            this.currentViolation = { ...violation };
        },
        saveViolation() {
            if (this.creatingNewViolation) {
                axios.post('http://localhost:8000/violations', this.currentViolation)
                    .then(response => {
                        this.violations.push(response.data);
                        this.currentViolation = {
                            name: '',
                            is_active: false,
                            is_selectable: false,
                            parent_id: null,
                        };
                        this.creatingNewViolation = false;
                        this.selectedViolation = null;
                    })
                    .catch(error => {
                        console.error('Error creating violation:', error);
                    });
            } else if (this.selectedViolation) {
                axios.put(`http://localhost:8000/violations/${this.selectedViolation.id}`, this.currentViolation)
                    .then(response => {
                        const index = this.violations.findIndex(v => v.id === this.selectedViolation.id);
                        this.$set(this.violations, index, response.data);
                        this.selectedViolation = null;
                        this.creatingNewViolation = false;  // Ensure to reset creatingNewViolation
                    })
                    .catch(error => {
                        console.error('Error updating violation:', error);
                    });
            }
        },
        cancelEdit() {
            this.selectedViolation = null;
            this.creatingNewViolation = false;
        },
        toggleDeleteMode() {
            this.deleteMode = !this.deleteMode;
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
    height: 100%;
    overflow-y: auto;
}
.edit-panel-content {
    height: 100%;
    margin-top: 20px;
}
.edit-panel,
.tree-panel {
    height: 100%;
    display: flex;
    flex-direction: column;
}

</style>
