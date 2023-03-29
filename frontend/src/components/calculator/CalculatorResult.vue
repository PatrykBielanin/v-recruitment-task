<script setup>
    import IconDollar from '../icons/IconDollar.vue';
    import { useAppStore } from '../../stores/app';
    import {ref} from 'vue'

    const store = useAppStore()

    defineProps({
        result: {
            type: Number,
            required: true,
            default: 0
        }
    })
</script>

<template>
    <div class="flex flex-col justify-center">
        <div class="text-left w-full">
            <h1 class="font-semibold text-3xl mb-4">Wynik</h1>
            <p>Na podstawie podanych przez Ciebie danych obliczona składka OC/AC wynosi</p>
        </div>


        <div class="flex items-center mt-6">
            <p class="font-bold text-3xl text-green">
                {{ store.activeResult == 'price' ? store.price.calculated : store.installment.calculated}} zł
            </p>
        </div>
    </div>
</template>
