<template>
  <div class="bg-gray-50">
    <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
      Quản lý thiết bị
    </h3>

    <div class="p-4 md:p-8">
      <!-- Statistics Cards - Hàng ngang -->
      <!-- Filters and Actions -->
      <KTXSearAndFillFacility
        @search="handleSearch"
        @filterChange="handleFilterChange"
        @openForm="openAddForm"
      />

      <FormAddAndEdit
        :fields="AccountInformationfields"
        :show="openForm"
        :title="
          isEdit ? 'Cập nhật thông tin thiết bị' : 'Thêm người thiết bị mới'
        "
        v-model="createNewFacility"
        :mess="formErrors"
        @close="openForm = false"
        @submit="handleSubmitFacility"
      />

      <!-- Skeleton Loader -->

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
                  Số phòng
                </th>
                <!-- <th class="px-6 py-4 text-left text-sm font-semibold">Tầng</th> -->
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Loại phòng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Sức chứa
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Đang ở
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Giá thuê/tháng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Trạng thái
                </th>
                <th class="px-6 py-4 text-center text-sm font-semibold">
                  Thao tác
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
                      <div
                        class="h-3 bg-gray-100 rounded w-40 animate-pulse"
                      ></div>
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
                    <div
                      class="h-4 bg-gray-200 rounded w-10 animate-pulse"
                    ></div>
                    <div
                      class="h-4 bg-gray-200 rounded w-10 animate-pulse"
                    ></div>
                    <div
                      class="h-4 bg-gray-200 rounded w-10 animate-pulse"
                    ></div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Table with Details -->
      <div v-else class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead
              class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white"
            >
              <tr>
                <th class="px-6 py-4 text-left text-sm font-semibold">STT</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tên Thiết bị
                </th>
                <!-- <th class="px-6 py-4 text-left text-sm font-semibold">Tầng</th> -->
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Mã Thiết bị
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Số lượng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Thuộc phòng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Trạng thái
                </th>
                <th class="px-6 py-4 text-center text-sm font-semibold">
                  Thao tác
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <!-- Row 1 -->
              <tr
                v-for="(item, index) in dataFacility"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div
                      class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-lg flex items-center justify-center"
                    >
                      <i class="fas fa-boxes text-white text-xs"></i>
                    </div>
                    <span class="font-medium text-gray-800">{{
                      item.facility_name
                    }}</span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-800">
                      {{ item.facility_code }}</span
                    >
                  </div>
                </td>

                <td class="px-6 py-4">
                  <span
                    class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium"
                  >
                    <i class="fas fa-layer-group text-xs"></i>
                    {{ item.quantity }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <span class="font-semibold text-gray-800">
                      Phòng {{ item.room?.room_code }}</span
                    >
                  </div>
                </td>
                <!-- <td class="px-6 py-4 text-sm text-gray-700">Tầng 1</td> -->

                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-green-100 text-green-700': item.status === 'good',
                      'bg-red-100 text-red-700': item.status === 'broken',
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                  >
                    <i
                      v-if="item.status === 'Available'"
                      class="fa-solid fa-circle-check"
                    ></i>
                    <i
                      v-else-if="item.status === 'Full'"
                      class="fa-solid fa-circle-xmark"
                    ></i>
                    <i
                      v-else-if="item.status === 'Maintenance'"
                      class="fa-solid fa-screwdriver-wrench"
                    ></i>

                    {{
                      item.status === "good"
                        ? "Tốt"
                        : item.status === "broken"
                        ? "Hỏng"
                        : "Bảo trì"
                    }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <!-- <button
                      class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition"
                      title="Xem chi tiết"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button> -->
                    <button
                      @click="handleEditFacility(item)"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button
                      @click="handleDeleteFacility(item.id)"
                      class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition"
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

        <!-- Pagination -->
      </div>
      <Paginate
        v-if="pagination.total > pagination.per_page"
        :current-page="pagination.current_page"
        :last-page="pagination.last_page"
        :total="pagination.total"
        @change="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "dashboard", middleware: "auth" });
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataFacility = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewFacility = ref({});
const openForm = ref(false);
const isEdit = ref(false);
const filters = ref({
  status: "all",
  description: "all",
});

const fetchFacility = async (keyword = "", page = 1) => {
  try {
    isLoading.value = true;

    // Tạo params động dựa trên filter
    const params = {
      search: keyword,
      page,
    };

    if (filters.value.status && filters.value.status !== "all") {
      params.status = filters.value.status;
    }
    if (filters.value.room_code) {
      params.room_code = filters.value.room_code;
    }

    const response = await api.get("/admin/facility", { params });

    dataFacility.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error("Lỗi hệ thống:", error);
  } finally {
    isLoading.value = false;
  }
};
onMounted(() => {
  fetchFacility();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewFacility.value = {}; // reset form
};

const handleEditFacility = (Facility) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewFacility.value = {
    ...Facility,
  };
};

const handleSubmitFacility = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/facility/${data.id}`, data);
      alert("Cập nhật thiết bị thành công!");
    } else {
      await api.post("/admin/facility", data);
      alert("Thêm thiết bị thành công!");
    }

    openForm.value = false;
    createNewFacility.value = {};
    await fetchFacility(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteFacility = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa thiết bị này không?");
  if (!confirmed) return; // nếu thiết bị bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/facility/${id}`);
    await fetchFacility(); // gọi lại danh sách sau khi xóa
    alert("Xóa thiết bị thành công");
  } catch (error) {
    console.error("Lỗi khi xóa thiết bị:", error);
    alert("Đã xảy ra lỗi khi xóa thiết bị!");
  }
};

const handleFilterChange = (newFilters) => {
  filters.value = newFilters;
  fetchFacility(searchKeyword.value, 1); // gọi lại API từ đầu trang
};

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;
  fetchFacility(keyword);
};

const handlePageChange = (page) => {
  fetchFacility(searchKeyword.value, page);
};

const AccountInformationfields = computed(() => {
  const baseFields = {
    personal: {
      title: "Thông tin thiết bị",
      icon: "fas fa-boxes",
      fields: [
        {
          key: "facility_name",
          label: "Tên thiết bị",
          type: "text",
          placeholder: "VD : Quạt máy",
          icon: "fas fa-boxes",
        },

        {
          key: "facility_code",
          label: "Mã thiết bị",
          type: "text",
          placeholder: "VD : TB001",
          icon: "fas fa-barcode",
        },
        {
          key: "quantity",
          label: "Số lượng",
          type: "text",
          placeholder: "VD : 10",
          icon: "fas fa-layer-group",
        },
        {
          key: "room_code",
          label: "Thuộc phòng",
          type: "text",
          placeholder: "VD : A101",
          icon: "fas fa-door-closed",
        },

        {
          key: "status",
          label: "Trạng thái thiết bị",
          type: "select",
          icon: "fas fa-info-circle",
          options: [
            { value: "good", text: "Tốt" },
            { value: "broken", text: "Hỏng" },
          ],
        },
      ],
    },
  };

  // 🔥 Nếu đang edit => chỉ hiển thị phần personal
  if (isEdit.value) {
    return [{ personal: baseFields.personal }];
  }

  // Nếu thêm mới => hiển thị cả 2 phần
  return [baseFields];
});
</script>
