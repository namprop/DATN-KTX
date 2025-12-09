<template>
  <div class="max-w-5xl mx-auto">
    <!-- Phần giới thiệu -->
    <section
      class="bg-gradient-to-br from-white to-cyan-50 rounded-2xl shadow-lg p-8 md:p-12 border-2 border-cyan-200 mb-12"
    >
      <div class="flex items-center gap-6">
        <div
          class="hidden md:flex w-20 h-20 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-2xl items-center justify-center flex-shrink-0"
        >
          <i class="fa-solid fa-home text-white text-3xl"></i>
        </div>
        <div class="flex-1">
          <h1
            class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-2"
          >
            Chào mừng bạn đến với KTX!
          </h1>
          <p class="text-lg text-slate-700 leading-relaxed">
            Có vẻ như bạn chưa đăng ký phòng ở cho học kỳ này. Hãy bắt đầu hành
            trình của mình bằng cách đăng ký ngay hôm nay.
          </p>
        </div>
      </div>

      <button
        @click="$emit('next')"
        class="mt-8 inline-block px-12 py-4 text-lg font-bold text-white bg-gradient-to-r from-cyan-600 to-blue-600 rounded-full hover:from-cyan-700 hover:to-blue-700 transition-all transform hover:scale-105 shadow-xl hover:shadow-2xl"
      >
        <i class="fa-solid fa-door-open mr-2"></i> ĐĂNG KÝ NGAY
      </button>
    </section>

    <!-- Quy trình đăng ký -->
    <section class="mb-12">
      <h2 class="text-3xl font-bold text-slate-800 mb-2 text-center">
        Quy trình đăng ký
      </h2>
      <p class="text-center text-slate-600 mb-10 text-lg">
        Đơn giản, nhanh chóng và an toàn
      </p>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="relative pt-6">
          <div
            class="bg-white rounded-2xl shadow-lg p-8 border-l-4 border-cyan-500 opacity-75"
          >
            <div class="flex items-center gap-4 mb-4">
              <div
                class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 text-white font-bold text-2xl rounded-full flex items-center justify-center shadow-md"
              >
                <i class="fa-solid fa-pen"></i>
              </div>
              <h3 class="text-xl font-bold text-slate-800">Điền thông tin</h3>
            </div>
            <p class="text-slate-600 leading-relaxed">
              Cập nhật và hoàn thiện hồ sơ cá nhân của bạn.
            </p>
          </div>
        </div>

        <div class="relative pt-6">
          <div
            class="bg-white rounded-2xl shadow-lg p-8 border-l-4 border-blue-500 opacity-75"
          >
            <div class="flex items-center gap-4 mb-4">
              <div
                class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 text-white font-bold text-2xl rounded-full flex items-center justify-center shadow-md"
              >
                <i class="fa-solid fa-bed"></i>
              </div>
              <h3 class="text-xl font-bold text-slate-800">Chọn phòng</h3>
            </div>
            <p class="text-slate-600 leading-relaxed">
              Lựa chọn tòa và phòng bạn muốn đăng ký ở.
            </p>
          </div>
        </div>

        <div class="relative pt-6">
          <div
            class="bg-white rounded-2xl shadow-lg p-8 border-l-4 border-purple-500 opacity-75"
          >
            <div class="flex items-center gap-4 mb-4">
              <div
                class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 text-white font-bold text-2xl rounded-full flex items-center justify-center shadow-md"
              >
                <i class="fa-solid fa-file-signature"></i>
              </div>
              <h3 class="text-xl font-bold text-slate-800">Ký đơn/Hợp đồng</h3>
            </div>
            <p class="text-slate-600 leading-relaxed">
              Xác nhận đơn xin vào ở ký túc xá theo quy định.
            </p>
          </div>
        </div>

        <div class="relative pt-6">
          <div
            class="bg-white rounded-2xl shadow-lg p-8 border-l-4 border-green-500 opacity-75"
          >
            <div class="flex items-center gap-4 mb-4">
              <div
                class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-2xl rounded-full flex items-center justify-center shadow-md"
              >
                <i class="fa-solid fa-check"></i>
              </div>
              <h3 class="text-xl font-bold text-slate-800">Hoàn tất</h3>
            </div>
            <p class="text-slate-600 leading-relaxed">
              Chờ ban quản lý KTX xác nhận và nhận phòng.
            </p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
// Emit để cha điều khiển bước kế tiếp
defineEmits(["next"]);
</script>
