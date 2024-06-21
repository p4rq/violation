<template>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <span @click="edit" style="cursor: pointer;">
                {{ violation.name }}
                <span @click.stop="toggle" v-html="expandIcon" style="cursor: pointer;"></span>
            </span>
            <ul v-if="isExpanded" class="list-group mt-2">
                <violation-item
                    v-for="child in children"
                    :key="child.id"
                    :violation="child"
                    :violations="violations"
                    :expanded-violations="expandedViolations"
                    :delete-mode="deleteMode"
                    @toggle="toggle"
                    @edit="emitEdit"
                    @delete="emitDelete"
                />
            </ul>
        </div>
        <button v-if="deleteMode" type="button" class="btn btn-danger btn-sm" @click="deleteViolation">Удалить</button>
    </li>
</template>

<script>
export default {
    name: 'ViolationItem',
    props: ['violation', 'violations', 'expandedViolations', 'deleteMode'],
    computed: {
        isExpanded() {
            return this.expandedViolations.includes(this.violation.id);
        },
        children() {
            return this.violations.filter(v => v.parent_id === this.violation.id);
        },
        expandIcon() {
            return this.isExpanded ? '&#9660;' : '&#9658;';
        },
    },
    methods: {
        toggle() {
            this.$emit('toggle', this.violation.id);
        },
        edit() {
            this.$emit('edit', this.violation);
        },
        deleteViolation() {
            this.$emit('delete', this.violation);
        },
        emitEdit(violation) {
            this.$emit('edit', violation);
        },
        emitDelete(violation) {
            this.$emit('delete', violation);
        }
    }
}
</script>
