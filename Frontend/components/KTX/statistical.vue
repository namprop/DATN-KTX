<template>
  <div
    v-if="!loading"
    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8"
  >
    <!-- Tổng học sinh -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-cyan-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Tổng học sinh</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.total_students }}/{{ dashboardData.total_schoolstudents }}
          </h3>
          <p class="text-green-500 text-xs mt-2">Học sinh đang trong KTX</p>
        </div>
        <div class="bg-cyan-100 p-3 rounded-lg">
          <i class="fa-solid fa-user-graduate text-cyan-600 text-2xl"></i>
        </div>
      </div>
    </div>

        <!-- Tổng học sinh  -->
    <!-- <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-cyan-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Học sinh không hoạt động</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.total_students_inactive }}/{{ dashboardData.total_students }}
          </h3>
          <p class="text-green-500 text-xs mt-2">Học sinh đang trong KTX</p>
        </div>
        <div class="bg-cyan-100 p-3 rounded-lg">
          <i class="fa-solid fa-user-graduate text-cyan-600 text-2xl"></i>
        </div>
      </div>
    </div> -->

    <!-- Tổng nhân viên -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Nhân viên</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.total_staff }}
          </h3>
          <p class="text-purple-500 text-xs mt-2">Bao gồm quản trị & hỗ trợ</p>
        </div>
        <div class="bg-purple-100 p-3 rounded-lg">
          <i class="fa-solid fa-user-tie text-purple-600 text-2xl"></i>
        </div>
      </div>
    </div>

    <!-- Phòng đang sử dụng -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Phòng đang sử dụng</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.used_rooms }}/{{ dashboardData.total_rooms }}
          </h3>
          <p class="text-blue-500 text-xs mt-2">
            {{ dashboardData.room_usage_rate }}% công suất
          </p>
        </div>
        <div class="bg-blue-100 p-3 rounded-lg">
          <i class="fa-solid fa-door-open text-blue-600 text-2xl"></i>
        </div>
      </div>
    </div>

    <!-- Hợp đồng đang hoạt động -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-indigo-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Hợp đồng hoạt động</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.active_contracts }}
          </h3>
          <p class="text-indigo-500 text-xs mt-2">Active contracts</p>
        </div>
        <div class="bg-indigo-100 p-3 rounded-lg">
          <i class="fa-solid fa-file-contract text-indigo-600 text-2xl"></i>
        </div>
      </div>
    </div>

    <!-- Đã thanh toán -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Đã thanh toán tháng này</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.paid_this_month }}
          </h3>
          <p class="text-green-500 text-xs mt-2">75% học sinh</p>
        </div>
        <div class="bg-green-100 p-3 rounded-lg">
          <i class="fa-solid fa-money-bill-wave text-green-600 text-2xl"></i>
        </div>
      </div>
    </div>

    <!-- Chờ thanh toán -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Chờ thanh toán</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.pending_payments }}
          </h3>
          <p class="text-yellow-600 text-xs mt-2">Cần xử lý</p>
        </div>
        <div class="bg-yellow-100 p-3 rounded-lg">
          <i class="fa-solid fa-clock text-yellow-600 text-2xl"></i>
        </div>
      </div>
    </div>

    <!-- Cơ sở vật chất hỏng -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-gray-500 text-sm mb-1">Thiết bị hỏng</p>
          <h3 class="text-3xl font-bold text-gray-800">
            {{ dashboardData.broken_facilities }}
          </h3>
          <p class="text-red-500 text-xs mt-2">Cần bảo trì</p>
        </div>
        <div class="bg-red-100 p-3 rounded-lg">
          <i class="fa-solid fa-screwdriver-wrench text-red-600 text-2xl"></i>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="text-center py-10 text-gray-500">Đang tải dữ liệu...</div>
</template>

<script setup>
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();



// Biến reactive chứa dữ liệu từ backend
const dashboardData = ref({
  total_students: 0,
  total_schoolstudents: 0,
  total_students_inactive: 0,
  total_staff: 0,
  used_rooms: 0,
  total_rooms: 0,
  room_usage_rate: 0,
  active_contracts: 0,
  paid_this_month: 0,
  pending_payments: 0,
  broken_facilities: 0,
});

const loading = ref(true);

// Gọi API lấy dữ liệu dashboard
const fetchDashboardData = async () => {
  try {
    const res = await api.get("/admin/dashboard");
    if (res.data) {
      dashboardData.value = res.data;
    }
  } catch (error) {
    console.error("Lỗi khi tải dữ liệu dashboard:", error);
  } finally {
    loading.value = false;
  }
};

// Gọi khi component mount
onMounted(() => {
  fetchDashboardData();
});
</script>
