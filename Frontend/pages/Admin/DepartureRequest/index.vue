<template>
  <div class="bg-gray-50">
    <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
      Quản lý đơn rời ký túc xá
    </h3>

    <div class="p-4 md:p-8">
      <!-- Bộ lọc + tìm kiếm -->
      <KTXFillterAndSearchRoom
        @search="handleSearch"
        @filterChange="handleFilterChange"
        @openForm="openAddForm"
      />

      <!-- Form thêm/sửa đơn -->
      <FormAddAndEdit
        :fields="departureRequestFields"
        :show="openForm"
        :title="isEdit ? 'Cập nhật đơn rời KTX' : 'Thêm đơn rời KTX mới'"
        v-model="createNewRequest"
        :mess="formErrors"
        @close="openForm = false"
        @submit="handleSubmitRequest"
      />

      <!-- Skeleton loading -->
      <div
        v-if="isLoading"
        class="bg-white rounded-lg shadow-sm overflow-hidden"
      >
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead
              class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white"
            >
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold">STT</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tên sinh viên
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Mã sinh viên
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Ngày yêu cầu
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">Lý do</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Trạng thái
                </th>
                <th class="px-6 py-4 text-center text-sm font-semibold">
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="i in 6" :key="i" class="hover:bg-gray-50">
                <td class="px-6 py-4">
                  <div class="h-4 bg-gray-200 rounded w-8 animate-pulse"></div>
                </td>
                <td class="px-6 py-4">
                  <div class="h-4 bg-gray-200 rounded w-32 animate-pulse"></div>
                </td>
                <td class="px-6 py-4">
                  <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                </td>
                <td class="px-6 py-4">
                  <div class="h-4 bg-gray-200 rounded w-28 animate-pulse"></div>
                </td>
                <td class="px-6 py-4">
                  <div class="h-4 bg-gray-200 rounded w-40 animate-pulse"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Bảng dữ liệu -->
      <div v-else class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead
              class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white"
            >
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold">STT</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tên sinh viên
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Mã sinh viên
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Ngày yêu cầu
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">Lý do</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Trạng thái
                </th>
                <th class="px-6 py-4 text-center text-sm font-semibold">
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr
                v-for="(item, index) in dataRequests"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                  {{ item.student?.full_name }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.student?.student_code }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.request_date }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.reason }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-yellow-100 text-yellow-700':
                        item.status === 'Pending',
                      'bg-green-100 text-green-700': item.status === 'Approved',
                      'bg-red-100 text-red-700': item.status === 'Rejected',
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                  >
                    <i
                      :class="{
                        'fa-solid fa-hourglass-half': item.status === 'Pending',
                        'fa-solid fa-check-circle': item.status === 'Approved',
                        'fa-solid fa-xmark-circle': item.status === 'Rejected',
                      }"
                    ></i>
                    {{
                      item.status === "Pending"
                        ? "Đang chờ"
                        : item.status === "Approved"
                        ? "Đã duyệt"
                        : "Từ chối"
                    }}
                  </span>
                </td>

                <!-- <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="handleEditRequest(item)"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button
                      @click="handleDeleteRequest(item.id)"
                      class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                      title="Xóa"
                    >
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td> -->

                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <!-- Nút Duyệt -->
                    <button
                      v-if="item.status === 'Pending'"
                      @click="updateStatus(item.id, 'Approved')"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Duyệt đơn"
                    >
                      <i class="fa-solid fa-check"></i>
                    </button>

                    <!-- Nút Từ chối -->
                    <button
                      v-if="item.status === 'Pending'"
                      @click="updateStatus(item.id, 'Rejected')"
                      class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
                      title="Từ chối đơn"
                    >
                      <i class="fa-solid fa-xmark"></i>
                    </button>

                    <!-- Nút chỉnh sửa -->
                    <button
                      @click="handleEditRequest(item)"
                      class="bg-yellow-50 text-yellow-600 p-2 rounded-lg hover:bg-yellow-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-pen"></i>
                    </button>

                    <!-- Nút xóa -->
                    <button
                      @click="handleDeleteRequest(item.id)"
                      class="bg-gray-50 text-gray-600 p-2 rounded-lg hover:bg-gray-100 transition"
                      title="Xóa"
                    >
                      <i class="fa-solid fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <Paginate
        v-if="pagination.total > pagination.per_page"
        :current-page="pagination.current_page"
        :total="pagination.total"
        :last-page="pagination.last_page"
        @change="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "dashboard", middleware: "auth" });
import { ref, onMounted, toRaw, computed } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataRequests = ref([]);
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewRequest = ref({});
const openForm = ref(false);
const isEdit = ref(false);
const filters = ref({
  status: "all",
});

const fetchRequests = async (keyword = "", page = 1) => {
  try {
    isLoading.value = true;
    const response = await api.get("/admin/departure-request", {
      params: {
        search: keyword,
        page,
        status: filters.value.status !== "all" ? filters.value.status : "",
      },
    });
    dataRequests.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error("Lỗi tải danh sách đơn:", error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => fetchRequests());

// Mở form thêm mới
const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewRequest.value = {};
};

// Chỉnh sửa đơn
const handleEditRequest = (request) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};
  createNewRequest.value = { ...request };
};

// Gửi form thêm / sửa
// const handleSubmitRequest = async (datas) => {
//   const data = toRaw(datas);
//   formErrors.value = {};

//   try {
//     if (isEdit.value) {
//       await api.put(`/admin/departure-request/${data.id}`, data);
//       alert("Cập nhật đơn thành công!");
//     } else {
//       await api.post("/admin/departure-request", data);
//       alert("Thêm đơn thành công!");
//     }
//     openForm.value = false;
//     createNewRequest.value = {};
//     await fetchRequests(searchKeyword.value);
//   } catch (error) {
//     if (error.response?.status === 422) {
//       formErrors.value = error.response.data.errors;
//     } else {
//       console.error("Lỗi hệ thống:", error);
//     }
//   }
// };

const updateStatus = async (id, status) => {
  const confirmText =
    status === "Approved"
      ? "Bạn có chắc muốn DUYỆT đơn này không?"
      : "Bạn có chắc muốn TỪ CHỐI đơn này không?";

  if (!confirm(confirmText)) return;

  try {
    const res = await api.put(`/admin/departure-request/${id}`, { status });

    alert(res.data.message || "Cập nhật trạng thái thành công!");
    await fetchRequests(searchKeyword.value);
  } catch (error) {
    if (error.response?.data?.message) {
      alert(error.response.data.message);
    } else {
      console.error("Lỗi cập nhật trạng thái:", error);
    }
  }
};

// Xóa đơn
const handleDeleteRequest = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa đơn này không?")) return;
  try {
    await api.delete(`/admin/departure-request/${id}`);
    await fetchRequests();
    alert("Xóa đơn thành công");
  } catch (error) {
    console.error("Lỗi khi xóa đơn:", error);
  }
};

// Bộ lọc & tìm kiếm
const handleFilterChange = (newFilters) => {
  filters.value = newFilters;
  fetchRequests(searchKeyword.value, 1);
};
const handleSearch = (keyword) => {
  searchKeyword.value = keyword;
  fetchRequests(keyword);
};
const handlePageChange = (page) => {
  fetchRequests(searchKeyword.value, page);
};

// Form fields
const departureRequestFields = computed(() => ({
  personal: {
    title: "Thông tin đơn rời KTX",
    icon: "fas fa-door-open",
    fields: [
      {
        key: "status",
        label: "Trạng thái",
        type: "select",
        icon: "fas fa-info-circle",
        options: [
          { value: "Pending", text: "Đang chờ" },
          { value: "Approved", text: "Đã duyệt" },
          { value: "Rejected", text: "Từ chối" },
        ],
      },
    ],
  },
}));
</script>
