<template>
  <div class="font-sans bg-slate-50">
    <header class="top-0 z-50">
      <div class="w-full relative">
        <ClientOnly>
          <img
            src="/img/123.png"
            alt="Banner trường THPT Thanh Oai A"
            class="w-full h-[180px] object-cover"
          />
        </ClientOnly>

        <div class="absolute inset-0 bg-black/10"></div>

        <div
          class="absolute inset-0 z-10 flex items-center justify-end px-6 sm:px-8 lg:px-16"
        >
          <div class="flex gap-3 sm:gap-4">
            <NuxtLink
              to="/login"
              class="bg-white text-slate-800 px-5 sm:px-7 py-2.5 rounded-lg font-bold text-sm sm:text-base hover:bg-gray-100 transition shadow-lg border-2 border-white flex items-center gap-2"
            >
              <i class="fa-solid fa-sign-in-alt"></i>
              <span class="hidden sm:inline">Đăng Nhập</span>
            </NuxtLink>

            <NuxtLink to="/loginparent">
              <button
                class="bg-red-600 text-white px-5 sm:px-7 py-2.5 rounded-lg font-bold text-sm sm:text-base hover:bg-red-700 transition shadow-lg border-2 border-red-700 flex items-center gap-2"
              >
                <i class="fa-solid fa-users"></i>
                <span class="hidden sm:inline">Đăng ký tài khoản Phụ Huynh</span>
              </button>
            </NuxtLink>
          </div>
        </div>
      </div>
    </header>

    <slot />

    <footer class="bg-slate-800">
      <div
        class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-slate-400 text-sm"
      >
        <p>© 2025 Bản quyền thuộc về Trường THPT Thanh Oai A.</p>
        <p class="mt-1">Phát triển bởi Ban Công nghệ Thông tin nhà trường.</p>
      </div>
    </footer>
  </div>
</template>
