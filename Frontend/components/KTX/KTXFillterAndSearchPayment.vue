<template>
  <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <div class="relative flex-1">
          <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="keyword"
            @input="emit('search', keyword)"
            type="text"
            placeholder="Tìm kiếm theo tên sinh viên, mã thanh toán..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none"
          />
        </div>

        <select v-model="filters.month" @change="emitFilter" class="px-4 py-2.5 border rounded-lg">
          <option value="all">Tất cả tháng</option>
          <option v-for="m in 12" :key="m" :value="m.toString().padStart(2, '0')">Tháng {{ m }}</option>
        </select>

        <select v-model="filters.year" @change="emitFilter" class="px-4 py-2.5 border rounded-lg">
          <option value="all">Tất cả năm</option>
          <option v-for="y in yearOptions" :key="y" :value="y">Năm {{ y }}</option>
        </select>

        <select v-model="filters.status" @change="emitFilter" class="px-4 py-2.5 border rounded-lg">
          <option value="all">Tất cả trạng thái</option>
          <option value="paid">Đã thanh toán</option>
          <option value="pending">Đang xử lý</option>
          <option value="unpaid">Chưa thanh toán</option>
        </select>
      </div>

      <button
        @click="emit('openForm')"
        class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2.5 rounded-lg hover:from-cyan-600 hover:to-blue-600 transition"
      >
        <i class="fa-solid fa-plus"></i> Thêm thanh toán mới
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";

const emit = defineEmits(["search", "openForm", "filterChange"]);

const keyword = ref("");
const filters = ref({
  month: "all",
  year: "all",
  status: "all",
});

const emitFilter = () => {
  emit("filterChange", { ...filters.value });
};

const currentYear = new Date().getFullYear();
const yearOptions = computed(() => {
  const years = [];
  for (let y = currentYear - 1; y <= currentYear + 1; y++) years.push(y.toString());
  return years;
});
</script>
