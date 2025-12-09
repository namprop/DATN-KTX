<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <!-- Thanh tìm kiếm + thêm mới -->
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Học Sinh"
      titleAdd="Thêm Học Sinh"
      placeholderSearch="Tìm kiếm học sinh..."
    />

    <!-- Form thêm / sửa -->
    <FormAddAndEdit
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật học sinh' : 'Thêm học sinh mới'"
      v-model="createNewSchoolStudent"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitSchoolStudent"
    />

    <!-- Loading -->
    <KTXLoadingSkeleton
      v-if="isLoading"
      :isLoading="isLoading"
      :titleTable="titleTable"
    />

    <!-- Bảng danh sách -->
    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <KTXTilteTable :titleTable="titleTable" />
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataSchoolStudents"
            :key="item.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-6 py-4 text-sm text-gray-900">{{ index + 1 }}</td>
            <td class="px-6 py-4">
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
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
              {{ item.student_code }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
              {{
                item.gender === "Male"
                  ? "Nam"
                  : item.gender === "Female"
                  ? "Nữ"
                  : "Khác"
              }}
            </td>
            <td class="px-6 py-4 text-sm font-medium">
              <!-- <button
                @click="handleEditSchoolStudent(item)"
                class="text-blue-600 hover:text-blue-900 mr-3"
              >
                Sửa
              </button>
              <button
                @click.prevent="handleDeleteSchoolStudent(item.id)"
                class="text-red-600 hover:text-red-900"
              >
                Xóa
              </button> -->
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
    :total="pagination.total"
    @change="handlePageChange"
  />
</template>

<script setup>
definePageMeta({ middleware: "auth", layout: "dashboard" });

import { ref, onMounted, toRaw, computed } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataSchoolStudents = ref([]);
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewSchoolStudent = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const titleTable = ["STT", "Họ và Tên", "Mã Học Sinh", "Giới Tính"];

// ✅ Lấy danh sách học sinh
const fetchSchoolStudents = async (keyword = "", page = 1) => {
  isLoading.value = true;
  try {
    const response = await api.get("/admin/schoolstudent", {
      params: { search: keyword, page },
    });
    dataSchoolStudents.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    console.error("Lỗi khi tải danh sách học sinh:", error);
  } finally {
    isLoading.value = false;
  }
};
onMounted(() => fetchSchoolStudents());

// ✅ Mở form thêm
const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewSchoolStudent.value = {};
};

// ✅ Sửa
const handleEditSchoolStudent = (schoolstudent) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};
  createNewSchoolStudent.value = { ...schoolstudent };
};

// ✅ Gửi form (thêm / sửa)
const handleSubmitSchoolStudent = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};
  try {
    if (isEdit.value) {
      await api.put(`/admin/schoolstudent/${data.id}`, data);
      alert("Cập nhật học sinh thành công!");
    } else {
      await api.post("/admin/schoolstudent", data);
      alert("Thêm học sinh thành công!");
    }
    openForm.value = false;
    await fetchSchoolStudents(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors || {};
    } else {
      console.error("Lỗi hệ thống:", error);
      alert("Đã xảy ra lỗi hệ thống!");
    }
  }
};

// ✅ Xóa
const handleDeleteSchoolStudent = async (id) => {
  if (!confirm("Bạn có chắc muốn xóa học sinh này không?")) return;
  try {
    await api.delete(`/admin/schoolstudent/${id}`);
    await fetchSchoolStudents(searchKeyword.value);
    alert("Xóa học sinh thành công");
  } catch (error) {
    console.error("Lỗi khi xóa học sinh:", error);
    alert("Không thể xóa học sinh!");
  }
};

// ✅ Tìm kiếm + phân trang
let debounceTimer;

// 🔍 Khi người dùng tìm kiếm
const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchSchoolStudents(searchKeyword.value);
  }, 500);
};

const handlePageChange = (page) => {
  fetchSchoolStudents(searchKeyword.value, page);
};

// ✅ Helper hiển thị
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
  ];
  return colors[Math.floor(Math.random() * colors.length)];
};

// ✅ Field form
const AccountInformationfields = computed(() => [
  {
    personal: {
      title: "Thông tin học sinh",
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
          key: "student_code",
          label: "Mã học sinh",
          type: "text",
          placeholder: "VD:696999",
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
      ],
    },
  },
]);
</script>
