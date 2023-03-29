<script setup>
    import IconClose from '../icons/IconClose.vue';
    import IconCheck from '../icons/IconCheck.vue';
    import AppButton from '../partials/AppButton.vue';
    import {ref, watch} from 'vue'
    import { useAppStore } from '../../stores/app';

    const store = useAppStore()

    const props = defineProps({
        checkboxes: {
            type: Array,
            required: false,
            default: []
        }
    })

    const form = ref({})

    const checked = ref(0);

    watch(() => store.getYears, function() {
        toggleInstallment(store.years)
    })

    const toggleInstallment = (type) => {
        props.checkboxes.map((item) => {
            if(item.type == type){
                item.value = !item.value
                checked.value = type
                store.setItem('years', type)
                return
            }

            item.value = false
        })
    }
</script>

<template>
    <div class="flex flex-col justify-center h-full" :class="{'blur-md': !store.price.calculated}">
        <div class="w-full">
            <h1 class="font-semibold text-3xl mb-4">Raty</h1>
            <p class="font-semibold mb-6">Wybierz <span class="font-light">ilość rat na które chciałbyś podzielić swoją wyliczoną składkę</span></p>
        </div>

        <div class="w-full flex flex-col xl:flex-row justify-evenly gap-4">
            <div
                v-for="i in checkboxes"
                class="flex flex-grow space-x-2 mb-4 px-6 py-2 items-center bg-background rounded-2xl border border-background transition-all cursor-pointer"
                :class="{'border-green bg-green/10': checked == i.type}"
                @click="toggleInstallment(i.type)"
            >
                <div class="flex items-center w-auto">
                    <div class="w-auto flex justify-center rounded-md px-3 py-2">
                        <IconClose v-if="checked != i.type" class="w-6 2xl:w-8"></IconClose>
                        <IconCheck v-else class="w-6 2xl:w-8"></IconCheck>
                    </div>

                    <input type="radio" v-model="i.value" name="instalments" class="hidden">

                    <div class="w-full pl-1">
                        <p class="text-xl xl:text-sm 2xl:text-xl font-semibold" :class="{'text-green': checked == i.type}">{{ i.name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <AppButton
            class="absolute right-6 -bottom-5"
            :loading="store.installment.loading"
            @click="store.getItemPriceWithInstallments()"
        >
            Oblicz składkę OC/AC z ratami
        </AppButton>
    </div>
</template>
