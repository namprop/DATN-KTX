<template>
  <div
    class="container mx-auto grid grid-cols-2 mt-4 pb-4 border-b border-neutral-300 lg:grid-cols-4 md:grid-cols-4 gap-x-2 gap-y-10"
  >
    <div v-for="(product, index) in products" :key="index" class="text-center">
      <NuxtLink class="hover:underline duration-500 underline-offset-4" href="">
        <div class="relative w-full max-w-[240px] aspect-[3/4] mx-auto">
          <!-- badges -->
          <div class="absolute top-2 left-2 space-x-1 flex flex-wrap">
            <span
              v-for="(badges, index) in product.badges"
              :key="index"
              class="bg-black text-white text-xs font-medium px-2 py-1"
              >{{ badges }}</span
            >
          </div>
          <img
            :src="`/img/${product.image}`"
            alt="item"
            class="w-full max-w-[240px] h-full object-cover rounded"
          />
        </div>
        <p class="mt-4 text-sm font-medium">{{ product.name }}</p>
      </NuxtLink>
      <p class="text-sm mt-1 font-semibold">{{ product.price }} d</p>
    </div>
  </div>
</template>

<script setup>
defineProps({
  products: {
    type: Array,
    required: true,
  },
});
</script>
