```vue
<!-- components/UserFormModal.vue -->
<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Overlay -->
      <div
        class="absolute inset-0 bg-black/40"
        @click="$emit('close')"
      ></div>

      <!-- Modal Content -->
      <div
        class="relative bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
      >
        <!-- Header -->
        <div
          class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between"
        >
          <div class="flex items-center space-x-3">
            <div class="bg-blue-100 p-2 rounded-lg">
              <i class="fa-solid fa-user-plus text-blue-600 text-lg"></i>
            </div>
            <div>
              <h2 class="text-xl font-bold text-gray-800">Thêm người dùng mới</h2>
              <p class="text-xs text-gray-500">Điền thông tin để tạo tài khoản</p>
            </div>
          </div>
          <button
            class="text-gray-400 hover:text-gray-600 transition-colors"
            @click="$emit('close')"
          >
            <i class="fa-solid fa-xmark text-xl"></i>
          </button>
        </div>

        <!-- Form -->
        <div class="px-6 py-4">
          <form class="space-y-5" @submit.prevent="handleSubmit">
            <!-- Account Information -->
            <div class="border-b border-gray-200 pb-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fa-solid fa-user-circle mr-2 text-blue-600"></i>
                Thông tin tài khoản
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Tên đăng nhập <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.username"
                    type="text"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="username123"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="user@ktx.edu.vn"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Mật khẩu <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.password"
                    type="password"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="••••••••"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Xác nhận mật khẩu <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.password_confirmation"
                    type="password"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="••••••••"
                  />
                </div>
              </div>
            </div>

            <!-- Personal Information -->
            <div class="border-b border-gray-200 pb-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fa-solid fa-id-card mr-2 text-blue-600"></i>
                Thông tin cá nhân
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Họ và tên <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.fullname"
                    type="text"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="Nguyễn Văn A"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Số điện thoại
                  </label>
                  <input
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="0987654321"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Ngày sinh
                  </label>
                  <input
                    v-model="form.birthday"
                    type="date"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                  />
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Giới tính
                  </label>
                  <select
                    v-model="form.gender"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                  >
                    <option value="">Chọn giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Địa chỉ
                  </label>
                  <textarea
                    v-model="form.address"
                    rows="2"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                    placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- Role & Permissions -->
            <div class="pb-4">
              <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fa-solid fa-user-shield mr-2 text-blue-600"></i>
                Vai trò & Quyền hạn
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Vai trò
                  </label>
                  <select
                    v-model="form.role"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                  >
                    <option value="">Chọn vai trò</option>
                    <option value="admin">Quản trị viên</option>
                    <option value="staff">Nhân viên</option>
                    <option value="accountant">Kế toán</option>
                    <option value="manager">Quản lý</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-medium text-gray-700 mb-1">
                    Phòng ban
                  </label>
                  <select
                    v-model="form.department"
                    class="w-full px-3 py-2 text-sm border rounded-lg"
                  >
                    <option value="">Chọn phòng ban</option>
                    <option value="admin">Hành chính</option>
                    <option value="student_affairs">Công tác sinh viên</option>
                    <option value="finance">Tài chính</option>
                    <option value="maintenance">Bảo trì</option>
                  </select>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div class="pb-2">
              <label class="block text-xs font-medium text-gray-700 mb-1">
                Ghi chú
              </label>
              <textarea
                v-model="form.note"
                rows="2"
                class="w-full px-3 py-2 text-sm border rounded-lg"
                placeholder="Thêm ghi chú về người dùng này (tùy chọn)..."
              ></textarea>
            </div>
          </form>
        </div>

        <!-- Footer Buttons -->
        <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4">
          <div class="flex flex-col sm:flex-row gap-2">
            <button
              type="button"
              class="flex-1 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50"
              @click="resetForm"
            >
              <i class="fa-solid fa-rotate mr-2 text-xs"></i>
              Làm mới
            </button>
            <button
              type="button"
              class="flex-1 bg-gray-600 text-white px-4 py-2.5 rounded-lg hover:bg-gray-700"
              @click="$emit('close')"
            >
              <i class="fa-solid fa-xmark mr-2 text-xs"></i>
              Hủy bỏ
            </button>
            <button
              type="button"
              class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 shadow-lg"
              @click="handleSubmit"
            >
              <i class="fa-solid fa-floppy-disk mr-2 text-xs"></i>
              Lưu người dùng
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup>
import { reactive, watch } from "vue";

const props = defineProps({
  show: Boolean,
  modelValue: {
    type: Object,
    default: () => ({}),
  },
});
const emit = defineEmits(["close", "submit", "update:modelValue"]);

const form = reactive({ ...props.modelValue });

// Đồng bộ khi modelValue thay đổi
watch(
  () => props.modelValue,
  (newVal) => {
    Object.assign(form, newVal);
  },
  { deep: true }
);

function resetForm() {
  for (let key in form) form[key] = "";
}

function handleSubmit() {
  emit("update:modelValue", { ...form });
  emit("submit", { ...form });
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
```
