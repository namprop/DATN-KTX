<template>
  <div v-if="isLoading" class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50">
        <tr>
          <th
            v-for="title in titleTable"
            :key="title"
            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
          >
            {{ title }}
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <!-- Tạo 6 dòng skeleton -->
        <tr v-for="i in 6" :key="i" class="hover:bg-gray-50">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="h-4 bg-gray-200 rounded w-8 animate-pulse"></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
              <div
                class="w-10 h-10 rounded-full bg-gray-200 animate-pulse"
              ></div>
              <div class="ml-3 flex-1">
                <div
                  class="h-4 bg-gray-200 rounded w-32 animate-pulse mb-2"
                ></div>
                <div class="h-3 bg-gray-100 rounded w-40 animate-pulse"></div>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="h-4 bg-gray-200 rounded w-28 animate-pulse"></div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex gap-3">
              <div class="h-4 bg-gray-200 rounded w-10 animate-pulse"></div>
              <div class="h-4 bg-gray-200 rounded w-10 animate-pulse"></div>
              <div class="h-4 bg-gray-200 rounded w-10 animate-pulse"></div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
  titleTable: {
    type: Array,
    required: true,
  },
});
</script>
