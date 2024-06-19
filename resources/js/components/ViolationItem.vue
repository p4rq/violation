<template>
    <li class="list-group-item">
        <div>
            <span @click="toggle" style="cursor: pointer;">
                {{ violation.name }}
                <span v-html="expandIcon"></span>
            </span>
            <button type="button" class="btn btn-sm btn-primary ms-2" @click="edit">
                Edit
            </button>
            <ul v-if="isExpanded" class="list-group mt-2">
                <violation-item
                    v-for="child in children"
                    :key="child.id"
                    :violation="child"
                    :violations="violations"
                    :expanded-violations="expandedViolations"
                    @toggle="toggle"
                    @edit="$emit('edit', $event)"
                />
            </ul>
        </div>
    </li>
</template>

<script>
export default {
    name: 'ViolationItem',
    props: ['violation', 'violations', 'expandedViolations'],
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
        }
    }
}
</script>
