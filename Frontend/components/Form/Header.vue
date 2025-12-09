<template>
  <div
    class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between"
  >
    <div class="flex items-center space-x-3">
      <div class="bg-blue-100 p-2 rounded-lg">
        <i class="fas fa-user-plus text-blue-600 text-lg"></i>
      </div>
      <div>
        <h2 class="text-xl font-bold text-gray-800">{{ nameForm }}</h2>
        <p class="text-xs text-gray-500">Điền thông tin để tạo tài khoản</p>
      </div>
    </div>
    <button
    @click="$emit('closes')"
     class="text-gray-400 hover:text-gray-600 transition-colors">
      <i class="fas fa-times text-xl"></i>
    </button>
  </div>
</template>

<script setup>
const props = defineProps({
    nameForm :{
        type:String,
    }
});

const emit =defineEmits(["closes"])

</script>
