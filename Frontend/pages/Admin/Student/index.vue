<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <!-- <KTXAddAndSearch @search="handleSearch" @openForm="openForm = true" /> -->

    <KTXAddAndSearch
     @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Học Sinh"
      titleAdd="Thêm Người Dùng"
      placeholderSearch="Tìm kiếm Người Dùng..."
       />

    <!-- <FormAdd
      :fields="AccountInformationfields"
      :show="openForm"
      title="Thêm người dùng mới"
      v-model="createNewStudent"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleAddStudent"
    /> -->

    <FormAddAndEdit
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông tin học sinh' : 'Thêm người dùng mới'"
      v-model="createNewStudent"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitStudent"
    />

    <!-- 🔄 Loading Skeleton -->
    <KTXLoadingSkeleton
      v-if="isLoading" :isLoading="isLoading"
        :titleTable="titleTable"
        />

    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <KTXTilteTable :titleTable="titleTable" />
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="(item, index) in dataStudents"
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
              {{ item.student_code }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.room?.room_code ?? "Chưa có phòng" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.phone ?? "Chưa Điền SĐT" }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.status ==="Active" ? "Hoạt Động" : "Vi Phạm"}}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <!-- <button
                class="text-cyan-600 hover:text-cyan-900 mr-3 cursor-pointer"
              >
                Xem
              </button> -->
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

  <Paginate
    v-if="pagination.total > pagination.per_page"
    :current-page="pagination.current_page"
    :last-page="pagination.last_page"
    @change="handlePageChange"
    :total="pagination.total"
  />
</template>

<script setup>
definePageMeta({middleware: "auth", layout: "dashboard" });
import { ref, onMounted, toRaw } from "vue";
import useAxios from "@/composables/useAxios";


const api = useAxios();
const dataStudents = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const titleTable = [
  "STT",
  "Họ và Tên Học Sinh",
  "Mã Học Sinh",
  "Số Phòng",
  "Số Điện Thoại",
  "Trạng Thái",
  "Hành Động",
];  

const isLoading = ref(false);

const createNewStudent = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const fetchStudents = async (keyword = "", page = 1) => {
  isLoading.value = true; // Bắt đầu loading
  try {
    const response = await api.get("/admin/student", {
      params: {
        search: keyword,
        page,
      },
    });
    dataStudents.value = response.data.data;
    pagination.value = response.data.pagination;
  } catch (error) {
    if (error.response && error.response.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }finally {
    isLoading.value = false; // 🔄 Kết thúc loading
  }
};
onMounted(() => {
  fetchStudents();
});

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewStudent.value = {}; // reset form
};

const handleEditStudent = (student) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  let birthDate = "";
  if (student.birth_day_of) {
    // Nếu API trả về 2002-01-01T00:00:00Z
    birthDate = student.birth_day_of.split("T")[0]; // => "2002-01-01"
  }
  

  createNewStudent.value = {
    id: student.id,
    name: student.user?.name || "",
    email: student.user?.email || "",
    password: "",
    password_confirmation: "",
    full_name: student.full_name,
    phone:student.phone,
    student_code: student.student_code,
    birth_day_of: birthDate, // gán đúng định dạng
    room_code: student.room?.room_code || "",
    gender: student.gender,
  };
};

const handleSubmitStudent = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  if (data.birth_day_of) {
    data.birth_day_of = new Date(data.birth_day_of).toISOString().slice(0, 10);
    // hoặc dùng: data.birth_day_of = dayjs(data.birth_day_of).format("YYYY-MM-DD");
  }

  try {
    if (isEdit.value) {
      await api.put(`/admin/student/${data.id}`, data);
      alert("Cập nhật học sinh thành công!");
    } else {
      await api.post("/admin/student", data);
      alert("Thêm học sinh thành công!");
    }

    openForm.value = false;
    createNewStudent.value = {};
    await fetchStudents(searchKeyword.value);
  } catch (error) {
      if (error.response?.status === 422) {
      // Kiểm tra xem có lỗi validation không
      if (error.response.data.errors) {
        formErrors.value = error.response.data.errors;
      } 
      // Nếu có lỗi nghiệp vụ (như phòng đầy)
      else if (error.response.data.error) {
        alert(error.response.data.error); // Hiển thị alert
        // HOẶC nếu FormAdd hỗ trợ hiển thị lỗi chung:
        // formErrors.value = { _general: [error.response.data.error] };
      }
      // Nếu có message chung
      else if (error.response.data.message) {
        alert(error.response.data.message);
      }
    } else {
      console.error("Lỗi hệ thống:", error);
      alert("Đã xảy ra lỗi hệ thống!");
    }
  }
};

// const handleAddStudent = async (datas) => {
//   const data = toRaw(datas);
//   formErrors.value = {}; // reset lỗi cũ
//   try {
//     await api.post("/student", data);
//     fetchStudents();
//     openForm.value = false;
//     createNewStudent.value = {};
//     alert("Thêm học sinh thành công");
//   } catch (error) {
//     if (error.response && error.response.status === 422) {
//       // ⬅️ Lấy lỗi từ Laravel validation
//       formErrors.value = error.response.data.errors;
//     } else {
//       console.error("Lỗi hệ thống:", error);
//     }
//   }
// };

const handleDeleteStudent = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa học sinh này không?");
  if (!confirmed) return; // nếu người dùng bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/student/${id}`);
    await fetchStudents(); // gọi lại danh sách sau khi xóa
    alert("Xóa học sinh thành công");
  } catch (error) {
    console.error("Lỗi khi xóa học sinh:", error);
    alert("Đã xảy ra lỗi khi xóa học sinh!");
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
    fetchStudents(searchKeyword.value);
  }, 500); // chỉ gọi API sau 500ms dừng gõ
};

const handlePageChange = (page) => {
  fetchStudents(searchKeyword.value, page);
};

// const AccountInformationfields = computed(() => [
//   {
//     account: {
//       title: "Thông tin tài khoản",
//       icon: "fas fa-user-circle",
//       fields: [
//         {
//           key: "name",
//           label: "Tên đăng nhập",
//           type: "text",
//           placeholder: "Nguyen Van A",
//           icon: "fas fa-user",
//         },
//         {
//           key: "email",
//           label: "Email",
//           type: "email",
//           placeholder: "user@ktx.edu.vn",
//           icon: "fas fa-envelope",
//         },
//         {
//           key: "password",
//           label: "Mật khẩu",
//           type: "password",
//           placeholder: "••••••••",
//           icon: "fas fa-lock",
//         },
//         {
//           key: "password_confirmation",
//           label: "Xác nhận mật khẩu",
//           type: "password",
//           placeholder: "••••••••",
//           icon: "fas fa-lock",
//         },
//       ],
//     },

//     personal: {
//       title: "Thông tin cá nhân",
//       icon: "fas fa-id-card",
//       fields: [
//         {
//           key: "full_name",
//           label: "Họ và tên",
//           type: "text",
//           placeholder: "Nguyen Van A",
//           icon: "fas fa-signature",
//         },
//         {
//           key: "birth_day_of",
//           label: "Ngày sinh",
//           type: "date",
//           placeholder: "",
//           icon: "fas fa-signature",
//         },
//         {
//           key: "student_code",
//           label: "Mã Học Sinh",
//           type: "text",
//           placeholder: "VD:696999",
//           icon: "fas fa-signature",
//         },

//         {
//           key: "room_code",
//           label: "Số phòng",
//           type: "text",
//           placeholder: "VD: A101",
//           icon: "fas fa-signature",
//         },

//         {
//           key: "gender",
//           label: "Giới tính",
//           type: "select",
//           placeholder: "",
//           icon: "fas fa-venus-mars",
//           options: [
//             { value: "", text: "Chọn giới tính" },
//             { value: "Male", text: "Nam" },
//             { value: "Female", text: "Nữ" },
//             { value: "Other", text: "Khác" },
//           ],
//         },
//       ],
//     },
//   },

// ]);

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
          key: "birth_day_of",
          label: "Ngày sinh",
          type: "date",
          placeholder: "",
          icon: "fas fa-calendar",
        },
        {
          key: "student_code",
          label: "Mã Học Sinh",
          type: "text",
          placeholder: "VD:696999",
          icon: "fas fa-signature",
        },
        {
          key: "room_code",
          label: "Số phòng",
          type: "text",
          placeholder: "VD: A101",
          icon: "fas fa-door-open",
        },
          {
          key: "phone",
          label: "Số Điện Thoại",
          type: "text",
          placeholder: "VD : 098761235",
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
