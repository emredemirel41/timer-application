
import AppLayout from '../layouts/AppLayout.vue';

<template>
    <AppLayout>
        Hello this is Home!;
    </AppLayout>
</template>


<script>
import AppLayout from '../layouts/AppLayout.vue';

export default {
    name: "Home",
    data() {
        return {
        };
    },
    components: { AppLayout }
};
</script>