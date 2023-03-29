<script setup>
    import IconClose from '../icons/IconClose.vue';
    import IconCheck from '../icons/IconCheck.vue';
    import { ref } from 'vue'
    import { useAppStore } from '../../stores/app';

    const store = useAppStore()

    const addons = ref([
        {'name': 'GPS', 'desc': 'Montaż modułu GPS', 'value': 1}
    ])

    const toggleAddon = (name) => {
        addons.value.map((item) => {
            if(item.name == name){
                item.value = !item.value
                store.setItem('gps', item.value)
            }
        })
    }

</script>

<template>
    <div>
        <h2 class="font-semibold text-2xl my-4">Dodatki</h2>

        <div
            v-for="i in addons"
            class="flex space-x-2 px-6 py-2 items-center bg-background rounded-2xl border border-background transition-all cursor-pointer"
            :class="{'border-green bg-green/10': i.value}"
            @click="toggleAddon(i.name)"
        >
            <input type="checkbox" v-model="i.value" class="hidden">

            <div class="flex items-center w-full">
                <div class="w-auto flex justify-center rounded-md px-3 py-2">
                    <IconClose v-if="!i.value" class="w-8"></IconClose>
                    <IconCheck v-else class="w-8"></IconCheck>
                </div>

                <div class="w-full pl-6">
                    <p class="text-xl font-semibold" :class="{'text-green': i.value}">{{ i.name }}</p>
                    <p class="text-sm font-light text-graywhite/60" :class="{'text-green/50': i.value}">{{ i.desc }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
