import * as Vue from 'vue';

document.addEventListener('DOMContentLoaded', () => {
    Vue.createApp({
        name: "App",
        delimiters: ["{[{", "}]}"],
        data() {
            return { a: 1 }
        },
    }).mount('#app');
});
