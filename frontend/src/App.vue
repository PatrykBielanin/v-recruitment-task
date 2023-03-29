<script setup>
  import CalculatorApp from './components/CalculatorApp.vue'
</script>

<template>
  <div class="flex flex-col overflow-hidden bg-background/50 justify-center relative min-h-screen">
    <img src="@/assets/images/beams.png" alt="Background Image" class="z-[-99] absolute top-1/2 left-1/2 max-w-none -translate-x-1/2 -translate-y-1/2" />

    <main>
      <CalculatorApp></CalculatorApp>
    </main>
  </div>
</template>
