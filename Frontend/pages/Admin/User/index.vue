<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <!-- Thanh tìm kiếm và nút thêm -->
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Người dùng"
      titleAdd="Thêm Người Dùng"
      placeholderSearch="Tìm kiếm Người Dùng..."
    />

    <!-- Form thêm/sửa -->
    <FormAddAndEdit
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông người dùng' : 'Thêm người dùng mới'"
      v-model="createNewUser"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitUser"
    />

    <!-- Skeleton khi loading -->
    <KTXLoadingSkeleton
      v-if="isLoading"
      :isLoading="isLoading"
      :titleTable="titleTable"
    />

    <!-- Bảng dữ liệu -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <KTXTilteTable :titleTable="titleTable" />
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataUsers"
            :key="item.id"
            class="hover:bg-gray-50 transition"
          >
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              {{ index + 1 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm bg-gradient-to-r"
                  :class="getRandomColor()"
                >
                  {{ getInitials(item.name) }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ item.name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ item.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.role ?? "Chưa có vai trò" }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.status === "Active" ? "Hoạt động" : "Không hoạt động" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="handleEditUser(item)"
                class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer"
              >
                Sửa
              </button>
              <button
                @click.prevent="handleDeleteUser(item.id)"
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

  <Paginate
    v-if="pagination.total > pagination.per_page"
    :current-page="pagination.current_page"
    :last-page="pagination.last_page"
    @change="handlePageChange"
    :total="pagination.total"
  />
</template>

<script setup>
import { ref, onMounted, computed, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

definePageMeta({ middleware: "auth", layout: "dashboard" });

const api = useAxios();
const dataUsers = ref([]);
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");

const titleTable = [
  "STT",
  "Họ và Tên Người dùng",
  "Vai Trò",
  "Trạng Thái",
  "Hành Động",
];

const isLoading = ref(false);
const createNewUser = ref({});
const openForm = ref(false);
const isEdit = ref(false);

// Fetch dữ liệu từ API
const fetchUsers = async (keyword = "", page = 1) => {
  isLoading.value = true;
  try {
    const res = await api.get("/admin/user", {
      params: { search: keyword, page },
    });
    dataUsers.value = res.data.data;
    pagination.value = res.data.pagination;
  } catch (err) {
    console.error("Lỗi fetch users:", err);
  } finally {
    isLoading.value = false;
  }
};
onMounted(() => fetchUsers());

// Thao tác thêm/sửa
const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  createNewUser.value = {
    role: "Admin",
    password: "",
    password_confirmation: "",
  };
  formErrors.value = {};
};

const handleEditUser = (item) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};
  createNewUser.value = {
    id: item.id,
    name: item.name, // dùng full_name cho tài khoản
    email: item.email,
    password: "",
    password_confirmation: "",
    role: item.role,
  };
};

const handleSubmitUser = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/user/${data.id}`, data);
      alert("Cập nhật Người dùng thành công!");
    } else {
      await api.post("/admin/user", data);
      alert("Thêm Người dùng thành công!");
    }
    openForm.value = false;
    createNewUser.value = {};
    await fetchUsers(searchKeyword.value);
  } catch (err) {
    if (err.response?.status === 422) {
      formErrors.value = err.response.data.errors || {};
    } else {
      alert("Đã xảy ra lỗi hệ thống!");
      console.error(err);
    }
  }
};

// Xóa người dùng
const handleDeleteUser = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa Người dùng này không?")) return;
  try {
    await api.delete(`/admin/user/${id}`);
    alert("Xóa Người dùng thành công");
    await fetchUsers(searchKeyword.value);
  } catch (err) {
    alert("Xóa thất bại!");
    console.error(err);
  }
};

// Giúp hiển thị avatar chữ cái
const getInitials = (name) => {
  if (!name) return "??";
  const words = name.trim().split(" ");
  return words
    .slice(-2)
    .map((w) => w[0].toUpperCase())
    .join("");
};

// Gradient màu avatar
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

let debounceTimer = null; // tạo timer debounce

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchUsers(searchKeyword.value);
  }, 500); // chỉ gọi API sau 500ms dừng gõ
};

const handlePageChange = (page) => fetchUsers(searchKeyword.value, page);

// Cấu hình fields form
const AccountInformationfields = computed(() => {
  const baseFields = {
    account: {
      title: "Thông tin tài khoản",
      icon: "fas fa-user-circle",
      fields: [
        {
          key: "name",
          label: "Tên đăng nhập",
          type: "text",
          icon: "fas fa-user",
        },
        {
          key: "email",
          label: "Email",
          type: "email",
          icon: "fas fa-envelope",
        },
        {
          key: "password",
          label: "Mật khẩu",
          type: "password",
          icon: "fas fa-lock",
        },
        {
          key: "password_confirmation",
          label: "Xác nhận mật khẩu",
          type: "password",
          icon: "fas fa-lock",
        },
      ],
    },
    // personal: {
    //   title: "Thông tin cá nhân",
    //   icon: "fas fa-id-card",
    //   fields: [
    //     {
    //       key: "full_name",
    //       label: "Họ và tên",
    //       type: "text",
    //       icon: "fas fa-signature",
    //     },
    //     {
    //       key: "birth_day_of",
    //       label: "Ngày sinh",
    //       type: "date",
    //       icon: "fas fa-calendar",
    //     },
    //     {
    //       key: "user_code",
    //       label: "Mã Người dùng",
    //       type: "text",
    //       icon: "fas fa-signature",
    //     },
    //     {
    //       key: "room_code",
    //       label: "Số phòng",
    //       type: "text",
    //       icon: "fas fa-door-open",
    //     },
    //     {
    //       key: "phone",
    //       label: "Số Điện Thoại",
    //       type: "text",
    //       icon: "fas fa-phone",
    //     },
    //     {
    //       key: "gender",
    //       label: "Giới tính",
    //       type: "select",
    //       icon: "fas fa-venus-mars",
    //       options: [
    //         { value: "", text: "Chọn giới tính" },
    //         { value: "Male", text: "Nam" },
    //         { value: "Female", text: "Nữ" },
    //         { value: "Other", text: "Khác" },
    //       ],
    //     },
    //   ],
    // },
  };

  // Chỉ hiển thị phần personal khi edit
  return [baseFields];
});
</script>
