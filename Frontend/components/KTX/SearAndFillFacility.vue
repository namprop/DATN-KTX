<template>
  <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
      <!-- LEFT: Search + Filters -->
      <div class="flex flex-col sm:flex-row gap-3 flex-1">
        <!-- Search -->
        <div class="relative flex-1">
          <i
            class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"
          ></i>
          <input
            v-model="keyword"
            @input="emitSearch"
            type="text"
            placeholder="Tìm kiếm theo tên hoặc mã thiết bị"
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none"
          />
        </div>

        <!-- Filter by Status -->
        <select
          v-model="filters.status"
          @change="emitFilters"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none bg-white min-w-[160px]"
        >
          <option value="all">Tất cả trạng thái</option>
          <option value="good">Tốt</option>
          <option value="broken">Hỏng</option>
        </select>

        <!-- Filter by Room -->
        <!-- <input
          v-model="filters.room_code"
          @input="emitFilters"
          type="text"
          placeholder="Tìm theo phòng"
          class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none min-w-[160px]"
        /> -->
      </div>

      <!-- RIGHT: Add Button -->
      <button
        @click="emit('openForm')"
        class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2.5 rounded-lg hover:from-cyan-600 hover:to-blue-600 transition flex items-center justify-center gap-2 font-medium shadow-md self-start lg:self-auto"
      >
        <i class="fa-solid fa-plus"></i>
        Thêm Thiết Bị Mới
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const keyword = ref("");
const filters = ref({
  status: "all",
  room_code: "",
});

const emit = defineEmits(["search", "openForm", "filterChange"]);

const emitSearch = () => {
  emit("search", keyword.value);
};

const emitFilters = () => {
  emit("filterChange", { ...filters.value });
};
</script>
