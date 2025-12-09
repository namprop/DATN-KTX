<template>
  <div class="font-sans bg-gradient-to-br from-slate-50 to-blue-50 text-slate-900 min-h-screen">
    <!-- HEADER -->
    <header class="bg-white shadow-md sticky top-0 z-50 border-b-2 border-cyan-200">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <!-- LOGO -->
          <a href="#" class="flex items-center gap-3">
            <img
              src="/img/logo.png"
              alt="Logo THPT Thanh Oai A"
              class="h-16 w-16 rounded-full border-2 border-cyan-300 shadow-md"
            />
            <div class="hidden sm:block">
              <span class="text-xl font-bold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">
                THPT THANH OAI A
              </span>
              <span class="block text-sm font-semibold text-cyan-600">
                Hệ Thống KÝ TÚC XÁ
              </span>
            </div>
          </a>

          <!-- USER MENU -->
          <div class="flex items-center gap-4">
            <ClientOnly>
              <div v-if="user" class="relative group">
                <button
                  class="flex items-center focus:outline-none py-2 px-4 rounded-lg hover:bg-cyan-50 transition-colors"
                >
                  <!-- 🟢 Avatar chữ viết tắt thay cho ảnh -->
                  <div
                    class="h-10 w-10 rounded-full flex items-center justify-center text-white font-semibold text-sm mr-2 border-2 border-cyan-300 bg-gradient-to-r"
                    :class="getRandomColor()"
                  >
                    {{ getInitials(user?.name) }}
                  </div>

                  <span class="font-semibold text-slate-800 hidden sm:block">
                    Chào, {{ user.name }}
                  </span>
                  <svg
                    class="w-5 h-5 text-slate-600 ml-1"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 9l-7 7-7-7"
                    ></path>
                  </svg>
                </button>

                <div
                  class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-200 border border-cyan-200"
                >
                  <hr class="my-1 border-cyan-100" />
                  <a
                    href="#"
                    @click.prevent="handleLogout"
                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-semibold"
                  >
                    <i class="fa-solid fa-sign-out-alt mr-2"></i> Đăng xuất
                  </a>
                </div>
              </div>
            </ClientOnly>
          </div>
        </div>
      </div>
    </header>

    <!-- MAIN CONTENT -->
    <slot />

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-400 mt-16 border-t-2 border-cyan-600">
      <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <p class="font-semibold text-white mb-2">© 2025 Trường THPT Thanh Oai A</p>
          <p class="text-sm">
            Hệ Thống Quản Lý Ký Túc Xá | Phát triển bởi Ban Công Nghệ Thông Tin
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// 🟢 Tự động import useAuth() từ composables
const { user, logout } = useAuth()

const handleLogout = async () => {
  const userRole = user.value?.role
  await logout(userRole)
  await navigateTo('/login')
}

// 🧩 Hàm tạo chữ viết tắt
const getInitials = (name) => {
  if (!name || typeof name !== 'string') return '??'
  const words = name.trim().split(/\s+/)
  const lastTwo = words.slice(-2).map(w => w[0].toUpperCase())
  return lastTwo.join('')
}

// 🎨 Hàm tạo màu ngẫu nhiên
const getRandomColor = () => {
  const colors = [
    // 'from-cyan-400 to-blue-500',
    // 'from-pink-400 to-rose-500',
    // 'from-emerald-400 to-green-500',
    // 'from-indigo-400 to-purple-500',
    // 'from-amber-400 to-orange-500',
    'from-sky-400 to-teal-500',
  ]
  return colors[Math.floor(Math.random() * colors.length)]
}
</script>
