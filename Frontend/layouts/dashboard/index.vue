<template>
  <div class="bg-gray-50">
    <div
      v-if="!isSidebarHidden"
      id="overlay"
      class="fixed inset-0 bg-black/40 z-20 md:hidden"
      @click="closeSidebar"
    ></div>

    <aside
      class="fixed left-0 top-0 w-64 bg-white shadow-lg z-30 sidebar md:translate-x-0 flex flex-col h-screen"
      :class="{ 'sidebar-hidden': isSidebarHidden }"
    >
      <div class="p-6 bg-gradient-to-r from-cyan-500 to-blue-500">
        <h1 class="text-2xl font-bold text-white">Trường THPT Thanh Oai A</h1>
        <p class="text-cyan-100 text-sm mt-1">Hệ thống quản lý ký túc xá</p>
      </div>

      <nav class="mt-6 flex-1 overflow-y-auto pb-4">
        <NuxtLink
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center px-6 py-3 transition"
          :class="
            route.path === item.path
              ? 'bg-cyan-50 border-r-4 border-cyan-500 text-cyan-700 font-medium'
              : 'text-gray-600 hover:bg-gray-50'
          "
        >
          <i
            :class="[
              'text-lg mr-3 transition-colors duration-200',
              item.icon,
              route.path === item.path ? 'text-cyan-600' : 'text-gray-500',
            ]"
          ></i>
          {{ item.label }}
        </NuxtLink>
      </nav>

      <div class="p-4 border-t">
        <ClientOnly>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div
                class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center text-white font-semibold flex-shrink-0"
              >
                {{ userInitials }}
              </div>
              <div class="ml-3 truncate">
                <p class="text-sm font-semibold text-gray-700 truncate">
                  {{ user?.name || "Admin" }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                  {{ user?.email || "admin@ktx.com" }}
                </p>
              </div>
            </div>
            <button
              @click="handleLogout"
              class="p-2 rounded-md text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors duration-200"
              title="Đăng xuất"
            >
              <i class="fa-solid fa-right-from-bracket text-lg"></i>
            </button>
          </div>

          <template #fallback>
            <div class="flex items-center space-x-3 animate-pulse">
              <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
              <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
              </div>
            </div>
          </template>
        </ClientOnly>
      </div>
    </aside>

    <div
      class="transition-all duration-300 min-h-screen"
      :class="isSidebarHidden ? 'md:ml-0' : 'md:ml-64'"
    >
      <header class="bg-white shadow-sm sticky top-0 z-10">
        <div class="flex items-center justify-between px-4 py-4 md:px-8">
          <div class="flex items-center">
            <button
              class="text-xl text-gray-600 px-2 mr-3"
              @click="toggleSidebar"
            >
              <i class="fas fa-bars"></i>
            </button>
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">
              {{ pageTitle }}
            </h2>
          </div>

          <div class="flex items-center space-x-4">
            <button class="relative text-gray-600 hover:text-cyan-600">
              <i class="fa-solid fa-bell text-xl"></i>
              <span
                class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"
              ></span>
            </button>
          </div>
        </div>
      </header>

      <main class="p-4 md:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRoute, useRouter } from "vue-router";
import useAuth from "@/composables/useAuth"; // THÊM MỚI: Import useAuth

const route = useRoute();
const router = useRouter(); // THÊM MỚI: Lấy router để điều hướng
const { user, logout } = useAuth(); // THÊM MỚI: Lấy thông tin user và hàm logout

const isSidebarHidden = ref(false);

const toggleSidebar = () => (isSidebarHidden.value = !isSidebarHidden.value);
const closeSidebar = () => (isSidebarHidden.value = true);

const handleResize = () => {
  if (window.innerWidth >= 768) {
    isSidebarHidden.value = false;
  } else {
    isSidebarHidden.value = true;
  }
};

// 🔹 Danh sách menu (Không đổi)
const menuItems = [
  { path: "/admin", label: "Tổng quan", icon: "fa-solid fa-chart-pie" },
  {
    path: "/admin/user",
    label: "Quản lý người dùng",
    icon: "fa-solid fa-users",
  },
  
  {
    path: "/admin/student",
    label: "Quản lý học sinh",
    icon: "fa-solid fa-user-graduate",
  },
  {
    path: "/admin/parent",
    label: "Quản lý phụ huynh",
    icon: "fa-solid fa-user-friends",
  },
  {
    path: "/admin/staff",
    label: "Quản lý Ban Quan Lý Hệ Thống",
    icon: "fa-solid fa-user-tie",
  },
  {
    path: "/admin/room",
    label: "Quản lý phòng",
    icon: "fa-solid fa-door-open",
  },
  {
    path: "/admin/facility",
    label: "Quản lý cơ sở vật chất",
    icon: "fa-solid fa-tools",
  },
  {
    path: "/admin/contracts",
    label: "Quản lý hợp đồng",
    icon: "fa-solid fa-file-lines",
  },
  { path: "/admin/payment", label: "Quản lý thanh toán", icon: "fa-solid fa-credit-card" },
  {
    path: "/admin/departurerequest",
    label: "Đơn xin ra khỏi KTX",
    icon: "fa-solid fa-plane-departure",
  },
  { path: "/admin/schoolstudent", label: "Danh sách học sinh", icon: "fa-solid fa-graduation-cap" },

  { path: "/admin/newpp", label: "Quản Lý Bài Viết", icon: "fa-solid fa-file-lines" },
];

// 🔹 Hiển thị tên trang hiện tại (Không đổi)
const pageTitle = computed(() => {
  const current = menuItems.find((item) => item.path === route.path);
  return current ? current.label : "Tổng quan";
});

// THÊM MỚI: Tạo tên viết tắt cho avatar
const userInitials = computed(() => {
  if (user.value?.name) {
    return user.value.name.charAt(0).toUpperCase();
  }
  if (user.value?.email) {
    return user.value.email.charAt(0).toUpperCase();
  }
  return "AD"; // Giá trị mặc định
});

// THÊM MỚI: Hàm xử lý đăng xuất
const handleLogout = async () => {
  await logout();
  // Chuyển hướng về trang đăng nhập sau khi logout
  router.push("/login");
};

onMounted(() => {
  window.addEventListener("resize", handleResize);
  handleResize();
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
});
</script>

<style scoped>
.sidebar {
  transition: transform 0.3s ease-in-out;
}
.sidebar-hidden {
  transform: translateX(-100%);
}
@media (min-width: 768px) {
  .sidebar {
    position: fixed;
    left: 0;
    top: 0;
    height: 100%;
  }
}
</style>
