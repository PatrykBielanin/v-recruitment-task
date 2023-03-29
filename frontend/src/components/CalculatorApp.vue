<script setup>
    import CalculatorResult from './calculator/CalculatorResult.vue';
    import CalculatorInstallments from './calculator/CalculatorInstallments.vue';
    import CalculatorPrices from './calculator/CalculatorPrices.vue';
    import CalculatorAddons from './calculator/CalculatorAddons.vue';
    import AppButton from './partials/AppButton.vue';
    import AppErrorMessage from './partials/AppErrorMessage.vue';
    import { useAppStore } from '../stores/app';

    import {ref} from 'vue'

    const store = useAppStore()

    const form = ref({})
    const isTooExpensive = ref(false)

    const data = [
        {'mode': 'input', 'type': 'year'},
        {'mode': 'input', 'type': 'brutto'},
        {'mode': 'input', 'type': 'netto'},
        {'mode': 'paragraph', 'type': 'Dodatki'},
        {'mode': 'checkbox', 'checkboxes': [{'name': 'GPS', 'value': false}]}
    ]

    const instalments = [
        {'name': '0 rat', 'type': 0, 'value': true},
        {'name': '2 raty', 'type': 2, 'value': false},
        {'name': '4 raty', 'type': 4, 'value': false}
    ]

    const getPrice = () => {
        let hasError = false
        isTooExpensive.value = false
        store.setErrorsDefault('price')

        if(store.netto < 1){
            store.setItemError('price', 'price', true)
            hasError = true
        }

        if(store.year < 1){
            store.setItemError('price', 'year', true)
            hasError = true
        }

        if(hasError){
            return
        }

        if(store.netto > 4000000){
            isTooExpensive.value = true
            return
        }

        store.getItemPrice()
    }
</script>

<template>
    <div class="flex flex-col xl:flex-row w-screen justify-evenly px-6 my-12 xl:my-0 xl:mt-0 space-y-16 xl:space-y-0">
        <div class="flex flex-col space-y-12 w-full xl:w-1/3 px-16 py-16 rounded-3xl bg-item relative shadow-xl">
            <h1 class="font-semibold text-xl" :class="{'mb-8': !isTooExpensive, 'mb-2': isTooExpensive}">Oblicz <span class="font-light">składkę OC/AC swojego pojazdu</span></h1>
            <AppErrorMessage v-if="isTooExpensive">Dla tak drogich samochodów składki nie szacujemy. Skontaktuj się z nami osobiście.</AppErrorMessage>

            <div>
                <CalculatorPrices></CalculatorPrices>
                <CalculatorAddons class="mt-12"></CalculatorAddons>
            </div>

            <AppButton
                class="absolute right-6 -bottom-5"
                @click="getPrice()"
                :loading="store.price.loading"
            >
                Oblicz składkę OC/AC
            </AppButton>
        </div>

        <div class="w-full xl:w-1/2 flex flex-col gap-4">
            <div class="px-16 py-16 rounded-3xl bg-item w-full h-1/2 shadow-xl flex items-center">
                <CalculatorResult :result="0"></CalculatorResult>
            </div>

            <div class="w-full h-1/2 flex-1 px-16 py-16 rounded-3xl bg-item shadow-xl relative z-1">
                <div v-if="!store.price.calculated" class="absolute flex items-center justify-center text-center top-0 left-0 h-full w-full bg-item z-99 rounded-3xl">
                    <p class="text-xl z-[100] px-12 font-semibold text-graywhite">Oblicz składkę OC/AC aby poznać ofertę ratalną</p>
                </div>
                <CalculatorInstallments v-else :checkboxes="instalments"></CalculatorInstallments>
            </div>
        </div>
    </div>
</template>
