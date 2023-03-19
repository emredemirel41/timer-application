<template>
    <AuthLayout>
        <v-form fast-fail @submit.prevent="login">
            <v-text-field v-model="username" label="Email"></v-text-field>
            <v-text-field v-model="password" label="Password"></v-text-field>
            <a href="#" class="text-body-2 font-weight-regular">Forgot Password?</a>
            <v-btn type="submit" color="primary" block class="mt-2">Sign in</v-btn>
        </v-form>
        <div class="mt-2">
            <p class="text-body-2">Don't have an account? <a href="#">Sign Up</a></p>
        </div>
    </AuthLayout>
</template>

<script>
import AuthLayout from '../layouts/AuthLayout.vue';

export default {
    name: "Login",
    data() {
        return {
            username: "",
            password: "",
        };
    },
    methods: {
        login() {
            // Your login logic here
        },
    },
    components: { AuthLayout }
}
</script>