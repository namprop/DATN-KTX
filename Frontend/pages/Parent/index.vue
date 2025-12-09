<template>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Loading -->
    <div
      v-if="isLoading"
      class="flex flex-col items-center justify-center min-h-[50vh] text-cyan-600"
    >
      <i class="fa-solid fa-spinner fa-spin text-5xl"></i>
      <p class="mt-4 text-lg font-semibold text-slate-700">
        Đang tải dữ liệu...
      </p>
    </div>

    <!-- Error -->
    <div
      v-else-if="error"
      class="bg-red-100 border-l-4 border-red-500 text-red-700 p-6 rounded-lg shadow-md max-w-3xl mx-auto"
      role="alert"
    >
      <h3 class="font-bold text-xl mb-2">
        <i class="fa-solid fa-circle-exclamation mr-2"></i> Rất tiếc, đã xảy ra
        lỗi
      </h3>
      <p>Không thể tải thông tin. Vui lòng thử lại sau.</p>
      <p class="mt-2 text-sm bg-red-200 p-2 rounded font-mono text-red-900">
        {{ error }}
      </p>
    </div>

    <!-- Dashboard -->
    <div v-else-if="parent" class="max-w-7xl mx-auto">
      <!-- Header -->
      <section
        class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-2xl shadow-lg p-6 md:p-8 mb-8 text-white"
      >
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold mb-2">
              Xin chào, {{ parent.name }}! 👋
            </h1>
            <p class="text-cyan-100">
              Cập nhật thông tin học sinh và thanh toán KTX
            </p>
          </div>
          <div class="hidden md:block">
            <i
              class="fa-solid fa-house-user text-white text-6xl opacity-30"
            ></i>
          </div>
        </div>
      </section>

      <!-- Student Info -->
      <section class="mb-8">
        <h2
          class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2"
        >
          <i class="fa-solid fa-user-graduate text-cyan-600"></i>
          Thông Tin Con Em
        </h2>

        <div
          v-if="student"
          class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border-2 border-cyan-200"
        >
          <div class="flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0 text-center">
              <!-- Avatar -->
              <div v-if="student.avatar" class="relative">
                <img
                  :src="student.avatar"
                  alt="Ảnh học sinh"
                  class="w-32 h-32 rounded-full object-cover border-4 border-cyan-300 shadow-lg mx-auto mb-3"
                />
              </div>

              <!-- Avatar fallback: chữ viết tắt -->
              <div
                v-else
                class="w-32 h-32 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto mb-3 border-4 border-cyan-300 shadow-lg bg-gradient-to-r"
                :class="getColorByName(student.name)"
              >
                {{ getInitials(student.name) }}
              </div>

              <!-- Trạng thái -->
              <span
                :class="
                  student.status === 'Active'
                    ? 'bg-green-100 text-green-700'
                    : 'bg-gray-100 text-gray-700'
                "
                class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
              >
                <i
                  :class="
                    student.status === 'Active'
                      ? 'fa-solid fa-circle-check'
                      : 'fa-solid fa-circle-info'
                  "
                ></i>
                {{
                  student.status === "Active" ? "Đang ở KTX" : student.status
                }}
              </span>
            </div>

            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-slate-500 mb-1">Họ và tên</p>
                <p class="text-lg font-bold text-slate-800">
                  {{ student.name }}
                </p>
              </div>
              <div>
                <p class="text-sm text-slate-500 mb-1">Mã số sinh viên</p>
                <p class="text-lg font-bold text-slate-800">
                  {{ student.student_code }}
                </p>
              </div>
              <div>
                <p class="text-sm text-slate-500 mb-1">Số điện thoại</p>
                <p class="text-lg font-bold text-slate-800">
                  {{ student.phone || "Chưa cập nhật" }}
                </p>
              </div>
              <div>
                <p class="text-sm text-slate-500 mb-1">Phòng ở</p>
                <p class="text-lg font-bold text-cyan-600">
                  {{ room.building }} - {{ room.name }}
                </p>
              </div>
              <div>
                <p class="text-sm text-slate-500 mb-1">Sức chứa phòng</p>
                <p class="text-lg font-bold text-slate-800">
                  {{ room.capacity }} người
                </p>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="bg-white rounded-2xl shadow-lg p-6 md:p-8 border-2 border-yellow-300 text-center"
        >
          <p class="text-yellow-700 font-semibold">
            Tài khoản này chưa được liên kết với thông tin học sinh.
          </p>
        </div>
      </section>

      <!-- Statistics -->
      <section class="mb-8">
        <h2
          class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2"
        >
          <i class="fa-solid fa-chart-line text-cyan-600"></i>
          Tổng Quan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-red-500 hover:shadow-xl transition-all"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center"
              >
                <i
                  class="fa-solid fa-exclamation-triangle text-red-600 text-xl"
                ></i>
              </div>
              <span class="text-3xl font-bold text-red-600">{{
                stats.unpaidCount
              }}</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">
              Hóa đơn chưa thanh toán
            </h3>
            <p class="text-sm text-slate-600">
              Tổng:
              <span class="font-bold text-red-600">{{
                stats.unpaidAmountFormatted
              }}</span>
            </p>
          </div>

          <div
            class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center"
              >
                <i class="fa-solid fa-check-circle text-green-600 text-xl"></i>
              </div>
              <span class="text-3xl font-bold text-green-600">{{
                stats.paidCount
              }}</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Đã thanh toán</h3>
            <p class="text-sm text-slate-600">
              Tổng:
              <span class="font-bold text-green-600">{{
                stats.paidAmountFormatted
              }}</span>
            </p>
          </div>

          <div
            class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all"
          >
            <div class="flex items-center justify-between mb-4">
              <div
                class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center"
              >
                <i class="fa-solid fa-calendar-days text-blue-600 text-xl"></i>
              </div>
              <span class="text-3xl font-bold text-blue-600">{{
                stats.totalMonths
              }}</span>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">
              Tổng số hóa đơn
            </h3>
            <p class="text-sm text-slate-600">Đã phát hành từ trước đến nay</p>
          </div>
        </div>
      </section>

      <!-- Payments -->
      <section class="mb-8">
        <h2
          class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2"
        >
          <i class="fa-solid fa-file-invoice-dollar text-cyan-600"></i>
          Hóa Đơn Thanh Toán
        </h2>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead
                class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white"
              >
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold">
                    Mã hóa đơn
                  </th>
                  <th class="px-6 py-4 text-left text-sm font-semibold">
                    Tháng/Năm
                  </th>
                  <th class="px-6 py-4 text-left text-sm font-semibold">
                    Nội dung
                  </th>
                  <th class="px-6 py-4 text-left text-sm font-semibold">
                    Số tiền
                  </th>
                  <th class="px-6 py-4 text-left text-sm font-semibold">
                    Trạng thái
                  </th>
                  <th class="px-6 py-4 text-center text-sm font-semibold">
                    Thao tác
                  </th>
                </tr>
              </thead>

              <tbody class="divide-y divide-gray-200">
                <tr v-if="payments.length === 0">
                  <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                    <i class="fa-solid fa-file-circle-xmark text-3xl mb-2"></i>
                    <p>Chưa có hóa đơn nào được phát hành.</p>
                  </td>
                </tr>

                <tr
                  v-for="payment in payments"
                  :key="payment.id"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="px-6 py-4 text-sm font-semibold text-slate-800">
                    {{ payment.payment_code }}
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-700">
                    {{ payment.month }}/{{ payment.year }}
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-700">
                    {{ payment.description }}
                  </td>
                  <td class="px-6 py-4 text-sm font-bold text-slate-900">
                    {{ payment.total_amount_formatted }}
                  </td>

                  <!-- Status -->
                  <td class="px-6 py-4">
                    <span
                      v-if="payment.status === 'refund_pending'"
                      class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                    >
                      <i class="fa-solid fa-circle-xmark"></i>
                      Trả lại tiền hoàn
                    </span>

                    <span
                      v-else-if="payment.status === 'unpaid'"
                      class="bg-green-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                    >
                      <i class="fa-solid fa-circle-check"></i>
                      Đã thanh toán
                    </span>

                    <span
                      v-else-if="payment.status === 'paid'"
                      class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                    >
                      <i class="fa-solid fa-circle-check"></i>
                      Đã thanh toán
                    </span>

                    <span
                      v-else
                      class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-1 w-fit"
                    >
                      <i class="fa-solid fa-spinner fa-spin"></i>
                      Đang xử lý
                    </span>
                  </td>

                  <!-- Actions -->
                  <td class="px-6 py-4 text-center">
                    <button
                      v-if="payment.status === 'unpaid'"
                      @click="handlePayment(payment.id)"
                      class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:from-cyan-700 hover:to-blue-700 transition shadow-md"
                    >
                      <i class="fa-solid fa-credit-card mr-1"></i> Thanh toán
                    </button>

                    <button
                      v-else-if="payment.status === 'paid'"
                      disabled
                      class="bg-green-100 text-green-700 px-5 py-2 rounded-lg font-semibold cursor-not-allowed"
                    >
                      <i class="fa-solid fa-circle-check mr-1"></i> Đã thanh
                      toán
                    </button>

                    <button
                      v-else-if="payment.status === 'refund_pending'"
                      @click="handleReturnPayment(payment.id)"
                      class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:from-cyan-700 hover:to-blue-700 transition shadow-md"
                    >
                      <i class="fa-solid fa-credit-card mr-1"></i> Nhận lại tiền
                      hoàn
                    </button>

                    <button
                      v-else-if="payment.status === 'refund_pending'"
                      @click="handlePayment(payment.id)"
                      class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white px-5 py-2 rounded-lg font-bold hover:from-cyan-700 hover:to-blue-700 transition shadow-md"
                    >
                      <i class="fa-solid fa-credit-card mr-1"></i> Thanh toán
                    </button>

                    <button
                      v-else
                      disabled
                      class="bg-gray-200 text-gray-500 px-5 py-2 rounded-lg font-bold cursor-not-allowed"
                    >
                      <i class="fa-solid fa-spinner fa-spin mr-1"></i> Đang xử
                      lý
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import useAxios from "@/composables/useAxios";

definePageMeta({
  layout: "parent",
});

const api = useAxios();
const isLoading = ref(true);
const error = ref(null);
const parent = ref(null);
const student = ref(null);
const room = ref(null);
const payments = ref([]);

// Gọi API dashboard
async function fetchData() {
  isLoading.value = true;
  try {
    const res = await api.get("/parent/dashboard");
    if (res.data.status) {
      parent.value = res.data.parent;
      student.value = res.data.student;
      room.value = res.data.room;
      payments.value = res.data.payments;
    } else throw new Error(res.data.message);
  } catch (err) {
    error.value = err.response?.data?.message || err.message;
  } finally {
    isLoading.value = false;
  }
}

// Format tiền
const formatCurrency = (value) =>
  new Intl.NumberFormat("vi-VN", {
    style: "currency",
    currency: "VND",
  }).format(value || 0);

// Thống kê
const stats = computed(() => {
  const unpaid = payments.value.filter((p) => p.status === "unpaid");
  const refund_pending = payments.value.filter(
    (p) => p.status === "refund_pending"
  );
  const paid = payments.value.filter((p) => p.status === "paid");
  return {
    unpaidCount: unpaid.length,
    paidCount: paid.length,
    totalMonths: payments.value.length,
    unpaidAmountFormatted: formatCurrency(
      unpaid.reduce((t, p) => t + p.total_amount, 0)
    ),
    paidAmountFormatted: formatCurrency(
      paid.reduce((t, p) => t + p.total_amount, 0)
    ),
  };
});

// Thanh toán
async function handlePayment(id) {
  try {
    const res = await api.post("/vnpay/create", { payment_id: id });
    if (res.data.payment_url) window.location.href = res.data.payment_url;
    else alert("Không thể khởi tạo thanh toán VNPAY!");
  } catch (e) {
    console.error(e);
    alert("Lỗi khi kết nối VNPAY!");
  }
}

onMounted(fetchData);

async function handleReturnPayment(id) {
  if (!confirm("Bạn có chắc muốn nhận lại tiền hoàn và xóa tài khoản không?"))
    return;

  try {
    const res = await api.post(`/parent/receive-refund/${id}`);
    if (res.data.status) {
      alert(res.data.message);
      // Xóa thông tin cục bộ & chuyển hướng về trang chủ
      window.location.href = "/";
    } else {
      alert(res.data.message);
    }
  } catch (e) {
    console.error(e);
    alert("Lỗi khi xử lý hoàn trả!");
  }
}

// 🎯 Lấy chữ viết tắt tên học sinh
const getInitials = (name) => {
  if (!name || typeof name !== "string") return "??";
  const parts = name.trim().split(/\s+/);
  const initials = parts.slice(-2).map((p) => p[0].toUpperCase());
  return initials.join("");
};

// 🎨 Sinh màu gradient ổn định theo tên
const getColorByName = (text) => {
  const colors = [
    // "from-cyan-400 to-blue-500",
    // "from-pink-400 to-rose-500",
    // "from-emerald-400 to-green-500",
    // "from-indigo-400 to-purple-500",
    // "from-amber-400 to-orange-500",
    "from-sky-400 to-teal-500",
  ];
  if (!text) return colors[0];
  let hash = 0;
  for (let i = 0; i < text.length; i++) {
    hash = text.charCodeAt(i) + ((hash << 5) - hash);
  }
  return colors[Math.abs(hash) % colors.length];
};
</script>
