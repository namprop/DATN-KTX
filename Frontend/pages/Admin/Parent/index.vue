<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Người Dùng"
      titleAdd="Thêm Người Dùng"
      placeholderSearch="Tìm kiếm Người Dùng..."
    />

    <FormAdd
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông tin Người Dùng' : 'Thêm người dùng mới'"
      v-model="createNewParentStudent"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitParentStudent"
    />

    <!-- 🔄 Loading Skeleton -->
    <KTXLoadingSkeleton
      v-if="isLoading"
      :isLoading="isLoading"
      :titleTable="titleTable"
    />

    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <KTXTilteTable :titleTable="titleTable" />
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataParentStudent"
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
                  {{ getInitials(item.full_name) }}
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-gray-900">
                    {{ item.full_name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ item.user?.email }}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.student.full_name ?? "Chưa điền thông tin" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.phone ?? "Chưa Điền SĐT" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.address ?? "Chưa Điền Thông Tin" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <!-- <button
                class="text-cyan-600 hover:text-cyan-900 mr-3 cursor-pointer"
              >
                Xem
              </button> -->
              <button
                @click="handleEditParentStudent(item)"
                class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer"
              >
                Sửa
              </button>
              <button
                @click.prevent="handleDeleteParentStudent(item.id)"
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
    :total="pagination.total"
    :last-page="pagination.last_page"
    @change="handlePageChange"
  />
</template>

<script setup>
definePageMeta({ middleware: "auth", layout: "dashboard" });
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataParentStudent = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewParentStudent = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const fetchParentStudent = async (keyword = "", page = 1) => {
  isLoading.value = true; // Bắt đầu loading
  try {
    const response = await api.get("/admin/parentstudent", {
      params: {
        search: keyword,
        page,
      },
    });
    dataParentStudent.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    if (error.response && error.response.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  } finally {
    isLoading.value = false; // 🔄 Kết thúc loading
  }
};
onMounted(() => {
  fetchParentStudent();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewParentStudent.value = {}; // reset form
};

const handleEditParentStudent = (parentStudent) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewParentStudent.value = {
    id: parentStudent.id,
    full_name: parentStudent.full_name,
    phone: parentStudent.phone,
    address: parentStudent.address,
    student_code: parentStudent.student.student_code,
    gender: parentStudent.gender,
  };
};

const handleSubmitParentStudent = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/parentstudent/${data.id}`, data);
      alert("Cập nhật người dùng thành công!");
    } else {
      await api.post("/admin/parentstudent", data);
      alert("Thêm người dùng thành công!");
    }

    openForm.value = false;
    createNewParentStudent.value = {};
    await fetchParentStudent(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteParentStudent = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa người dùng này không?");
  if (!confirmed) return; // nếu người dùng bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/parentstudent/${id}`);
    await fetchParentStudent(); // gọi lại danh sách sau khi xóa
    alert("Xóa người dùng thành công");
  } catch (error) {
    console.error("Lỗi khi xóa người dùng:", error);
    alert("Đã xảy ra lỗi khi xóa người dùng!");
  }
};

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

let debounceTimer = null; // tạo timer debounce

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchUsers(searchKeyword.value);
  }, 500); // chỉ gọi API sau 500ms dừng gõ
};

const handlePageChange = (page) => {
  fetchParentStudent(searchKeyword.value, page);
};

const titleTable = [
  "STT",
  "Họ và Tên Phụ Huynh",
  "Họ và Tên Học Sinh",
  "Số Điện Thoại",
  "Địa Chỉ",
  "Hành Động",
];

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
          placeholder: "Nguyen Van A",
          icon: "fas fa-user",
        },
        {
          key: "email",
          label: "Email",
          type: "email",
          placeholder: "user@ktx.edu.vn",
          icon: "fas fa-envelope",
        },
        {
          key: "password",
          label: "Mật khẩu",
          type: "password",
          placeholder: "••••••••",
          icon: "fas fa-lock",
        },
        {
          key: "password_confirmation",
          label: "Xác nhận mật khẩu",
          type: "password",
          placeholder: "••••••••",
          icon: "fas fa-lock",
        },
      ],
    },
    personal: {
      title: "Thông tin cá nhân",
      icon: "fas fa-id-card",
      fields: [
        {
          key: "full_name",
          label: "Họ và tên",
          type: "text",
          placeholder: "Nguyen Van A",
          icon: "fas fa-signature",
        },

        {
          key: "phone",
          label: "Số Điện Thoại",
          type: "text",
          placeholder: "VD : 098761235",
          icon: "fas fa-door-open",
        },
        {
          key: "student_code",
          label: "Số thẻ học sinh",
          type: "text",
          placeholder: "VD : 000001",
          icon: "fas fa-door-open",
        },
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
        {
          key: "address",
          label: "Địa chỉ",
          type: "text",
          placeholder: "VD : Hà Nội",
          icon: "fas fa-door-open",
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
