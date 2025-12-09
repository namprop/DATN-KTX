<template>
  <div class="w-full my-20 flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-md p-6 bg-white rounded-xl shadow-lg">
      <h2 class="text-center text-lg font-normal text-gray-500 mb-4">
        ĐĂNG KÝ TÀI KHOẢN PHỤ HUYNH MỚI
      </h2>
      <form class="space-y-3" @submit="handleRegister">
        <div class="relative">
          <input
            type="text"
            v-model="registerForm.name"
            placeholder="Họ và tên"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-user"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="email"
            v-model="registerForm.email"
            placeholder="Email"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 bg-blue-50 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-envelope"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="password"
            v-model="registerForm.password"
            placeholder="Mật khẩu"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 bg-blue-50 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-lock"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="password"
            v-model="registerForm.password_confirmation"
            placeholder="Nhập Lại Mật khẩu"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 bg-blue-50 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-lock"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="text"
            v-model="registerForm.student_id"
            placeholder="Mã học sinh"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-signature"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="text"
            v-model="registerForm.address"
            placeholder="Địa chỉ"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-address-card"></i>
          </span>
        </div>

        <div class="relative">
          <input
            type="tel"
            v-model="registerForm.phone"
            placeholder="Số điện thoại"
            required
            class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded"
          />
          <span class="absolute left-3 top-2.5 text-gray-500">
            <i class="fas fa-phone"></i>
          </span>
        </div>

        <button
          type="submit"
          class="w-full border border-black py-2 hover:bg-gray-300"
          :disabled="loading"
        >
          {{ loading ? "Đang xử lý..." : "Đăng ký" }}
        </button>

        <p
          v-if="message"
          class="text-center text-sm mt-2"
          :class="message.includes('thành công') ? 'text-green-600' : 'text-red-500'"
        >
          {{ message }}
        </p>
      </form>
    </div>
  </div>
</template>


<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import useAxios from "@/composables/useAxios";
import useAuth from "@/composables/useAuth";

definePageMeta({ layout: "index" });

const router = useRouter();
const api = useAxios();
const { login } = useAuth();

const error = ref(""); // ✅ khai báo error

const loading = ref(false);
const message = ref("");

const form = reactive({
  email: "",
  password: "",
});

const registerForm = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  student_id: "",
  address: "",
  phone: "",
  role: "Parent",
});


// 🟢 Đăng ký
const handleRegister = async (e) => {
  e.preventDefault();
  message.value = "";
  loading.value = true;

  try {
    const res = await api.post("/registerparentstudent", registerForm);
    message.value = "Đăng ký thành công! Vui lòng đăng nhập.";
    alert(message.value);

    // Reset form (giữ nguyên role)
    Object.keys(registerForm).forEach((k) => {
      if (k !== "role") registerForm[k] = "";
    });
  } catch (err) {
    message.value = err.response?.data?.message || "Đăng ký thất bại. Vui lòng thử lại.";
  } finally {
    loading.value = false;
  }
};

</script>
