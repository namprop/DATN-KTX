<template>
  <div class="bg-gray-50 min-h-screen">
    <h3 class="text-xl font-bold text-gray-800 mb-4 md:mb-0">
      Quản lý thanh toán
    </h3>

    <div class="p-4 md:p-8">
      <!-- Bộ lọc + tìm kiếm -->
      <KTXFillterAndSearchPayment
        @search="handleSearch"
        @filterChange="handleFilterChange"
        @openForm="openAddForm"
      />

      <div class="flex justify-end mb-4">
        <button
          @click="openUtilityPriceForm"
          class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-6 py-2.5 rounded-lg hover:from-cyan-600 hover:to-blue-600 transition flex items-center justify-center gap-2 font-medium shadow-md self-start lg:self-auto"
        >
          💡 Giá điện nước
        </button>
      </div>

      <!-- Form thêm/sửa -->
      <FormAddAndEdit
        :fields="AccountInformationfields"
        :show="openForm"
        :title="isEdit ? 'Cập nhật thanh toán' : 'Thêm thanh toán mới'"
        v-model="createNewPayment"
        :mess="formErrors"
        @close="openForm = false"
        @submit="handleSubmitPayment"
      />

      <FormEditStatusPayment
        :show="openEditStatusForm"
        :payment="selectedPayment"
        @close="openEditStatusForm = false"
        @submit="handleSubmitEditStatus"
      />

      <!-- Hiển thị loading -->
      <div
        v-if="isLoading"
        class="bg-white rounded-lg shadow-sm overflow-hidden"
      >
        <div class="p-8 text-center text-gray-500 animate-pulse">
          Đang tải dữ liệu thanh toán...
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
                  Họ và tên
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Mã thanh toán
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">Phòng</th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Điện (kWh)/ Phòng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Nước (m³) / Phòng
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tổng tiền (VNĐ)
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Nội dung thanh toán
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Trạng thái
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Ngày hết hạn
                </th>
                <th class="px-6 py-4 text-left text-sm font-semibold">
                  Tháng/Năm
                </th>
                <th class="px-6 py-4 text-center text-sm font-semibold">
                  Thao tác
                </th>
              </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
              <!-- Không có dữ liệu -->
              <tr
                v-if="dataPayment.length === 0"
                class="text-center text-gray-500"
              >
                <td colspan="10" class="py-6">Không có thông tin thanh toán</td>
              </tr>

              <!-- Dữ liệu thanh toán -->
              <tr
                v-else
                v-for="(item, index) in dataPayment"
                :key="item.payment_id"
                class="hover:bg-gray-50 transition"
              >
                <td class="px-6 py-4 text-sm text-gray-700">{{ index + 1 }}</td>

                <td class="px-6 py-4">
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
                      <p class="text-xs text-gray-500">
                        MSSV: {{ item.student?.student_code }}
                      </p>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                  {{ item.payment_code }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ item.room?.room_code }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ item.electricity_usage }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ item.water_usage }}
                </td>

                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                  {{ item.total_amount.toLocaleString("vi-VN") }}
                </td>
                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                  {{ item.description }}
                </td>

                <td class="px-6 py-4">
                  <span
                    :class="{
                      'bg-green-100 text-green-700':
                        item.payment_status === 'paid',
                      'bg-yellow-100 text-yellow-700':
                        item.payment_status === 'refund_pending',
                      'bg-red-100 text-red-700':
                        item.payment_status === 'unpaid',
                    }"
                    class="px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                  >
                    <i
                      v-if="item.payment_status === 'paid'"
                      class="fa-solid fa-circle-check"
                    ></i>
                    <i
                      v-else-if="item.payment_status === 'pending'"
                      class="fa-solid fa-clock"
                    ></i>
                    <i v-else class="fa-solid fa-circle-xmark"></i>

                    {{
                      item.payment_status === "paid"
                        ? "Đã thanh toán"
                        : item.payment_status === "unpaid"
                        ? "Chưa thanh toán"
                        : "Hoàn lại tiền cọc"
                    }}
                  </span>
                </td>

                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ formatDate(item.payment_date) }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-800">
                  {{ item.month }}/{{ item.year }}
                </td>

                <td class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <!-- <button
                      class="bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition"
                      title="Xem chi tiết"
                    >
                      <i class="fa-solid fa-eye"></i>
                    </button> -->
                    <button
                      @click="handleEditPayments(item)"
                      class="bg-green-50 text-green-600 p-2 rounded-lg hover:bg-green-100 transition"
                      title="Chỉnh sửa"
                    >
                      <i class="fa-solid fa-edit"></i>
                    </button>
                    <button
                      @click="handleDeletePayment(item.payment_id)"
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

        <!-- Phân trang -->
        <Paginate
          v-if="pagination.total > pagination.per_page"
          :current-page="pagination.current_page"
          :total="pagination.total"
          :last-page="pagination.last_page"
          @change="handlePageChange"
        />
      </div>
    </div>

    <div
      v-if="showUtilityForm"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
      @click.self="showUtilityForm = false"
    >
      <!-- Form chính -->
      <div class="bg-white p-6 rounded shadow-md w-[400px] relative z-10">
        <h2 class="text-xl font-semibold mb-4">Cập nhật giá điện nước</h2>

        <div class="space-y-4">
          <div>
            <label class="block font-medium">Giá điện (đ/kWh)</label>
            <input
              v-model="utilityForm.electricity_price"
              type="number"
              class="border p-2 w-full rounded"
            />
          </div>

          <div>
            <label class="block font-medium">Giá nước (đ/m³)</label>
            <input
              v-model="utilityForm.water_price"
              type="number"
              class="border p-2 w-full rounded"
            />
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
          <button
            class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
            @click="showUtilityForm = false"
          >
            Hủy
          </button>
          <button
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
            @click="updateUtilityPrice"
          >
            Lưu
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: "dashboard", middleware: "auth" });
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const showUtilityForm = ref(false);
const utilityForm = ref({
  id: null,
  electricity_price: 0,
  water_price: 0,
}); // Đổi từ [] thành {} vì đây là object

const openUtilityPriceForm = async () => {
  try {
    const res = await api.get("/admin/utilityprice");
    if (
      res.data.status &&
      Array.isArray(res.data.data) &&
      res.data.data.length > 0
    ) {
      const u = res.data.data[0];
      utilityForm.value = {
        id: u.id,
        electricity_price: u.electricity_price,
        water_price: u.water_price,
      };
    }
    showUtilityForm.value = true;
  } catch (error) {
    console.error("Lỗi khi lấy giá điện nước:", error);
  }
};

const updateUtilityPrice = async () => {
  try {
    const res = await api.put(
      `/admin/utilityprice/${utilityForm.value.id}`,
      utilityForm.value
    );
    if (res.data.status) {
      alert("Cập nhật giá điện nước thành công!");
      showUtilityForm.value = false;
    }
  } catch (error) {
    console.error("Lỗi khi cập nhật:", error);
    alert("Có lỗi xảy ra khi cập nhật!");
  }
};
const api = useAxios();

const dataPayment = ref([]);
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const openEditStatusForm = ref(false);
const selectedPayment = ref(null);

const createNewPayment = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const filters = ref({
  month: "all",
  year: "all",
  status: "all",
});

const roomOptions = ref([]);

// 📦 Lấy dữ liệu thanh toán
const fetchPayment = async (keyword = "", page = 1) => {
  try {
    isLoading.value = true;

    const response = await api.get("/admin/payment", {
      params: {
        search: keyword,
        month: filters.value.month !== "all" ? filters.value.month : "",
        year: filters.value.year !== "all" ? filters.value.year : "",
        status: filters.value.status !== "all" ? filters.value.status : "",
        page,
      },
    });

    dataPayment.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error("Lỗi khi tải dữ liệu:", error);
  } finally {
    isLoading.value = false;
  }
};

const fetchRooms = async () => {
  try {
    const response = await api.get("/admin/room"); // đổi theo route thật của bạn
    roomOptions.value = response.data.data.map((room) => ({
      label: room.room_code,
      value: room.id,
    }));
  } catch (error) {
    console.error("Lỗi khi lấy danh sách phòng:", error);
  }
};

// 🔄 Lấy dữ liệu ban đầu
onMounted(() => {
  fetchPayment();
  fetchRooms();
});

const handleEditPayments = (payment) => {
  selectedPayment.value = payment;
  openEditStatusForm.value = true;
};

const handleSubmitEditStatus = async (data) => {
  console.log("📦 Dữ liệu gửi đi:", data); // 👈 thêm dòng này để xem data có gì
  try {
    await api.put(`/admin/payment/${data.payment_id}`, data);
    alert("Cập nhật trạng thái thành công!");
    openEditStatusForm.value = false;
    await fetchPayment(searchKeyword.value);
  } catch (error) {
    console.error("Lỗi khi cập nhật trạng thái:", error);
    alert("Có lỗi xảy ra khi cập nhật!");
  }
};

// 🎛️ Khi người dùng thay đổi bộ lọc tháng/năm/trạng thái
const handleFilterChange = (newFilters) => {
  filters.value = newFilters;
  fetchPayment(searchKeyword.value, 1);
};

let debounceTimer;

// 🔍 Khi người dùng tìm kiếm
const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchPayment(searchKeyword.value);
  }, 500);
};

// 🔁 Chuyển trang
const handlePageChange = (page) => {
  fetchPayment(searchKeyword.value, page);
};
const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  // 🗓️ Hiển thị dạng dd/MM/yyyy
  return new Intl.DateTimeFormat("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  }).format(date);
};

// ⚙️ CRUD
const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewPayment.value = {};
};

const handleEditPayment = (payment) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};
  createNewPayment.value = { ...payment };
};

const handleSubmitPayment = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    let response;

    if (isEdit.value) {
      response = await api.put(`/admin/payment/${data.id}`, data);
      alert("Cập nhật thanh toán thành công!");
    } else {
      response = await api.post("/admin/payment", data);
      // 🧠 Thêm kiểm tra ở đây:
      if (!response.data.status) {
        alert(response.data.message); // ⚡ Hiện thông báo lỗi backend gửi về
        return;
      }
      alert("Thêm thanh toán thành công!");
    }

    openForm.value = false;
    createNewPayment.value = {};
    await fetchPayment(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      alert(error.response.data.message); // ⚡ Trường hợp lỗi từ backend (như phòng không có sinh viên)
    } else {
      console.error("Lỗi hệ thống:", error);
      alert("Có lỗi xảy ra, vui lòng thử lại!");
    }
  }
};

const handleDeletePayment = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa thanh toán này không?")) return;

  try {
    await api.delete(`/admin/payment/${id}`);
    await fetchPayment(searchKeyword.value);
    alert("Xóa thanh toán thành công!");
  } catch (error) {
    console.error("Lỗi khi xóa:", error);
  }
};

// 🎨 Hàm hiển thị avatar
const getInitials = (name) => {
  if (!name) return "??";
  const words = name.trim().split(" ");
  const lastTwo = words.slice(-2).map((w) => w[0].toUpperCase());
  return lastTwo.join("");
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
      title: "Tạo hóa đơn thanh toán",
      icon: "fas fa-boxes",
      fields: [
        {
          key: "room_id",
          label: "Số phòng",
          type: "text",
          placeholder: "VD : A101",
          icon: "fas fa-door-open",
        },

        {
          key: "electricity_usage",
          label: "Số điện tiêu thụ (kWh)/ Phòng",
          type: "text",
          placeholder: "VD : 50 Số ",
          icon: "fas fa-bolt",
        },
        {
          key: "water_usage",
          label: "Số nước tiêu thụ (m³) / Phòng",
          type: "text",
          placeholder: "VD : 50 m3/Tháng",
          icon: "fas fa-water",
        },
        {
          key: "description",
          label: "Mô tả",
          type: "text",
          placeholder: "VD : Tiên điện nước ",
          icon: "fas fa-info-circle",
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
