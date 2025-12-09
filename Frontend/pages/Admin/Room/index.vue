<template>
  <div class="bg-gray-50">
    <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Quản lý phòng</h3>

    <div class="p-4 md:p-8">
      <!-- Statistics Cards - Hàng ngang -->
      <!-- Filters and Actions -->
      <KTXFillterAndSearchRoom
        @search="handleSearch"
        @filterChange="handleFilterChange"
        @openForm="openAddForm"
      />

      <FormAddAndEdit
        :fields="AccountInformationfields"
        :show="openForm"
        :title="isEdit ? 'Cập nhật thông tin học sinh' : 'Thêm người dùng mới'"
        v-model="createNewRoom"
        :mess="formErrors"
        @close="openForm = false"
        @submit="handleSubmitRoom"
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
            <tbody class="divide-y divide-gray-200">
              <!-- Row 1 -->
              <tr
                v-for="(item, index) in dataRoom"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <div
                      class="bg-cyan-100 text-cyan-700 w-10 h-10 rounded-lg flex items-center justify-center font-bold"
                    >
                      {{ item.room_code }}
                    </div>
                    <span class="font-semibold text-gray-800"
                      >Phòng {{ item.room_code }}</span
                    >
                  </div>
                </td>
                <!-- <td class="px-6 py-4 text-sm text-gray-700">Tầng 1</td> -->
                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-blue-100 text-blue-700':
                        item.description === 'Male Dormitory',
                      'bg-pink-100 text-pink-700':
                        item.description === 'Female Dormitory',
                      'bg-gray-100 text-gray-600': ![
                        'Male Dormitory',
                        'Female Dormitory',
                      ].includes(item.description),
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                  >
                    <i
                      v-if="item.description === 'Male Dormitory'"
                      class="fa-solid fa-mars text-blue-500"
                    ></i>
                    <i
                      v-else-if="item.description === 'Female Dormitory'"
                      class="fa-solid fa-venus text-pink-500"
                    ></i>
                    <i
                      v-else
                      class="fa-solid fa-circle-exclamation text-gray-500"
                    ></i>
                    {{
                      item.description === "Male Dormitory"
                        ? "Nam"
                        : item.description === "Female Dormitory"
                        ? "Nữ"
                        : "Chưa điền thông tin"
                    }}
                  </span>
                </td>

                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.capacity }} người
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-center gap-2">
                    <!-- Hiển thị số lượng -->
                    <span class="font-semibold text-gray-800">
                      {{ item.students_count }}/{{ item.capacity }}
                    </span>

                    <!-- Thanh tiến trình -->
                    <div
                      class="w-24 bg-gray-200 rounded-full h-2 overflow-hidden"
                    >
                      <div
                        class="h-2 rounded-full transition-all duration-300"
                        :class="{
                          'bg-green-500':
                            item.students_count / item.capacity < 0.5,
                          'bg-yellow-500':
                            item.students_count / item.capacity >= 0.5 &&
                            item.students_count / item.capacity < 0.8,
                          'bg-red-500':
                            item.students_count / item.capacity >= 0.8,
                        }"
                        :style="{
                          width:
                            (item.students_count / item.capacity) * 100 + '%',
                        }"
                      ></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm font-bold text-cyan-600">
                  {{
                    new Intl.NumberFormat("vi-VN", {
                      style: "currency",
                      currency: "VND",
                    }).format(item.price)
                  }}
                </td>
                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-green-100 text-green-700':
                        item.status === 'Available',
                      'bg-red-100 text-red-700': item.status === 'Full',
                      'bg-yellow-100 text-yellow-700':
                        item.status === 'Maintenance',
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
                      item.status === "Available"
                        ? "Còn trống"
                        : item.status === "Full"
                        ? "Đầy"
                        : "Bảo trì"
                    }}
                  </span>
                </td>

                <td class="px-6 py-4">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="handleViewRoom(item.id)"
                      class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition"
                      title="Xem chi tiết"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button>
                    <button
                      @click="handleEditRoom(item)"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button
                      @click="handleDeleteRoom(item.id)"
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

      <KTXRoomDetail
        :show="showDetail"
        :room="selectedRoom"
        @close="showDetail = false"
      />
    </div>
  </div>
</template>

<script setup>
definePageMeta({ middleware: "auth", layout: "dashboard" });
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataRoom = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewRoom = ref({});
const openForm = ref(false);
const isEdit = ref(false);
const filters = ref({
  status: "all",
  description: "all",
});

const fetchRoom = async (keyword = "", page = 1) => {
  try {
    isLoading.value = true;
    const response = await api.get("/admin/room", {
      params: {
        search: keyword,
        status: filters.value.status !== "all" ? filters.value.status : "",
        description:
          filters.value.description !== "all" ? filters.value.description : "",
        page,
      },
    });
    dataRoom.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    if (error.response && error.response.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  } finally {
    isLoading.value = false;
  }
};
onMounted(() => {
  fetchRoom();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewRoom.value = {}; // reset form
};

const handleEditRoom = (Room) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewRoom.value = {
    ...Room,
  };
};

const handleSubmitRoom = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/room/${data.id}`, data);
      alert("Cập nhật người dùng thành công!");
    } else {
      await api.post("/admin/room", data);
      alert("Thêm người dùng thành công!");
    }

    openForm.value = false;
    createNewRoom.value = {};
    await fetchRoom(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteRoom = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa người dùng này không?");
  if (!confirmed) return; // nếu người dùng bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/room/${id}`);
    await fetchRoom(); // gọi lại danh sách sau khi xóa
    alert("Xóa người dùng thành công");
  } catch (error) {
    console.error("Lỗi khi xóa người dùng:", error);
    alert("Đã xảy ra lỗi khi xóa người dùng!");
  }
};

const handleViewRoom = async (roomId) => {
  try {
    const response = await api.get(`/admin/room/${roomId}`);
    selectedRoom.value = response.data.data;
    showDetail.value = true;
  } catch (error) {
    console.error("Lỗi khi lấy chi tiết phòng:", error);
    alert("Không thể lấy thông tin chi tiết phòng!");
  }
};

const selectedRoom = ref({});
const showDetail = ref(false);

const handleFilterChange = (newFilters) => {
  filters.value = newFilters;
  fetchRoom(searchKeyword.value, 1); // gọi lại API từ đầu trang
};

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;
  fetchRoom(keyword);
};

const handlePageChange = (page) => {
  fetchRoom(searchKeyword.value, page);
};

const AccountInformationfields = computed(() => {
  const baseFields = {
    personal: {
      title: "Thông tin cá nhân",
      icon: "fas fa-id-card",
      fields: [
        {
          key: "room_code",
          label: "Só phòng",
          type: "text",
          placeholder: "VD : A101",
          icon: "fas fa-door-open",
        },

        {
          key: "capacity",
          label: "Sức chứa",
          type: "text",
          placeholder: "VD : 4",
          icon: "fas fa-users",
        },
        {
          key: "price",
          label: "Giá thuê (VND)",
          type: "text",
          placeholder: "VD : 2000000",
          icon: "fas fa-money-bill-wave",
        },
        {
          key: "description",
          label: "Khu vực dành cho",
          type: "select",
          icon: "fas fa-venus-mars",
          options: [
            { value: "", text: "Chọn giới tính" },
            { value: "Male Dormitory", text: "Nam" },
            { value: "Female Dormitory", text: "Nữ" },
          ],
        },

        {
          key: "status",
          label: "Trạng thái phòng",
          type: "select",
          icon: "fas fa-info-circle",
          options: [
            { value: "Available", text: "Còn trống" },
            { value: "Full", text: "Đầy" },
            { value: "Maintenance", text: "Bảo trì" },
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
