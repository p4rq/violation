<template>
    <div class="container mt-2" @click="handleClickOutside">
        <!-- Toolbar for Search, Filter, and Sort -->
        <div class="toolbar mb-2 d-flex align-items-center justify-content-between">
            <div class="input-group me-2">
                <input type="text" v-model="searchQuery" @input="applyFilters" placeholder="Поиск..." class="form-control form-control-sm" />
                <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>

            <div class="dropdown me-2" ref="sortDropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="sortDropdownButton" @click="toggleSortDropdown">
                    <i class="bi bi-sort-alpha-down"></i>
                </button>
                <div class="dropdown-menu" :class="{ show: showSortDropdown }" aria-labelledby="sortDropdownButton">
                    <button class="dropdown-item" @click="setSortOption('name_asc')">По названию (возр.)</button>
                    <button class="dropdown-item" @click="setSortOption('name_desc')">По названию (убыв.)</button>
                    <button class="dropdown-item" @click="setSortOption('children_count_asc')">По количеству детей (возр.)</button>
                    <button class="dropdown-item" @click="setSortOption('children_count_desc')">По количеству детей (убыв.)</button>
                </div>
            </div>

            <div class="dropdown" ref="filterDropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdownButton" @click="toggleFilterDropdown">
                    <i class="bi bi-funnel"></i>
                </button>
                <div class="dropdown-menu" :class="{ show: showFilterDropdown }" aria-labelledby="filterDropdownButton">
                    <div class="p-2">
                        <div class="form-group mb-2">
                            <label for="filterActive">Активные:</label>
                            <select id="filterActive" v-model="filter.isActive" class="form-select form-select-sm">
                                <option value="">Все</option>
                                <option value="true">Активные</option>
                                <option value="false">Неактивные</option>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="filterSelectable">Выбираемые:</label>
                            <select id="filterSelectable" v-model="filter.isSelectable" class="form-select form-select-sm">
                                <option value="">Все</option>
                                <option value="true">Выбираемые</option>
                                <option value="false">Не выбираемые</option>
                            </select>
                        </div>
                        <button class="btn btn-sm btn-primary w-100" @click="applyFilters">Применить</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- List of Violations -->
        <div class="row">
            <div class="col-6 tree-panel">
                <div class="tree">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <button class="tree-button root-button" @click="addParentViolation">
                                    Root
                                    <span @click.stop="toggleSubViolations(null)" v-if="isExpanded(null)">&#9660;</span>
                                    <span @click.stop="toggleSubViolations(null)" v-else>&#9658;</span>
                                </button>
                            </div>
                            <ul v-if="isExpanded(null)" class="list-group mt-1">
                                <violation-item
                                    v-for="violation in filteredAndSortedViolations"
                                    :key="violation.id"
                                    :violation="violation"
                                    :violations="violations"
                                    :expanded-violations="expandedViolations"
                                    :selected-violation-id="selectedViolation ? selectedViolation.id : null"
                                    :delete-mode="deleteMode"
                                    @toggle="toggleSubViolations"
                                    @edit="startEditViolation"
                                />
                            </ul>
                        </li>
                    </ul>
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <!-- First Page -->
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <a class="page-link" @click="goToPage(1)" href="javascript:void(0)">First</a>
                        </li>
                        <!-- Previous Page -->
                        <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                            <a class="page-link" @click="goToPage(pagination.current_page - 1)" href="javascript:void(0)">Previous</a>
                        </li>
                        <!-- Page Numbers -->
                        <li class="page-item" v-for="page in pagesToShow" :key="page" :class="{ active: page === pagination.current_page }">
                            <a class="page-link" @click="goToPage(page)" href="javascript:void(0)">{{ page }}</a>
                        </li>
                        <!-- Next Page -->
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" @click="goToPage(pagination.current_page + 1)" href="javascript:void(0)">Next</a>
                        </li>
                        <!-- Last Page -->
                        <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                            <a class="page-link" @click="goToPage(pagination.last_page)" href="javascript:void(0)">Last</a>
                        </li>
                    </ul>
                </nav>

            </div>

            <div class="col-6 edit-panel" v-if="selectedViolation || creatingNewViolation">
                <div class="edit-panel-content border p-2 rounded">
                    <h5>{{ creatingNewViolation ? 'Создать новый элемент' : 'Редактировать элемент' }}</h5>
                    <div class="form-group mb-2">
                        <label>Название:</label>
                        <input v-model="currentViolation.name" class="form-control form-control-sm"/>
                    </div>
                    <div class="form-group mb-2 form-check">
                        <input type="checkbox" v-model="currentViolation.is_active" class="form-check-input"/>
                        <label class="form-check-label">is_active</label>
                    </div>
                    <div class="form-group mb-2 form-check">
                        <input type="checkbox" v-model="currentViolation.is_selectable" class="form-check-input"/>
                        <label class="form-check-label">is_selectable</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-success me-1" @click="saveViolation">
                        Сохранить
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary me-1" @click="cancelEdit">
                        Отменить
                    </button>
                    <button v-if="!creatingNewViolation" type="button" class="btn btn-sm btn-danger me-1" @click="confirmDeleteViolation(currentViolation)">
                        Удалить
                    </button>
                    <button v-if="!creatingNewViolation" type="button" class="btn btn-sm btn-primary" @click="startAddChildViolation(selectedViolation)">
                        Добавить дочерний элемент
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import ViolationItem from './ViolationItem.vue';

export default {
    name: 'IndexComponent',
    components: { ViolationItem },
    data() {
        return {
            violations: [],
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 40,
                total: 0,
            },
            searchQuery: '',
            filter: {
                isActive: '',
                isSelectable: '',
            },
            sortOption: 'name_asc',
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
            showFilterDropdown: false,
            showSortDropdown: false,
        };
    },
    computed: {
        filteredAndSortedViolations() {
            let violations = this.violations.filter(v => v.parent_id === null);

            switch (this.sortOption) {
                case 'name_asc':
                    violations.sort((a, b) => a.name.localeCompare(b.name));
                    break;
                case 'name_desc':
                    violations.sort((a, b) => b.name.localeCompare(a.name));
                    break;
                case 'children_count_asc':
                    violations.sort((a, b) => this.getChildrenCount(a.id) - this.getChildrenCount(b.id));
                    break;
                case 'children_count_desc':
                    violations.sort((a, b) => this.getChildrenCount(b.id) - this.getChildrenCount(a.id));
                    break;
            }

            return violations;
        },
        pagesToShow() {
            let pages = [];
            const range = 2; // number of pages to show around current page

            let start = this.pagination.current_page - range;
            let end = this.pagination.current_page + range;

            // Ensure start and end are within bounds
            if (start < 1) {
                start = 1;
                end = Math.min(start + range * 2, this.pagination.last_page);
            }
            if (end > this.pagination.last_page) {
                end = this.pagination.last_page;
                start = Math.max(end - range * 2, 1);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        }
    },
    mounted() {
        this.fetchViolations();
    },
    methods: {
        fetchViolations() {
            const params = {
                page: this.pagination.current_page,
                per_page: this.pagination.per_page,
                search: this.searchQuery,
                sort: this.sortOption,
                filter_active: this.filter.isActive,
                filter_selectable: this.filter.isSelectable,
            };

            axios.get('/violations', { params })
                .then(response => {
                    this.violations = response.data.data;
                    this.pagination.current_page = response.data.current_page;
                    this.pagination.last_page = response.data.last_page;
                    this.pagination.total = response.data.total;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        fetchChildren(violationId) {
            console.log(`Fetching children for violation ID: ${violationId}`);
            axios.get(`/violations?parent_id=${violationId}`)
                .then(response => {
                    console.log('Fetched children:', response.data.data);
                    const children = response.data.data;
                    if (children.length > 0) {
                        this.addUniqueViolations(children);
                    } else {
                        console.log('No children found for this parent.');
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
        addUniqueViolations(newViolations) {
            console.log('Adding unique violations:', newViolations);
            newViolations.forEach(newViolation => {
                if (!this.violations.some(v => v.id === newViolation.id)) {
                    this.violations.push(newViolation);
                }
            });
            console.log('Updated violations:', this.violations);
        },
        goToPage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.pagination.current_page = page;
                this.fetchViolations();
            }
        },
        applyFilters() {
            this.pagination.current_page = 1;
            this.fetchViolations();
        },
        toggleSortDropdown() {
            this.showSortDropdown = !this.showSortDropdown;
            this.showFilterDropdown = false;
        },
        toggleFilterDropdown() {
            this.showFilterDropdown = !this.showFilterDropdown;
            this.showSortDropdown = false;
        },
        setSortOption(option) {
            this.sortOption = option;
            this.applyFilters();
            this.showSortDropdown = false;
        },
        addParentViolation() {
            this.selectedViolation = null;
            this.creatingNewViolation = true;
            this.currentViolation = {
                name: '',
                is_active: false,
                is_selectable: false,
                parent_id: null,
            };
        },
        startEditViolation(violation) {
            this.selectedViolation = violation;
            this.creatingNewViolation = false;
            this.currentViolation = { ...violation };
        },
        startAddChildViolation(parentViolation) {
            this.creatingNewViolation = true;
            this.currentViolation = {
                name: '',
                is_active: false,
                is_selectable: false,
                parent_id: parentViolation.id,
            };
        },
        saveViolation() {
            const saveAction = this.creatingNewViolation ? axios.post : axios.put;
            const url = this.creatingNewViolation ? '/violations' : `/violations/${this.currentViolation.id}`;

            saveAction(url, this.currentViolation)
                .then(() => {
                    this.fetchViolations();
                    this.cancelEdit();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        cancelEdit() {
            this.selectedViolation = null;
            this.creatingNewViolation = false;
            this.currentViolation = {
                name: '',
                is_active: false,
                is_selectable: false,
                parent_id: null,
            };
        },
        confirmDeleteViolation(violation) {
            Swal.fire({
                title: 'Вы уверены?',
                text: "Это действие нельзя будет отменить!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Да, удалить!',
                cancelButtonText: 'Отменить'
            }).then(result => {
                if (result.isConfirmed) {
                    this.deleteViolation(violation);
                }
            });
        },
        deleteViolation(violation) {
            axios.delete(`/violations/${violation.id}`)
                .then(() => {
                    this.fetchViolations();
                    this.cancelEdit();
                })
                .catch(error => {
                    console.error(error);
                });
        },
        toggleSubViolations(violationId) {
            const index = this.expandedViolations.indexOf(violationId);
            if (index === -1) {
                this.expandedViolations.push(violationId);
                this.fetchChildren(violationId);
            } else {
                this.expandedViolations.splice(index, 1);
            }
        },
        isExpanded(violationId) {
            return this.expandedViolations.includes(violationId);
        },
        getChildrenCount(violationId) {
            return this.violations.filter(v => v.parent_id === violationId).length;
        },
        handleClickOutside(event) {
            if (!this.$refs.sortDropdown.contains(event.target)) {
                this.showSortDropdown = false;
            }
            if (!this.$refs.filterDropdown.contains(event.target)) {
                this.showFilterDropdown = false;
            }
        },
    },
    beforeDestroy() {
        document.removeEventListener('click', this.handleClickOutside);
    },
};
</script>

<style scoped>
.container {
    padding: 20px;
    background-color: #f0f2f5;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.toolbar {
    display: flex;
    justify-content: flex-start;
    background-color: transparent;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: none;
}

.tree {
    margin-top: 10px;
    height: 100%;
    overflow-y: auto;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.edit-panel-content {
    height: 100%;
    margin-top: 10px;
    background-color: #ffffff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.edit-panel,
.tree-panel {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.list-group-item {
    display: block;
    margin-bottom: 10px;
    list-style: none;
    background-color: #ffffff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.list-group-item > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.list-group-item button.tree-button {
    background: none;
    border: none;
    font-size: 0.875rem;
    font-family: inherit;
    color: #007bff;
    cursor: pointer;
}

.root-button {
    color: black !important;
}

.list-group-item button.tree-button:hover {
    text-decoration: underline;
}

.tree-button.btn {
    padding: 0.125rem 0.25rem;
    border-radius: 50%;
}

.tree-button.btn-add {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
    font-weight: bold;
    transition: background-color 0.3s, border-color 0.3s;
}

.tree-button.btn-add:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    color: white;
}

.tree-button.btn-add:focus {
    outline: none;
    box-shadow: 0 0 0 0.1rem rgba(0, 123, 255, 0.25);
}

.violation-item .list-group-item {
    margin-bottom: 5px;
    list-style: none;
    background-color: #ffffff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.violation-item .list-group-item > div {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.edit-panel-content h5 {
    margin-bottom: 20px;
    font-size: 1.25rem;
}

.form-group {
    margin-bottom: 15px;
}

.form-control {
    border-radius: 4px;
    border: 1px solid #d1d5db;
    padding: 0.375rem 0.75rem;
}

.form-check-input {
    margin-right: 10px;
}

.form-check-label {
    margin-bottom: 0;
}

.btn {
    border-radius: 4px;
    transition: background-color 0.2s, border-color 0.2s;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
}

.dropdown-menu.show {
    display: block;
}
</style>
