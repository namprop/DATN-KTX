<template>
  <!-- Nền -->
  <section
    class="relative w-full h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center"
    :style="{ backgroundImage: `url(${bgImage})` }"
  >
    <!-- Form đăng nhập/đăng ký -->
    <div
      class="relative z-10 w-full max-w-sm p-6 space-y-5 backdrop-blur-lg rounded-xl shadow-lg text-sm scale-90"
    >
      <!-- Form Đăng nhập -->
      <div v-if="isLoginForm" id="login-form">
        <h2 class="text-3xl font-semibold text-center text-white">Đăng Nhập</h2>
        <form class="mt-6 space-y-4" @submit.prevent="handleLogin">
          <div class="space-y-4">
            <div>
              <input
                v-model="form.email"
                id="email"
                name="email"
                type="email"
                autocomplete="email"
                required
                @focus="clearError"
                @input="clearError"
                class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
                placeholder="Địa chỉ email"
              />
            </div>

            <div>
              <input
                v-model="form.password"
                id="password"
                name="password"
                type="password"
                autocomplete="current-password"
                required
                @focus="clearError"
                @input="clearError"
                class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
                placeholder="Mật khẩu"
              />
            </div>
            <p v-if="error" class="text-red-500 text-xs mt-1">
              {{ error }}
            </p>
          </div>

          <div class="flex items-center justify-between text-xs">
            <div class="flex items-center">
              <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                class="h-3.5 w-3.5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-1.5 block text-white">
                Ghi nhớ tôi
              </label>
            </div>

            <div>
              <a
                href="/"
                class="font-medium text-indigo-400 hover:text-indigo-300"
              >
                Về trang chủ
              </a>
            </div>
          </div>

          <div>
            <button
              type="submit"
              class="group relative w-full flex justify-center py-2 px-3 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500"
            >
              Đăng nhập
            </button>
          </div>
        </form>

        <p class="mt-3 text-center text-xs text-gray-200">
          Chưa có tài khoản?
          <a
            href="#"
            id="show-register"
            class="font-medium text-indigo-400 hover:text-indigo-300"
            @click.prevent="toggleForm(false)"
          >
            Đăng ký ngay
          </a>
        </p>
      </div>

      <!-- Form Đăng ký -->
      <div v-else id="register-form">
        <h2 class="text-3xl font-semibold text-center text-white">Đăng Ký</h2>
        <form class="mt-6 space-y-4" @submit.prevent="handleRegister">
          <div class="space-y-3">
            <input
              v-model="registerForm.name"
              key="name"
              type="text"
              required
              @focus="clearError"
              @input="clearError"
              class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
              placeholder="Họ và tên"
            />
            <input
              v-model="registerForm.email"
              key="email"
              type="email"
              autocomplete="email"
              required
              @focus="clearError"
              @input="clearError"
              class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
              placeholder="Địa chỉ email"
            />
            <input
              v-model="registerForm.password"
              key="password"
              type="password"
              required
              @focus="clearError"
              @input="clearError"
              class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
              placeholder="Mật khẩu"
            />
            <input
              v-model="registerForm.password_confirmation"
              key="password_confirmation"
              type="password"
              required
              @focus="clearError"
              @input="clearError"
              class="appearance-none rounded-md block w-full px-2.5 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-xs bg-white/80"
              placeholder="Xác nhận mật khẩu"
            />
          </div>

          <p v-if="error" class="text-red-500 text-xs mt-1">
            {{ error }}
          </p>

          <div>
            <button
              type="submit"
              class="group relative w-full flex justify-center py-2 px-3 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500"
            >
              Đăng ký
            </button>
          </div>
        </form>

        <p class="mt-3 text-center text-xs text-gray-200">
          Đã có tài khoản?
          <a
            href="#"
            id="show-login"
            class="font-medium text-indigo-400 hover:text-indigo-300"
            @click.prevent="toggleForm(true)"
          >
            Đăng nhập
          </a>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import useAuth from "@/composables/useAuth";
import useAxios from "@/composables/useAxios";

const form = reactive({
  email: "",
  password: "",
});

const registerForm = reactive({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const error = ref("");
const router = useRouter();
const { login, user } = useAuth();
const api = useAxios();

const bgImage = "/img/anh3.webp";
const isLoginForm = ref(true);
const toggleForm = (showLogin) => {
  isLoginForm.value = showLogin;
  error.value = ""; // reset lỗi khi chuyển form
};

// 🧹 Hàm xoá lỗi khi người dùng thao tác lại
const clearError = () => {
  error.value = "";
};

// 🟢 Đăng nhập
const handleLogin = async () => {
  error.value = "";
  try {
    const res = await login({ form });

    if (!res.status) {
      error.value = res.message || "Đăng nhập thất bại";
      return;
    }

    const userData = res.user;

    // 🧠 Kiểm tra trạng thái tài khoản
    if (userData?.status === "Inactive") {
      alert("Bạn đã được chấp thuận rời KTX. Tài khoản của bạn hiện không còn hoạt động.");
      return;
    }

    // 🧭 Điều hướng theo vai trò
    switch (userData?.role) {
      case "admin":
      case "staff":
        await navigateTo("/admin");
        break;
      case "student":
        await navigateTo("/student");
        break;
      case "parent":
        await navigateTo("/parent");
        break;
      default:
        await navigateTo("/");
        break;
    }
  } catch (err) {
    error.value =
      err.response?.data?.message || "Đã có lỗi xảy ra trong quá trình đăng nhập";
  }
};

// 🟢 Đăng ký
const handleRegister = async () => {
  error.value = "";

  if (registerForm.password !== registerForm.password_confirmation) {
    error.value = "Mật khẩu xác nhận không khớp!";
    return;
  }

  const payload = {
    name: registerForm.name,
    email: registerForm.email,
    password: registerForm.password,
    password_confirmation: registerForm.password_confirmation,
    role: "Student",
  };

  try {
    const res = await api.post("/registerstudents", payload);

    if (res.data?.status) {
      isLoginForm.value = true;
      error.value = "";
      registerForm.name = "";
      registerForm.email = "";
      registerForm.password = "";
      registerForm.password_confirmation = "";
      alert("Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.");
    } else {
      error.value = res.data?.message || "Đăng ký thất bại";
    }
  } catch (err) {
    error.value =
      err.response?.data?.message || "Đã có lỗi xảy ra trong quá trình đăng ký";
  }
};
</script>
