<script setup>
definePageMeta({
  middleware: "auth",
  layout: "dashboard",
});
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataContract = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewContract = ref({});
const openForm = ref(false);
const isEdit = ref(false);
const filters = ref({
  status: "all",
  description: "all",
});

const fetchContract = async (keyword = "", page = 1) => {
  try {
    isLoading.value = true;
    const response = await api.get("/admin/contract", {
      params: {
        search: keyword,
        status: filters.value.status !== "all" ? filters.value.status : "",
        description:
          filters.value.description !== "all" ? filters.value.description : "",
        page,
      },
    });
    dataContract.value = response.data.data;
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
  fetchContract();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewContract.value = {}; // reset form
};

const handleEditContract = (Contract) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewContract.value = {
    ...Contract,
    student_code: Contract.student?.student_code,
    start_date: formatDate(Contract.start_date),
    end_date: formatDate(Contract.end_date),
  };
};

const handleSubmitContract = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/contract/${data.id}`, data);
      alert("Cập nhật hợp đồng thành công!");
    } else {
      await api.post("admin/contract", data);
      alert("Thêm hợp đồng thành công!");
    }

    openForm.value = false;
    createNewContract.value = {};
    await fetchContract(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteContract = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa hợp đồng này không?");
  if (!confirmed) return; // nếu hợp đồng bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/contract/${id}`);
    await fetchContract(); // gọi lại danh sách sau khi xóa
    alert("Xóa hợp đồng thành công");
  } catch (error) {
    console.error("Lỗi khi xóa hợp đồng:", error);
    alert("Đã xảy ra lỗi khi xóa hợp đồng!");
  }
};

const handleFilterChange = (newFilters) => {
  filters.value = newFilters;
  fetchContract(searchKeyword.value, 1); // gọi lại API từ đầu trang
};

let debounceTimer;

// 🔍 Khi người dùng tìm kiếm
const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchContract(searchKeyword.value);
  }, 500);
};

const handlePageChange = (page) => {
  fetchContract(searchKeyword.value, page);
};

const getInitials = (name) => {
  if (!name) return "??";
  const words = name.trim().split(" ");
  const lastTwoWords = words.slice(-2).map((w) => w[0].toUpperCase());
  return lastTwoWords.join("");
};

const formatDate = (dateStr) => {
  if (!dateStr) return "";
  const date = new Date(dateStr);
  if (isNaN(date)) return dateStr; // nếu dateStr không hợp lệ thì trả về nguyên gốc
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

const getRandomColor = () => {
  const colors = [
    "from-cyan-400 to-blue-500",
    "from-pink-400 to-rose-500",
    "from-emerald-400 to-green-500",
    "from-indigo-400 to-purple-500",
    "from-amber-400 to-orange-500",
    "from-sky-400 to-teal-500",
  ];
  return colors[Math.floor(Math.random() * colors.length)];
};

const AccountInformationfields = computed(() => {
  const baseFields = {
    personal: {
      title: "Thông tin phòng",
      icon: "fas fa-door-open",
      fields: [
        {
          key: "student_code",
          label: "Mã học sinh",
          type: "text",
          placeholder: "VD : HS001",
          icon: "fas fa-id-badge",
        },

        {
          key: "start_date",
          label: "Ngày bắt đầu",
          type: "text",
          placeholder: "VD : 01/05/2024",
          icon: "fas fa-calendar-plus",
        },
        {
          key: "end_date",
          label: "Ngày kết thúc",
          type: "text",
          placeholder: "VD : 01/11/2024",
          icon: "fas fa-calendar-minus",
        },
        {
          key: "status",
          label: "Trạng thái hợp đồng",
          type: "select",
          icon: "fas fa-info-circle",
          options: [
            { value: "Pending", text: "Chờ duyệt" },
            { value: "Approved", text: "Đã duyệt" },
            { value: "Active", text: "Đang hiệu lực" },
            { value: "Completed", text: "Hoàn thành" },
            { value: "Terminated", text: "Kết thúc sớm" },
            { value: "Rejected", text: "Từ chối" },
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
<template>
  <div class="bg-gray-50">
    <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">Quản lý phòng</h3>

    <div class="p-4 md:p-8">
      <!-- Statistics Cards - Hàng ngang -->
      <!-- Filters and Actions -->
      <KTXAddAndSearch
        title="Thêm hợp đồng mới"
        title-add="Thêm hợp đồng"
        placeholder-search="Tìm kiếm mã, học sinh..."
        @search="handleSearch"
        @filterChange="handleFilterChange"
        @openForm="openAddForm"
      />

      <FormAddAndEdit
        :fields="AccountInformationfields"
        :show="openForm"
        :title="isEdit ? 'Cập nhật thông tin hợp đồng' : 'Thêm hợp đồng'"
        v-model="createNewContract"
        :mess="formErrors"
        @close="openForm = false"
        @submit="handleSubmitContract"
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
                  Họ và tên học sinh
                </th>
                <!-- <th class="px-6 py-4 text-left text-sm font-semibold">Tầng</th> -->
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Mã học sinh
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  ID Phòng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Ngày bắt đầu
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Ngày kết thúc
                </th>
                <!-- <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tiền đặt cọc
                </th> -->
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
                v-for="(item, index) in dataContract"
                :key="item.id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div
                      class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm bg-gradient-to-r"
                      :class="getRandomColor()"
                    >
                      {{ getInitials(item.student?.full_name) }}
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900">
                        {{ item.student?.full_name }}
                      </p>
                      <p class="text-sm text-gray-500">
                        {{ item.studnet?.student_code }}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.student?.student_code }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ item.student?.room_id }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ formatDate(item.start_date) }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  {{ formatDate(item.end_date) }}
                </td>

                <!-- <td class="px-6 py-4 text-sm text-gray-700">Tầng 1</td> -->

                <!-- <td class="px-6 py-4 text-sm font-bold text-cyan-600">
                  {{
                    new Intl.NumberFormat("vi-VN", {
                      style: "currency",
                      currency: "VND",
                    }).format(item.deposit_amount)
                  }}
                </td> -->
                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-yellow-100 text-yellow-700':
                        item.status === 'Pending', // Chờ duyệt
                      'bg-blue-100 text-blue-700': item.status === 'Approved', // Đã duyệt, chờ bắt đầu
                      'bg-green-100 text-green-700': item.status === 'Active', // Đang hiệu lực
                      'bg-gray-100 text-gray-700': item.status === 'Completed', // Hết hạn bình thường
                      'bg-red-100 text-red-700': item.status === 'Terminated', // Kết thúc sớm
                      'bg-pink-100 text-pink-700': item.status === 'Rejected', // Bị từ chối
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                  >
                    <!-- Icon cho từng trạng thái -->
                    <i
                      v-if="item.status === 'Pending'"
                      class="fa-solid fa-hourglass-half"
                    ></i>
                    <i
                      v-else-if="item.status === 'Approved'"
                      class="fa-solid fa-circle-check"
                    ></i>
                    <i
                      v-else-if="item.status === 'Active'"
                      class="fa-solid fa-play-circle"
                    ></i>
                    <i
                      v-else-if="item.status === 'Completed'"
                      class="fa-solid fa-flag-checkered"
                    ></i>
                    <i
                      v-else-if="item.status === 'Terminated'"
                      class="fa-solid fa-ban"
                    ></i>
                    <i
                      v-else-if="item.status === 'Rejected'"
                      class="fa-solid fa-circle-xmark"
                    ></i>

                    <!-- Text hiển thị tiếng Việt -->
                    {{
                      item.status === "Pending"
                        ? "Chờ duyệt"
                        : item.status === "Approved"
                        ? "Đã duyệt"
                        : item.status === "Active"
                        ? "Đang hiệu lực"
                        : item.status === "Completed"
                        ? "Hoàn thành"
                        : item.status === "Terminated"
                        ? "Kết thúc sớm"
                        : item.status === "Rejected"
                        ? "Từ chối"
                        : "Không xác định"
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
                      @click="handleEditContract(item)"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button
                      @click="handleDeleteContract(item.id)"
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
        :total="pagination.total"
        :last-page="pagination.last_page"
        @change="handlePageChange"
      />
    </div>
  </div>
</template>
