<template>
    <div class="d-flex align-center justify-center" style="height: 100vh">
        <v-sheet width="500" class="mx-auto py-15 px-15" :elevation=20>
            <slot></slot>
        </v-sheet>
    </div>
</template>

<script>
export default {
    data() {
        return {
        };
    },
    methods: {
    },
}
</script>