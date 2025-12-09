<template>
  <div class="bg-white shadow-sm py-2 border-b border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="flex space-x-6 md:space-x-10 text-gray-600 font-semibold text-sm md:text-base"
      >
        <a href="#" class="py-2 px-1 border-b-2 border-blue-600 text-blue-600"
          >Trang chủ</a
        >
        <a
          href="/student/contract"
          class="py-2 px-1 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-500 transition-colors"
          >Thông tin hợp đồng</a
        >
        <!-- <a
          href="#"
          class="py-2 px-1 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-500 transition-colors"
          >Đăng ký phòng</a
        > -->
        <a
          href="/student/payments"
          class="py-2 px-1 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-500 transition-colors"
          >Hóa đơn</a
        >
        <!-- <a
          href="#"
          class="py-2 px-1 border-b-2 border-transparent hover:border-blue-500 hover:text-blue-500 transition-colors"
          >Hỗ trợ</a
        > -->
      </div>
    </div>
  </div>

  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <div class="max-w-7xl mx-auto">
      <section
        class="relative rounded-xl shadow-lg p-6 md:p-10 mb-12 overflow-hidden"
        style="background-image: linear-gradient(to right, #2563eb, #3b82f6)"
      >
        <div class="absolute inset-0 bg-black opacity-30 rounded-xl"></div>
        <div
          class="relative z-10 flex flex-col md:flex-row items-center justify-between"
        >
          <div class="text-white text-center md:text-left mb-6 md:mb-0">
            <ClientOnly>
              <h1
                class="text-3xl md:text-4xl font-extrabold mb-2 leading-tight"
              >
                Chào mừng {{ user.name }}
              </h1>
            </ClientOnly>

            <p class="text-lg md:text-xl font-medium">
              Chúc bạn một ngày học tập và sinh hoạt hiệu quả.
            </p>
          </div>
          <div class="w-full md:w-auto">
            <div class="relative w-full md:w-72">
              <input
                type="text"
                placeholder="Tìm kiếm thông tin..."
                class="w-full pl-10 pr-4 py-2 rounded-full bg-white/90 text-gray-700 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <svg
                class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-500"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
            </div>
          </div>
        </div>
      </section>

      <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-8">
        DASHBOARD
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <!-- Phòng của tôi -->
        <div
          class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-start transition-transform hover:scale-105 hover:shadow-xl duration-300 border-t-4 border-blue-500"
        >
          <div class="flex items-center gap-3 mb-4">
            <svg
              class="w-8 h-8 text-blue-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l2-2m0 0l7-7 7 7M5 10v10h14V10"
              ></path>
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Phòng của tôi</h3>
          </div>
          <!-- <p class="text-gray-600 text-sm">
            Tòa nhà: <span class="font-semibold">A</span>
          </p> -->
          <!-- <p class="text-gray-600 text-sm">
            Phòng số: <span class="font-semibold"></span>
          </p>
          <p class="text-gray-600 text-sm mb-4">
            Bạn cùng phòng:
            <span class="font-semibold">Lê Văn B, Trần Thị C</span>
          </p> -->
          <button
            @click="openRoomDetail"
            class="mt-auto px-5 py-2 text-sm font-semibold text-blue-600 border border-blue-600 rounded-full hover:bg-blue-600 hover:text-white transition-colors"
          >
            Chi tiết phòng
          </button>
        </div>
        <StudentRoomDetail
          :show="showRoomDetail"
          :room="roomDetail"
          @close="closeRoomDetail"
        />

        <!-- Hóa đơn -->

        <!-- <div
          class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-start transition-transform hover:scale-105 hover:shadow-xl duration-300 border-t-4 border-green-500"
        >
          <div class="flex items-center gap-3 mb-4">
            <svg
              class="w-8 h-8 text-green-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5h5l5 5h5v9a2 2 0 01-2 2z"
              ></path>
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Hóa đơn gần nhất</h3>
          </div>
          <p class="text-gray-600 text-sm">Tiền phòng T10/2025</p>
          <p class="text-gray-600 text-sm">
            Số tiền:
            <span class="font-semibold text-red-600">1.200.000 VNĐ</span>
          </p>
          <p class="text-gray-600 text-sm mb-4">
            Hạn cuối: <span class="font-semibold">31/10/2025</span>
          </p>
          <a
            href="#"
            class="mt-auto px-5 py-2 text-sm font-semibold text-white bg-green-500 rounded-full hover:bg-green-600 transition-colors"
            >Thanh toán ngay</a
          >
        </div> -->

        <!-- Hóa đơn gần nhất -->
        <div
          class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-start transition-transform hover:scale-105 hover:shadow-xl duration-300 border-t-4 border-green-500"
        >
          <div class="flex items-center gap-3 mb-4">
            <svg
              class="w-8 h-8 text-green-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5h5l5 5h5v9a2 2 0 01-2 2z"
              ></path>
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Hóa đơn gần nhất</h3>
          </div>

          <template v-if="latestPayment">
            <p class="text-gray-600 text-sm">
              Tiền phòng T{{ latestPayment.month }}/{{ latestPayment.year }}
            </p>
            <p class="text-gray-600 text-sm">
              Số tiền:
              <span class="font-semibold text-red-600">{{
                formatCurrency(latestPayment.total_amount)
              }}</span>
            </p>
            <p class="text-gray-600 text-sm mb-4">
              Trạng thái:
              <span
                :class="{
                  'text-green-600 font-semibold':
                    latestPayment.payment_status === 'paid',
                  'text-yellow-500 font-semibold':
                    latestPayment.payment_status === 'pending',
                  'text-red-500 font-semibold':
                    latestPayment.payment_status === 'unpaid',
                }"
              >
                {{
                  latestPayment.payment_status === "paid"
                    ? "Đã thanh toán"
                    : latestPayment.payment_status === "pending"
                    ? "Đang xử lý"
                    : "Chưa thanh toán"
                }}
              </span>
            </p>

            <button
              v-if="latestPayment.payment_status !== 'paid'"
              @click="goToPayments"
              class="mt-auto px-5 py-2 text-sm font-semibold text-white bg-green-500 rounded-full hover:bg-green-600 transition-colors"
            >
              Xem chi tiết
            </button>
          </template>

          <p v-else class="text-gray-500 text-sm">Chưa có hóa đơn nào.</p>
        </div>

        <!-- Thông khi click vào chi tiết hợp đồng  -->

        <div
          class="bg-white rounded-xl shadow-lg p-6 flex flex-col items-start transition-transform hover:scale-105 hover:shadow-xl duration-300 border-t-4 border-purple-500"
        >
          <div class="flex items-center gap-3 mb-4">
            <svg
              class="w-8 h-8 text-purple-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 20h9M3 20h9M5 8h14M5 12h14M9 16h6"
              />
            </svg>
            <h3 class="text-lg font-bold text-gray-800">Chi tiết hợp đồng</h3>
          </div>

          <p class="text-gray-600 text-sm mb-4">
            Xem thông tin chi tiết hợp đồng lưu trú, thời hạn và điều khoản của
            bạn.
          </p>
          <button
            @click="goToContractDetail"
            class="mt-auto px-5 py-2 text-sm font-semibold text-purple-600 border border-purple-600 rounded-full hover:bg-purple-600 hover:text-white transition-colors"
          >
            Xem hợp đồng
          </button>
        </div>
      </div>

      <section class="mb-12">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">
          Tiện ích Ký túc xá
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
          <!-- Nút Báo cáo thiết bị -->
          <a
            href="#"
            @click.prevent="openReportFacilities"
            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg transition-transform hover:scale-105 hover:shadow-xl duration-300"
          >
            <svg
              class="w-12 h-12 text-primary-blue mb-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
              ></path>
            </svg>
            <span class="text-sm font-semibold text-dark-text"
              >Báo cáo thiết bị</span
            >
          </a>

          <!-- Modal danh sách thiết bị -->
          <transition name="fade">
            <div
              v-if="showFacilitiesModal"
              class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            >
              <div
                class="bg-white rounded-xl shadow-xl w-full max-w-3xl p-6 relative animate-fadeIn overflow-y-auto max-h-[80vh]"
              >
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                  Danh sách thiết bị phòng
                </h2>
                <button
                  @click="showFacilitiesModal = false"
                  class="absolute top-3 right-3 text-gray-500 hover:text-gray-800"
                >
                  Đóng
                </button>

                <div v-if="facilities.length > 0" class="space-y-3">
                  <div
                    v-for="facility in facilities"
                    :key="facility.id"
                    class="flex justify-between items-center p-3 border rounded-lg"
                  >
                    <div>
                      <p class="font-semibold">{{ facility.facility_name }}</p>
                      <p class="text-sm text-gray-500">
                        Mã: {{ facility.facility_code }}
                      </p>
                      <p
                        class="text-sm"
                        :class="
                          facility.status === 'broken'
                            ? 'text-red-500'
                            : 'text-green-600'
                        "
                      >
                        Trạng thái:
                        {{ facility.status === "broken" ? "Hỏng" : "Tốt" }}
                      </p>
                    </div>
                    <button
                      v-if="facility.status !== 'broken'"
                      @click="reportFacility(facility)"
                      class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600"
                    >
                      Báo cáo hỏng
                    </button>
                  </div>
                </div>

                <p v-else class="text-gray-500 text-sm">
                  Chưa có thiết bị nào trong phòng.
                </p>
              </div>
            </div>
          </transition>

          <transition name="fade">
            <div
              v-if="showDepartureForm"
              class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
            >
              <div
                class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 relative animate-fadeIn"
              >
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                  Yêu cầu rời KTX
                </h2>

                <form
                  @submit.prevent="submitDepartureRequest"
                  class="space-y-4"
                >
                  <!-- <div>
                    <label
                      class="block text-sm font-semibold text-gray-700 mb-1"
                      >Mã sinh viên</label
                    >
                    <input
                      type="text"
                      v-model="departureForm.student_code"
                      readonly
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 cursor-not-allowed"
                    />
                  </div> -->

                  <div>
                    <label
                      class="block text-sm font-semibold text-gray-700 mb-1"
                      >Lý do rời</label
                    >
                    <textarea
                      v-model="departureForm.reason"
                      rows="3"
                      placeholder="Nhập lý do..."
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    ></textarea>
                  </div>

                  <div>
                    <label
                      class="block text-sm font-semibold text-gray-700 mb-1"
                      >Ngày rời dự kiến</label
                    >
                    <input
                      type="text"
                      v-model="departureForm.request_date"
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"
                    />
                  </div>

                  <div class="flex justify-end space-x-3 mt-6">
                    <button
                      type="button"
                      @click="closeDepartureForm"
                      class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
                    >
                      Hủy
                    </button>
                    <button @click="submitDepartureRequest">Gửi đơn</button>
                  </div>
                </form>
              </div>
            </div>
          </transition>

          <!-- <a
            href="#"
            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg transition-transform hover:scale-105 hover:shadow-xl duration-300"
          >
            <svg
              class="w-12 h-12 text-primary-blue mb-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M4 6h16M4 12h16m-7 6h7"
              ></path>
            </svg>
            <span class="text-sm font-semibold text-dark-text">Góp ý</span>
          </a> -->
          <a
            href="#"
            v-if="!departureRequested"
            @click.prevent="openDepartureForm"
            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-lg transition-transform hover:scale-105 hover:shadow-xl duration-300"
          >
            <svg
              class="w-12 h-12 text-red-500 mb-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"
              />
            </svg>
            <span class="text-sm font-semibold text-dark-text"
              >Yêu cầu rời KTX</span
            >
          </a>

          <!-- Nút chờ phê duyệt -->
          <div
            v-else
            class="flex flex-col items-center p-6 bg-gray-100 rounded-xl shadow-md cursor-not-allowed"
          >
            <svg
              class="w-12 h-12 text-gray-400 mb-3"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"
              />
            </svg>
            <span class="text-sm font-semibold text-gray-500"
              >Đang chờ phê duyệt</span
            >
          </div>

          <!-- Các tiện ích khác giữ nguyên -->
        </div>
      </section>

      <!-- Liên hệ -->
      <section id="contact" class="mt-24">
        <div
          class="rounded-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-2 shadow-lg border-2 border-cyan-200"
        >
          <div class="p-8 md:p-12 bg-gradient-to-br from-cyan-50 to-white">
            <img
              src="/img/logo.png"
              alt="Logo"
              class="h-20 w-20 mb-6 rounded-full shadow-md border-4 border-cyan-200"
            />
            <h2 class="text-2xl font-bold mb-2 text-cyan-700">
              Liên hệ Ban Quản lý Ký túc xá
            </h2>
            <p class="text-slate-600 mb-6">
              Chúng tôi luôn sẵn sàng hỗ trợ bạn. Vui lòng liên hệ qua các thông
              tin dưới đây.
            </p>

            <ul class="space-y-4 text-slate-700">
              <li class="flex items-start gap-3">
                <span class="text-cyan-600">📍</span>
                <span>[Địa chỉ Ký túc xá], Thanh Oai, Hà Nội</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="text-cyan-600">📞</span>
                <span>024-XXX-YYYY (Văn phòng)</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="text-cyan-600">✉️</span>
                <span>kytucxa@thanhhoaia.edu.vn</span>
              </li>
              <li class="flex items-center gap-3">
                <span class="text-cyan-600">🕓</span>
                <span>Giờ làm việc: 7h30 - 17h00 (Thứ 2 - Thứ 6)</span>
              </li>
            </ul>
          </div>

          <div
            class="p-8 md:p-12 bg-gradient-to-br from-cyan-600 to-blue-700 text-white"
          >
            <h3 class="text-xl font-bold mb-4 text-cyan-200">
              Cẩm Nang Ký Túc Xá
            </h3>
            <ul class="space-y-3">
              <li>
                <a
                  href="#"
                  class="flex items-center gap-3 text-white hover:text-cyan-200 transition-all"
                >
                  📘 <span>Nội quy & Quy định chung KTX</span>
                </a>
              </li>
              <li>
                <a
                  href="#"
                  class="flex items-center gap-3 text-white hover:text-cyan-200 transition-all"
                >
                  💳 <span>Hướng dẫn thanh toán chi phí nội trú</span>
                </a>
              </li>
              <li>
                <a
                  href="#"
                  class="flex items-center gap-3 text-white hover:text-cyan-200 transition-all"
                >
                  ❓ <span>Các câu hỏi thường gặp (FAQs)</span>
                </a>
              </li>
              <li>
                <a
                  href="#"
                  class="flex items-center gap-3 text-white hover:text-cyan-200 transition-all"
                >
                  ⚠️ <span>Báo cáo sự cố & Yêu cầu sửa chữa</span>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: "student",
  middleware: "auth",
});

const router = useRouter();

const { user } = useAuth();
import useAxios from "@/composables/useAxios";

const api = useAxios();

const showRoomDetail = ref(false);
const roomDetail = ref([]);
const latestPayment = ref(null);

const goToContractDetail = () => {
  navigateTo("/student/contract"); // chuyển sang trang contract.vue
};

onMounted(async () => {
  try {
    // 1️⃣ Kiểm tra hóa đơn quá hạn
    const { data: paymentData } = await api.get("/student/checkoverduepayments");

    if (paymentData.status && paymentData.overdue_count && paymentData.overdue_count > 0) {
      alert(`Bạn có ${paymentData.overdue_count} hóa đơn đã quá hạn thanh toán không thì sẽ vi phạm!`);
    }

    if (paymentData.status && paymentData.locked) {
      alert(paymentData.message);
      router.push('/'); // '/locked' là trang chỉ hiển thị nút login
      return; // tránh check hợp đồng nếu tài khoản bị khóa do hóa đơn
    }

    // 2️⃣ Kiểm tra hợp đồng quá hạn
    const { data: contractData } = await api.get("/student/checkcontractstatus");

    if (contractData.status && contractData.alert) {
      alert(contractData.alert);
    }

    if (contractData.status && contractData.locked) {
      alert("Tài khoản của bạn bị hạn chế thao tác do hợp đồng quá hạn > 45 ngày.");
      router.push('/student/contract'); // '/locked' là trang chỉ hiển thị nút login
      return;
    }
  } catch (error) {
    console.error("Lỗi khi kiểm tra hóa đơn hoặc hợp đồng:", error);
  }
});



// onMounted(async () => {
//   try {
//     const { data } = await api.get("/student/checkoverduepayments");

//     // Thông báo hóa đơn quá hạn
//     if (data.status && data.overdue_count && data.overdue_count > 0) {
//       alert(`Bạn có ${data.overdue_count} hóa đơn đã quá hạn thanh toán!`);
//     }

//     // Nếu bị khóa → redirect sang trang “chỉ có login”
//     if (data.status && data.locked) {
//       alert(data.message);
//       router.push('/'); // '/locked' là trang hiển thị nút login
//     }
//   } catch (error) {
//     console.error("Lỗi khi kiểm tra hóa đơn:", error);
//   }
// });

// onMounted(async () => {
//   try {
//     const { data } = await api.get("/student/checkcontractstatus");

//     // Thông báo hợp đồng quá hạn
//     if (data.status && data.alert) {
//       alert(data.alert);
//     }

//     // Nếu bị khóa → redirect sang trang “chỉ có login”
//     if (data.status && data.locked) {
//       alert("Tài khoản của bạn bị hạn chế thao tác do hợp đồng quá hạn > 2 tháng.");
//       router.push('/'); // '/locked' là trang hiển thị nút login
//     }
//   } catch (error) {
//     console.error("Lỗi khi kiểm tra hợp đồng:", error);
//   }
// });

// Gọi hàm khi trang load
// onMounted(() => {
//   checkOverduePayments();
// });

onMounted(async () => {
  try {
    const { data } = await api.get("/student/showmypayments"); // gọi route showMyPayments

    if (data.status && data.payments.length > 0) {
      // lấy hóa đơn mới nhất (theo year, month)
      latestPayment.value = data.payments[0];
    }
  } catch (error) {
    console.error("Lỗi khi tải hóa đơn:", error);
  }
});

const formatCurrency = (num) => {
  if (!num) return "0 VNĐ";
  return Number(num).toLocaleString("vi-VN") + " VNĐ";
};

const goToPayments = () => {
  // chuyển hướng đến trang hóa đơn chi tiết
  navigateTo("/student/payments");
};

const openRoomDetail = async () => {
  showRoomDetail.value = true;
  try {
    // ✅ Gọi đúng API không có room_id
    const { data } = await api.get(`/student/showroomstudent`);

    if (data.status) {
      roomDetail.value = data.room;
    } else {
      console.warn("Không tìm thấy phòng:", data.message);
      roomDetail.value = null;
    }
  } catch (error) {
    console.error("Lỗi khi tải chi tiết phòng:", error);
    roomDetail.value = null;
  }
};

const closeRoomDetail = () => {
  showRoomDetail.value = false;
};

const showDepartureForm = ref(false);
const departureRequested = ref(false);
const departureForm = reactive({
  student_code: "",
  reason: "",
  request_date: "",
});

const openDepartureForm = () => {
  departureForm.student_code = user.value?.student_code || "";
  showDepartureForm.value = true;
};

const closeDepartureForm = () => {
  showDepartureForm.value = false;
};

// === Thay đổi chính: gộp logic gửi yêu cầu rời KTX + check backend ===
const isSubmitting = ref(false);

const submitDepartureRequest = async () => {
  if (isSubmitting.value) return;
  isSubmitting.value = true;

  try {
    const res = await api.post("/student/postdeparturerequest", departureForm);

    if (res.data.need_confirm) {
      const confirmExit = confirm(res.data.message);
      if (confirmExit) {
        const confirmRes = await api.post("/student/postdeparturerequest", {
          ...departureForm,
          confirm_exit: true,
        });
        alert(confirmRes.data.message);
        departureRequested.value = true;
        showDepartureForm.value = false;
      } else {
        alert("Bạn đã hủy yêu cầu rời ký túc xá.");
      }
      return;
    }

    if (res.data.alert) {
      alert(res.data.message);
      return;
    }

    alert(res.data.message);
    departureRequested.value = true;
    showDepartureForm.value = false;
  } catch (err) {
    console.error(err);
    alert(err.response?.data?.message || "Có lỗi xảy ra, vui lòng thử lại.");
  } finally {
    isSubmitting.value = false;
  }
};

// === Tiện ích phòng ===
const showFacilitiesModal = ref(false);
const facilities = ref([]);

const openReportFacilities = async () => {
  showFacilitiesModal.value = true;
  try {
    const { data } = await api.get("/student/showroomfacilities");
    if (data.status) {
      facilities.value = data.facilities;
    } else {
      alert(data.message || "Không thể tải danh sách thiết bị");
      facilities.value = [];
    }
  } catch (err) {
    console.error(err);
    alert("Lỗi khi tải danh sách thiết bị!");
  }
};

const reportFacility = async (facility) => {
  const confirmed = confirm(
    `Bạn có chắc chắn muốn báo cáo thiết bị "${facility.facility_name}" hỏng?`
  );
  if (!confirmed) return;

  try {
    const { data } = await api.post("/student/reportfacility", {
      facility_id: facility.id,
    });
    if (data.status) {
      alert(data.message);
      // Cập nhật trạng thái thiết bị trên frontend
      facility.status = "broken";
    } else {
      alert(data.message || "Báo cáo thất bại");
    }
  } catch (err) {
    console.error(err);
    alert("Lỗi khi gửi báo cáo!");
  }
};
</script>
