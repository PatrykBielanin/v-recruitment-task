<script setup>
    import AppLoading from './AppLoading.vue';
    import {ref} from 'vue'

    const active = ref()
    defineProps({
        isActive: {
            type: Boolean,
            required: false,
            default: false
        },
        loading: {
            type: Boolean,
            required: false,
            default: false
        }
    })

</script>

<template>
    <button class="btn shadow-xl" :class="{'btn-active': isActive, 'flex justify-center': loading}">
        <AppLoading v-if="loading"></AppLoading>
        <slot v-else />
    </button>
</template>
