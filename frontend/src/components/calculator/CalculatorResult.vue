<script setup>
    import IconCheck from '../icons/IconCheck.vue';
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

    const formatPriceWithInstallments = (price, years) => {
        return (price / years).toFixed(2);
    }
</script>

<template>
    <div class="flex flex-col justify-center">
        <div class="text-left w-full">
            <h1 class="font-semibold text-3xl mb-4">Wynik</h1>
            <p>Na podstawie podanych przez Ciebie danych obliczona składka OC/AC wynosi</p>
        </div>


        <div class="flex flex-col justify-center mt-6">
            <p class="font-bold text-3xl text-green mb-4">
                {{ store.activeResult == 'price' ? store.price.calculated : store.installment.calculated}} zł
            </p>

            <div class="flex" v-if="store.activeResult != 'price' && store.installment.years != 0">
                <IconCheck class="w-4 mr-2"></IconCheck>
                <p>koszt jednej raty z <span class="font-bold text-green">{{ store.installment.years }}</span> wybranych to <span class="font-bold text-green">{{ formatPriceWithInstallments(store.installment.calculated, store.installment.years) }} zł</span> </p>
            </div>
        </div>
    </div>
</template>
