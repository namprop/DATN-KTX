<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Ban Quản Lý"
      titleAdd="Thêm Ban Quản Lý"
      placeholderSearch="Tìm kiếm Ban Quản Lý..."
    />

    <FormAddAndEdit
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông tin Ban Quản Lý' : 'Thêm người dùng mới'"
      v-model="createNewDormManager"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitDormManager"
    />

    <!-- 🔄 Loading Skeleton -->
    <KTXLoadingSkeleton
     v-if="isLoading" :isLoading="isLoading"
      :titleTable="titleTable"
      />

    <!-- ✅ Dữ liệu thực -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <KTXTilteTable :titleTable="titleTable" />
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataDormManager"
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
              {{ item.position ?? "Ban Quản Lý Ký Túc Xá" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.phone ?? "Chưa Điền SĐT" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <!-- <button
                class="text-cyan-600 hover:text-cyan-900 mr-3 cursor-pointer"
              >
                Xem
              </button> -->
              <button
                @click="handleEditDormManager(item)"
                class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer"
              >
                Sửa
              </button>
              <button
                @click.prevent="handleDeleteDormManager(item.id)"
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
    v-if="!isLoading && pagination.total > pagination.per_page"
    :current-page="pagination.current_page"
    :last-page="pagination.last_page"
    @change="handlePageChange"
  />
</template>

<script setup>
definePageMeta({middleware: "auth", layout: "dashboard" });
import { ref, onMounted, toRaw, computed } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataDormManager = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false); // 🔄 Thêm state loading

const createNewDormManager = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const fetchDormManager = async (keyword = "", page = 1) => {
  isLoading.value = true; // 🔄 Bắt đầu loading
  try {
    const response = await api.get("/admin/dormmanager", {
      params: {
        search: keyword,
        page,
      },
    });
    dataDormManager.value = response.data.data;
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
  fetchDormManager();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewDormManager.value = {};
};

const handleEditDormManager = (dormmanager) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewdormmanager.value = {
    id: dormmanager.id,
    full_name: dormmanager.full_name,
    gender: dormmanager.gender,
    phone: dormmanager.phone,
    position: dormmanager.position,
  };
};

const handleSubmitDormManager = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  try {
    if (isEdit.value) {
      await api.put(`/admin/dormmanager/${data.id}`, data);
      alert("Cập nhật ban quản lý thành công!");
    } else {
      await api.post("/admin/dormmanager", data);
      alert("Thêm ban quản lý thành công!");
    }

    openForm.value = false;
    createNewDormManager.value = {};
    await fetchDormManager(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteDormManager = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa ban quản lý này không?");
  if (!confirmed) return;

  try {
    await api.delete(`/admin/dormmanager/${id}`);
    await fetchdormmanager();
    alert("Xóa ban quản lý thành công");
  } catch (error) {
    console.error("Lỗi khi xóa ban quản lý:", error);
    alert("Đã xảy ra lỗi khi xóa ban quản lý!");
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

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;
  fetchDormManager(keyword);
};

const handlePageChange = (page) => {
  fetchDormManager(searchKeyword.value, page);
};

const titleTable = ["STT", "Họ và tên", "Chức vụ", "Số điện thoại", "Thao tác"];

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
          key: "phone",
          label: "Số Điện Thoại",
          type: "text",
          placeholder: "VD : 098761235",
          icon: "fas fa-door-open",
        },
        {
          key: "position",
          label: "Chức vụ ban quản lý",
          type: "text",
          placeholder: "VD : ban quản lý",
          icon: "fas fa-door-open",
        },
      ],
    },
  };

  if (isEdit.value) {
    return [{ personal: baseFields.personal }];
  }

  return [baseFields];
});
</script>
