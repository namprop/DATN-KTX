<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <!-- Thanh tìm kiếm + nút thêm -->
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
    />

    <!-- Form thêm / sửa -->
    <FormAdd
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông tin học sinh' : 'Thêm người dùng mới'"
      v-model="createNewStudent"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitStudent"
    />

    <!-- Bảng danh sách -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">STT</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Họ và tên</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Mã HS</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Phòng</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Số điện thoại</th>
            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Thao tác</th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataStudents"
            :key="item.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ index + 1 }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm bg-gradient-to-r"
                  :class="getRandomColor()"
                >
                  {{ getInitials(item.full_name) }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">{{ item.full_name }}</p>
                  <p class="text-sm text-gray-500">{{ item.user?.email }}</p>
                </div>
              </div>
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.student_code }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.room?.room_code ?? "Chưa có phòng" }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ item.phone ?? "Chưa Điền SĐT" }}</td>

            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="handleEditStudent(item)"
                class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer"
              >
                Sửa
              </button>

              <button
                @click.prevent="handleDeleteStudent(item.id)"
                class="text-red-600 hover:text-red-900 cursor-pointer"
              >
                Xóa
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Phân trang -->
  <Paginate
    v-if="pagination.total > pagination.per_page"
    :current-page="pagination.current_page"
    :last-page="pagination.last_page"
    @change="handlePageChange"
  />
</template>

<script setup>
definePageMeta({ layout: "dashboard" });
import { ref, onMounted, toRaw, computed } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataStudents = ref([]);
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");

// Trạng thái form
const openForm = ref(false);
const isEdit = ref(false);
const createNewStudent = ref({});

// ================== LẤY DANH SÁCH ==================
const fetchStudents = async (keyword = "", page = 1) => {
  try {
    const response = await api.get("/student", {
      params: { search: keyword, page },
    });
    dataStudents.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error("Lỗi khi lấy danh sách:", error);
  }
};
onMounted(() => fetchStudents());

// ================== MỞ FORM THÊM ==================
const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewStudent.value = {}; // reset form
};

// ================== MỞ FORM SỬA ==================
const handleEditStudent = (student) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewStudent.value = {
    id: student.id,
    name: student.user?.name || "",
    email: student.user?.email || "",
    password: "",
    password_confirmation: "",
    full_name: student.full_name,
    student_code: student.student_code,
    birth_day_of: student.birth_day_of,
    room_code: student.room?.room_code || "",
    gender: student.gender,
  };
};

// ================== THÊM HOẶC CẬP NHẬT ==================
const handleSubmitStudent = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/student/${data.id}`, data);
      alert("Cập nhật học sinh thành công!");
    } else {
      await api.post("/student", data);
      alert("Thêm học sinh thành công!");
    }

    openForm.value = false;
    createNewStudent.value = {};
    await fetchStudents(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

// ================== XOÁ ==================
const handleDeleteStudent = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa học sinh này không?")) return;

  try {
    await api.delete(`/student/${id}`);
    await fetchStudents(searchKeyword.value);
    alert("Xóa học sinh thành công");
  } catch (error) {
    console.error("Lỗi khi xóa học sinh:", error);
  }
};

// ================== TÌM KIẾM + PHÂN TRANG ==================
const handleSearch = (keyword) => {
  searchKeyword.value = keyword;
  fetchStudents(keyword);
};

const handlePageChange = (page) => {
  fetchStudents(searchKeyword.value, page);
};

// ================== HIỂN THỊ ==================
const getInitials = (name) => {
  if (!name) return "??";
  const words = name.trim().split(" ");
  return words.slice(-2).map((w) => w[0].toUpperCase()).join("");
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

// ================== CẤU HÌNH FORM ==================
const AccountInformationfields = computed(() => [
  {
    account: {
      title: "Thông tin tài khoản",
      icon: "fas fa-user-circle",
      fields: [
        { key: "name", label: "Tên đăng nhập", type: "text", placeholder: "Nguyen Van A", icon: "fas fa-user" },
        { key: "email", label: "Email", type: "email", placeholder: "user@ktx.edu.vn", icon: "fas fa-envelope" },
        { key: "password", label: "Mật khẩu", type: "password", placeholder: "••••••••", icon: "fas fa-lock" },
        { key: "password_confirmation", label: "Xác nhận mật khẩu", type: "password", placeholder: "••••••••", icon: "fas fa-lock" },
      ],
    },
    personal: {
      title: "Thông tin cá nhân",
      icon: "fas fa-id-card",
      fields: [
        { key: "full_name", label: "Họ và tên", type: "text", placeholder: "Nguyen Van A", icon: "fas fa-signature" },
        { key: "birth_day_of", label: "Ngày sinh", type: "date", icon: "fas fa-calendar" },
        { key: "student_code", label: "Mã Học Sinh", type: "text", placeholder: "VD:696999", icon: "fas fa-id-badge" },
        { key: "room_code", label: "Số phòng", type: "text", placeholder: "VD: A101", icon: "fas fa-door-open" },
        {
          key: "gender",
          label: "Giới tính",
          type: "select",
          icon: "fas fa-venus-mars",
          options: [
            { value: "", text: "Chọn giới tính" },
            { value: "Male", text: "Nam" },
            { value: "Female", text: "Nữ" },
            { value: "Other", text: "Khác" },
          ],
        },
      ],
    },
  },
]);
</script>
