<template>
          <!-- Info Cards Section -->
      <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
        <!-- Benefits Card -->
        <div
          class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl shadow-lg p-8 border-2 border-cyan-200"
        >
          <h3
            class="text-2xl font-bold text-cyan-700 mb-4 flex items-center gap-2"
          >
            <i class="fa-solid fa-star text-yellow-500"></i> Lợi ích của KTX
          </h3>
          <ul class="space-y-3">
            <li class="flex items-start gap-3">
              <span class="text-cyan-600 font-bold text-lg">✓</span>
              <span class="text-slate-700"
                >Môi trường sống an toàn, thân thiện và có tổ chức</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-cyan-600 font-bold text-lg">✓</span>
              <span class="text-slate-700"
                >Các tiện ích hiện đại: WiFi, điều hòa, nước nóng</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-cyan-600 font-bold text-lg">✓</span>
              <span class="text-slate-700"
                >Giá cả hợp lý, thanh toán linh hoạt theo từng học kỳ</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-cyan-600 font-bold text-lg">✓</span>
              <span class="text-slate-700"
                >Hỗ trợ 24/7 từ Ban Quản lý Ký túc xá</span
              >
            </li>
          </ul>
        </div>

        <!-- Rules Card -->
        <div
          class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-2xl shadow-lg p-8 border-2 border-blue-200"
        >
          <h3
            class="text-2xl font-bold text-blue-700 mb-4 flex items-center gap-2"
          >
            <i class="fa-solid fa-book text-red-500"></i> Quy định chung
          </h3>
          <ul class="space-y-3">
            <li class="flex items-start gap-3">
              <span class="text-blue-600 font-bold text-lg">•</span>
              <span class="text-slate-700"
                >Tuân thủ nội quy KTX và các quy định của nhà trường</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-blue-600 font-bold text-lg">•</span>
              <span class="text-slate-700"
                >Giữ gìn vệ sinh phòng và các tiện ích chung</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-blue-600 font-bold text-lg">•</span>
              <span class="text-slate-700"
                >Không gây tiếng ồn hoặc ảnh hưởng đến sinh viên khác</span
              >
            </li>
            <li class="flex items-start gap-3">
              <span class="text-blue-600 font-bold text-lg">•</span>
              <span class="text-slate-700"
                >Tham gia các hoạt động cộng đồng KTX</span
              >
            </li>
          </ul>
        </div>
      </section>

      <!-- Support Section -->
      <section
        class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl shadow-xl p-8 md:p-12 text-white"
      >
        <div class="text-center mb-8">
          <h2
            class="text-3xl font-bold mb-3 flex items-center justify-center gap-2"
          >
            <i class="fa-solid fa-headset"></i> Bạn cần hỗ trợ?
          </h2>
          <p class="text-lg text-cyan-100">
            Nếu có bất kỳ thắc mắc nào trong quá trình đăng ký, đừng ngần ngại
            liên hệ với chúng tôi hoặc xem các câu hỏi thường gặp.
          </p>
        </div>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
          <a
            href="#"
            class="px-8 py-3 font-bold text-cyan-600 bg-white rounded-full hover:bg-gray-100 transition-all transform hover:scale-105 text-center shadow-lg"
          >
            <i class="fa-solid fa-circle-question mr-2"></i> Câu hỏi thường gặp
          </a>
          <a
            href="#"
            class="px-8 py-3 font-bold text-white bg-slate-800 rounded-full hover:bg-slate-900 transition-all transform hover:scale-105 text-center shadow-lg"
          >
            <i class="fa-solid fa-envelope mr-2"></i> Liên hệ BQL KTX
          </a>
        </div>
      </section>
</template>