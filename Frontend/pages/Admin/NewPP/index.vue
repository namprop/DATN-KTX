<
<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <KTXAddAndSearch
      @search="handleSearch"
      @openForm="openAddForm"
      title="Danh Sách Bài Viết"
      titleAdd="Thêm Bài Viết"
      placeholderSearch="Tìm kiếm Bài Viết..."
    />

    <!-- <FormAdd
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật thông tin Bài Viết' : 'Thêm Bài Viết mới'"
      v-model="createNewAnnouncement"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitAnnouncement"
    /> -->

    <!-- Form thêm/sửa -->
    <FormAddAndEdit
      :fields="AccountInformationfields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật bài viết' : 'Thêm bài viết mới'"
      v-model="createNewAnnouncement"
      :mess="formErrors"
      @close="openForm = false"
      @submit="handleSubmitAnnouncement"
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
            v-for="(item, index) in dataAnnouncement"
            :key="item.id"
            class="hover:bg-gray-50 transition"
          >
            <td
              class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
            >
              {{ index + 1 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.title ?? "Chưa điền thông tin" }}
            </td>
            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.content ?? "Chưa điền thông tin" }}
            </td> -->

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ truncateWords(item.content, 5) }}
            </td>

            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ item.type ?? "Chưa điền thông tin" }}
            </td>
            <td class="px-6 py-2">
              <img
                v-if="item.image"
                :src="`http://localhost:8000/storage/${item.image}`"
                alt="Hình Ảnh Bài Viết"
                class="w-20 h-12 object-cover rounded"
              />
              <span v-else class="text-gray-400 italic">Chưa có ảnh</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="handleEditAnnouncement(item)"
                class="text-blue-600 hover:text-blue-900 mr-3 cursor-pointer"
              >
                Sửa
              </button>
              <button
                @click.prevent="handleDeleteAnnouncement(item.id)"
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
import { ref, onMounted, toRaw, computed } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const dataAnnouncement = ref({});
const pagination = ref({});
const formErrors = ref({});
const searchKeyword = ref("");
const isLoading = ref(false);

const createNewAnnouncement = ref({});
const openForm = ref(false);
const isEdit = ref(false);

const fetchAnnouncement = async (keyword = "", page = 1) => {
  isLoading.value = true; // Bắt đầu loading
  try {
    const response = await api.get("/admin/announcement", {
      params: {
        search: keyword,
        page,
      },
    });
    dataAnnouncement.value = response.data.data;
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
  fetchAnnouncement();
});

const truncateWords = (text, limit = 5) => {
  if (!text) return "Chưa điền thông tin";

  const words = text.trim().split(/\s+/);

  if (words.length <= limit) {
    return text;
  }

  return words.slice(0, limit).join(" ") + "...";
};

const openAddForm = () => {
  isEdit.value = false;
  openForm.value = true;
  formErrors.value = {};
  createNewAnnouncement.value = {}; // reset form
};

const handleEditAnnouncement = (announcement) => {
  isEdit.value = true;
  openForm.value = true;
  formErrors.value = {};

  createNewAnnouncement.value = {
    id: announcement.id,
    title: announcement.title,
    content: announcement.content,
    type: announcement.type,
    image: announcement.image
      ? `http://localhost:8000/storage/${announcement.image}`
      : null,
  };
};

const handleSubmitAnnouncement = async (datas) => {
  const data = toRaw(datas);
  formErrors.value = {};

  const formData = new FormData();
  formData.append("title", data.title || "");
  formData.append("type", data.type || "");
  formData.append("content", data.content || "");

  // 👇 QUAN TRỌNG
  if (data.image instanceof File) {
    formData.append("image", data.image);
  }

  try {
    if (isEdit.value) {
      formData.append("_method", "PUT");

      await api.post(`/admin/announcement/${data.id}`, formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      alert("Cập nhật Bài Viết thành công!");
    } else {
      await api.post("/admin/announcement", formData, {
        headers: { "Content-Type": "multipart/form-data" },
      });

      alert("Thêm Bài Viết thành công!");
    }

    openForm.value = false;
    createNewAnnouncement.value = {};
    await fetchAnnouncement(searchKeyword.value);
  } catch (error) {
    if (error.response?.status === 422) {
      formErrors.value = error.response.data.errors;
    } else {
      console.error("Lỗi hệ thống:", error);
    }
  }
};

const handleDeleteAnnouncement = async (id) => {
  const confirmed = confirm("Bạn có chắc muốn xóa Bài Viết này không?");
  if (!confirmed) return; // nếu Bài Viết bấm Cancel thì dừng lại

  try {
    await api.delete(`/admin/announcement/${id}`);
    await fetchAnnouncement(); // gọi lại danh sách sau khi xóa
    alert("Xóa Bài Viết thành công");
  } catch (error) {
    console.error("Lỗi khi xóa Bài Viết:", error);
    alert("Đã xảy ra lỗi khi xóa Bài Viết!");
  }
};

let debounceTimer = null; // tạo timer debounce

const handleSearch = (keyword) => {
  searchKeyword.value = keyword;

  if (debounceTimer) clearTimeout(debounceTimer); // hủy timer cũ
  debounceTimer = setTimeout(() => {
    fetchAnnouncement(searchKeyword.value);
  }, 500); // chỉ gọi API sau 500ms dừng gõ
};

const handlePageChange = (page) => {
  fetchAnnouncement(searchKeyword.value, page);
};

const titleTable = [
  "STT",
  "Tiêu Đề Bài Viết",
  "Nội Dung",
  "Kiểu Bài Viết",
  // "Người Đăng Bài",
  "Hình Ảnh",
  "Hành Động",
];

const AccountInformationfields = computed(() => {
  const baseFields = {
    personal: {
      title: isEdit.value ? "Cập nhật bài viết" : "Thêm bài viết mới",
      icon: "fas fa-newspaper",
      fields: [
        {
          key: "title",
          label: "Tiêu đề bài viết",
          type: "text",
          placeholder: "Nhập tiêu đề bài viết",
          icon: "fas fa-heading",
        },
        {
          key: "type",
          label: "Kiểu bài viết",
          type: "text",
          icon: "fas fa-toggle-on",
          placeholder: "Nhập kiểu bài viết (ví dụ: tin tức, sự kiện)",
        },
        {
          key: "image",
          label: "Hình ảnh",
          type: "file",
          icon: "fas fa-image",
        },
        {
          key: "content",
          label: "Nội dung bài viết",
          type: "textarea",
          placeholder: "Nhập nội dung bài viết",
          icon: "fas fa-align-left",
        },
      ],
    },
  };

  return [baseFields];
});
</script>
