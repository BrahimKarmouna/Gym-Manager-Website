<template>
    <section class="h-auto bg-white dark:bg-gray-900">
        <div class="flex flex-col mt-16 px-6 py-8 mx-auto md:h-screen lg:py-0">
            <!-- Logo and Text Section -->
            <a
                href="#"
                class="flex flex-col items-center text-3xl font-bold text-gray-900 dark:text-white"
            >
                <img
                    class="w-96 h-32 md:w-64 md:h-64"
                    :src="Logo"
                    alt="logo"
                />
                <!-- Text under logo, making the font size responsive -->
                <span class="text-xl font-extrabold sm:text-2xl md:text-3xl lg:text-4xl mt-2">Welcome back!</span>
            </a>

            <!-- Form Section -->
            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Sign in to your account
                    </h1>
                    <form
                        @submit.prevent="handleSubmit"
                        class="space-y-4 md:space-y-6"
                    >
                        <div>
                            <label
                                for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Your email</label>
                            <input
                                v-model="form.email"
                                type="email"
                                name="email"
                                id="email"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="name@company.com"
                            >
                        </div>
                        <div>
                            <label
                                for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                            >Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                name="password"
                                id="password"
                                placeholder="••••••••"
                                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input
                                        id="remember"
                                        aria-describedby="remember"
                                        type="checkbox"
                                        class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-primary-600 dark:ring-offset-gray-800"
                                    >
                                </div>
                                <div class="ml-3 text-sm">
                                    <label
                                        for="remember"
                                        class="text-gray-500 dark:text-gray-300"
                                    >Remember me</label>
                                </div>
                            </div>
                            <a
                                href="#"
                                class="text-sm font-medium text-primary-600 hover:underline dark:text-primary-500"
                            >Forgot password?</a>
                        </div>
                        <button
                            type="submit"
                            class="w-full text-black bg-gray-100 hover:bg-grey-200 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800"
                        >
                            Sign in
                        </button>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Don’t have an account yet? <a
                                href="#"
                                class="font-medium text-primary-600 hover:underline dark:text-primary-500"
                            >Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
<script setup>
import { reactive } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';  // Import useRouter to use Vue's router programmatically
import Logo from "../../../resources/gym.png";

const router = useRouter(); // Initialize the router to navigate programmatically

const form = reactive({
    email: '',
    password: ''
});

const handleSubmit = async () => {
    try {
        const response = await axios.post('/login', form);
        console.log('Login successful:', response.data);
        window.location.reload();

        // Store the token (or any other way you track authentication)
        localStorage.setItem("auth_token", response.data.token);

        // Redirect the user to the home page or dashboard after successful login
        router.push({ name: "Dashboard" }); // You can also use router.push('/') to redirect to the homepage
    } catch (error) {
        console.error('Login failed:', error.response?.data || error.message);
    }
};
</script>
