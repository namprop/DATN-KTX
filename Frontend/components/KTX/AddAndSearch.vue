<template>
  <div class="p-6 border-b border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
      <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
        {{ title }}
      </h3>
      <div class="flex flex-col sm:flex-row gap-3">
        <!-- <select
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none bg-white min-w-[160px]"
        >
          <option value="all">Tất cả loại phòng</option>
          <option value="Male Dormitory">Phòng Nam</option>
          <option value="Female Dormitory">Phòng Nữ</option>
        </select> -->
        <input
          v-model="keyword"
          @input="emit('search', keyword)"
          type="text"
          :placeholder="placeholderSearch"
          class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        />
        <button
          @click="emit('openForm')"
          class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg hover:from-cyan-600 hover:to-blue-600 transition"
        >
          {{ titleAdd }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
const keyword = ref("");
const emit = defineEmits(["search", "openForm"]);
defineProps({
  title: String,
  titleAdd: String,
  placeholderSearch: String,
});
</script>
