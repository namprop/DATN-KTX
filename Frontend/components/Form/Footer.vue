<template>
  <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4">
    <div class="flex flex-col sm:flex-row gap-2">
      <!-- <button
        @click="$emit('resets')"
        class="flex-1 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm flex items-center justify-center"
      >
        <i class="fas fa-redo mr-2 text-xs"></i>
        Làm mới
      </button> -->
      <button
        @click="$emit('closes')"
        class="flex-1 bg-gray-600 text-white px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm flex items-center justify-center"
      >
        <i class="fas fa-times mr-2 text-xs"></i>
        Hủy bỏ
      </button>
      <button
        type="submit"
        class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm flex items-center justify-center shadow-lg"
      >
        <i class="fas fa-save mr-2 text-xs"></i>
        Lưu 
      </button>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(["closes", "resets"]);
</script>
