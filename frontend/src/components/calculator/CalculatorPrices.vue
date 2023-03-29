<script setup>
    import AppButton from '../partials/AppButton.vue'
    import IconCalendar from '../icons/IconCalendar.vue';
    import IconDollar from '../icons/IconDollar.vue';
    import AppErrorMessage from '../partials/AppErrorMessage.vue';
    import { ref } from 'vue'
    import { useAppStore } from '../../stores/app';

    const store = useAppStore()

    defineProps({
        type: {
            type: String,
            required: false,
            default: 'brutto'
        }
    })

    const form = ref({
        'price': null
    })
    const priceMode = ref('brutto')
    const calculated = ref('0.00 zł')

    const setPriceMode = (mode) => {
        priceMode.value = mode
        calculateVatPrice()
    }

    const calculateVatPrice = () => {
        if(form.value.price < 1){
            return
        }

        let brutto = (form.value.price * 1.23).toFixed(2)
        let netto = (form.value.price / 1.23).toFixed(2)

        if(priceMode.value == 'brutto') {
            store.setItem('brutto', form.value.price)
            store.setItem('netto', netto)

            return calculated.value = `${netto} zł`
        }

        store.setItem('brutto', brutto)
        store.setItem('netto', form.value.price)
        return calculated.value = `${brutto} zł`
    }

</script>

<template>
    <div class="space-y-8">
        <div>
            <p class="text-lg font-light mb-2">Podaj <span class="font-bold">rok</span> produkcji pojazdu</p>
            <div class="relative">
                <input
                    v-model="form.year"
                    type="number"
                    placeholder="2023"
                    class="input-text focus:border-green"
                    :class="{'input-error': store.price.errors.year}"
                    @input="store.setItem('year', form.year)"
                />
                <IconCalendar class="w-4 absolute top-1/2 right-[10px] transform -translate-y-1/2"></IconCalendar>
            </div>
            <AppErrorMessage v-if="store.price.errors.year">Musisz podać rok produkcji swojego pojazdu!</AppErrorMessage>
        </div>

        <div class="my-4">
            <p class="mb-4">Wybierz typ kwoty, który zamierzasz wprowadzić</p>

            <AppButton @click="setPriceMode('brutto')" :isActive="priceMode == 'brutto'">
                Brutto
            </AppButton>

            <AppButton @click="setPriceMode('netto')" :isActive="priceMode == 'netto'">
                Netto
            </AppButton>
        </div>

        <div class="mb-4">
            <p class="text-lg font-light mb-2">Podaj wartość <span class="font-bold">{{ priceMode == 'brutto' ? 'brutto' : 'netto' }}</span> pojazdu</p>
            <div class="relative">
                <input
                    @input="calculateVatPrice(), store.setItem(priceMode, form.price)"
                    v-model="form.price"
                    type="number"
                    class="input-text focus:border-green"
                    :class="{'input-error': store.price.errors.price}"
                    placeholder="0"
                />
                <IconDollar class="w-3 absolute top-1/2 right-[10px] transform -translate-y-1/2"></IconDollar>
            </div>
            <AppErrorMessage v-if="store.price.errors.price">Musisz podać wartość swojego pojazdu!</AppErrorMessage>

            <p class="text-lg font-light mt-4 text-graywhite/40 mb-2">Wartość <span class="font-bold text-graywhite/40">{{ priceMode != 'brutto' ? 'brutto' : 'netto' }}</span> pojazdu</p>
            <div class="relative">
                <input v-model="calculated" type="text" class="input-text input-disabled" disabled/>
                <IconDollar class="w-3 absolute top-1/2 right-[10px] transform -translate-y-1/2"></IconDollar>
            </div>
        </div>
    </div>
</template>
