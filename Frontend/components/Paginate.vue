<template>
  <div class="w-full flex flex-col items-center mt-6 space-y-3">
    <!-- Hiển thị số lượng phòng -->
    <div class="text-gray-700 text-sm">
      Tổng có: {{ total }}
    </div>

    <!-- Phân trang -->
    <div class="flex flex-wrap gap-2">
      <button
        class="border px-3 py-1 rounded hover:bg-black hover:text-white disabled:opacity-40"
        :disabled="currentPage === 1"
        @click="$emit('change', currentPage - 1)"
      >
        &laquo;
      </button>

      <button
        v-for="page in lastPage"
        :key="page"
        @click="$emit('change', page)"
        :class="[
          'border px-3 py-1 rounded transition',
          page === currentPage
            ? 'bg-black text-white font-medium'
            : 'hover:bg-black hover:text-white'
        ]"
      >
        {{ page }}
      </button>

      <button
        class="border px-3 py-1 rounded hover:bg-black hover:text-white disabled:opacity-40"
        :disabled="currentPage === lastPage"
        @click="$emit('change', currentPage + 1)"
      >
        &raquo;
      </button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  currentPage: Number,
  lastPage: Number,
  total: Number // tổng số phòng
})

defineEmits(['change'])

// Tính tổng số phòng hiển thị đến hiện tại (ví dụ: trang 2, mỗi trang 10 => hiển thị 20 phòng)
const totalShown = computed(() => {
  const shown = props.currentPage * props.perPage
  return shown > props.totalRooms ? props.totalRooms : shown
})
</script>
