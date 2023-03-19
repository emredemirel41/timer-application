<template>
    <v-app style="background-color:#f5f5f5;">
        <v-content>
            <Home v-if="isAuthenticated"></Home>
            <Login v-else></Login>
        </v-content>
    </v-app>
</template>

<script>
import Login from './views/Login.vue';
import Home from './views/Home.vue';

export default {
    name: "App",
    data() {
        return {
            drawer: false,
            isAuthenticated: false,
        };
    },
    components: { Login, Home }
};
</script>