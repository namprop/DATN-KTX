<template>
  <div
    class="max-w-2xl mx-auto text-center bg-white p-12 rounded-2xl shadow-xl"
  >
    <div
      class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6"
    >
      <i class="fa-solid fa-check-double text-5xl text-green-600"></i>
    </div>

    <h2 class="text-3xl font-bold text-green-700">Nộp đơn thành công!</h2>

    <p class="mt-4 text-lg text-gray-700 leading-relaxed">
      Cảm ơn bạn đã hoàn tất hồ sơ đăng ký. Ban quản lý Ký túc xá sẽ xem xét
      đơn của bạn và phản hồi trong thời gian sớm nhất.
    </p>

    <p class="mt-4 text-gray-600">
      Bạn có thể kiểm tra email hoặc mục thông báo để theo dõi trạng thái đơn.
    </p>

    <button
      @click="goHome"
      class="mt-8 px-8 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-full hover:from-cyan-700 hover:to-blue-700 transition-all shadow-lg"
    >
      Về trang chủ
    </button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { onMounted } from 'vue'

const router = useRouter()

// 🟢 Tự động về trang student/page sau vài giây
onMounted(() => {
  setTimeout(() => {
    router.push('/student/page')
  }, 2500)
})

function goHome() {
  router.push('/student/page')
}
</script>
