<template>
    <div class="list-group-item" :class="{ 'selected': isSelected }">
        <div class="flex items-center w-full font-normal text-gray-900 p-1 rounded-lg transition duration-75 group hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700">
            <div @click="toggleAndEdit" class="flex items-center justify-between w-full cursor-pointer" :class="{'font-bold bg-blue-100': isSelected}">
                <span class="text-blue-600 text-sm">{{ violation.name }}</span>
                <span @click.stop="toggle">{{ isExpanded ? '▼' : '▶' }}</span>
            </div>
        </div>
        <div v-if="isExpanded" class="pl-2 mt-1 border-l border-gray-300">
            <ul class="list-none">
                <li v-for="child in sortedChildren" :key="child.id">
                    <ViolationItem
                        :violation="child"
                        :violations="violations"
                        :expanded-violations="expandedViolations"
                        :selected-violation-id="selectedViolationId"
                        :delete-mode="deleteMode"
                        @toggle="emitToggle"
                        @edit="emitEdit"
                        @delete="emitDelete"
                        @add-child="emitAddChild"
                    />
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ViolationItem',
    props: {
        violation: Object,
        violations: Array,
        expandedViolations: Array,
        selectedViolationId: Number,
        deleteMode: Boolean
    },
    computed: {
        isExpanded() {
            return this.expandedViolations.includes(this.violation.id);
        },
        isSelected() {
            return this.violation.id === this.selectedViolationId;
        },
        children() {
            const children = this.violations.filter(v => v.parent_id === this.violation.id);
            console.log(`Children of ${this.violation.name}:`, children); // Add this line
            return children;
            },
        sortedChildren() {
            return this.children.sort((a, b) => {
                const nameA = a.name;
                const nameB = b.name;

                const isNumberA = !isNaN(nameA);
                const isNumberB = !isNaN(nameB);

                if (isNumberA && isNumberB) {
                    return parseFloat(nameA) - parseFloat(nameB);
                }

                if (isNumberA) return -1;
                if (isNumberB) return 1;

                return nameA.localeCompare(nameB);
            });
        }
    },
    methods: {
        toggleAndEdit() {
            this.toggle();
            this.edit();
        },
        toggle() {
            this.$emit('toggle', this.violation.id);
        },
        edit() {
            this.$emit('edit', this.violation);
        },
        emitToggle(violationId) {
            this.$emit('toggle', violationId);
        },
        emitEdit(violation) {
            this.$emit('edit', violation);
        },
        emitDelete(violation) {
            this.$emit('delete', violation);
        },
        emitAddChild(violation) {
            this.$emit('add-child', violation);
        }
    }
}
</script>

<style scoped>
.list-none {
    list-style: none;
}

.font-bold {
    font-weight: bold;
}

.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.w-full {
    width: 100%;
}

.font-normal {
    font-weight: normal;
}

.text-gray-900 {
    color: #1f2937;
}

.p-1 {
    padding: 0.25rem;
}

.rounded-lg {
    border-radius: 0.25rem;
}

.transition {
    transition: all 0.2s;
}

.duration-75 {
    transition-duration: 75ms;
}

.group:hover .icon {
    color: #2f4c86;
}

.pl-2 {
    padding-left: 0.5rem;
}

.mt-1 {
    margin-top: 0.25rem;
}

.border-l {
    border-left-width: 1px;
}

.border-gray-300 {
    border-color: #d1d5db;
}

.text-blue-600 {
    color: #0c0c0c;
}

.text-sm {
    font-size: 0.875rem;
}

.hover\:bg-gray-200:hover {
    background-color: #a5d1ec;
}

.cursor-pointer {
    cursor: pointer;
}


</style>
