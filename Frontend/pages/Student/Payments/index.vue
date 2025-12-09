<template>
  <div class="min-h-screen bg-gray-50 py-10">
    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-xl p-8">
      <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="w-6 h-6 text-green-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5h5l5 5h5v9a2 2 0 01-2 2z"
          />
        </svg>
        Danh sách hóa đơn
      </h1>

      <div v-if="loading" class="text-gray-500 text-center py-10">
        Đang tải dữ liệu...
      </div>

      <div v-else>
        <table class="w-full border-collapse">
          <thead>
            <tr class="bg-gray-100 text-gray-700 text-sm">
              <th class="py-3 px-4 text-left">Tháng/Năm</th>
              <th class="py-3 px-4 text-left">Số tiền</th>
              <th class="py-3 px-4 text-left">Nội dung</th>
              <th class="py-3 px-4 text-left">Trạng thái</th>
              <!-- <th class="py-3 px-4 text-center">Hành động</th> -->
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(payment, index) in payments"
              :key="index"
              class="border-b hover:bg-gray-50"
            >
              <td class="py-3 px-4">
                <span class="font-semibold text-gray-800">
                  T{{ payment.month }}/{{ payment.year }}
                </span>
              </td>
              <td class="py-3 px-4 text-gray-700">
                {{ formatCurrency(payment.total_amount) }}
              </td>
                 <td class="py-3 px-4 text-gray-700">
                {{ payment.description }}
              </td>
              <td class="py-3 px-4">
                <span
                  :class="{
                    'text-green-600 font-semibold':
                      payment.payment_status === 'paid',
                    'text-red-500 font-semibold':
                      payment.payment_status === 'unpaid',
                  }"
                >
                  {{
                    payment.payment_status === "paid"
                      ? "Đã thanh toán"
                      : "Chưa thanh toán"
                  }}
                </span>
              </td>
              <!-- <td class="py-3 px-4 text-center">
                <button
                  v-if="payment.payment_status !== 'paid'"
                  @click="confirmPayment(payment)"
                  class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2 rounded-full transition"
                >
                  Thanh toán qua VNPAY
                </button>



                <span v-else class="text-gray-400 text-sm italic"
                  >Đã hoàn tất</span
                >
              </td> -->
            </tr>

            <tr v-if="payments.length === 0">
              <td colspan="4" class="text-center py-6 text-gray-500">
                Không có hóa đơn nào
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
      class="pt-10"
      >
        <NuxtLink
          to="/student/page"
          class="text-blue-600 underline hover:text-blue-800"
        >
          Quay lại Trang
        </NuxtLink>
      </div>
    </div>

    <!-- Popup xác nhận -->
    <div
      v-if="showPopup"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl p-6 w-[380px] shadow-xl text-center">
        <h2 class="text-lg font-bold text-gray-800 mb-3">
          Xác nhận thanh toán
        </h2>
        <p class="text-gray-600 mb-6">
          Bạn có chắc muốn thanh toán hóa đơn
          <span class="font-semibold text-green-600">
            T{{ selectedPayment.month }}/{{ selectedPayment.year }}
          </span>
          qua VNPAY không?
        </p>
        <div class="flex justify-center gap-4">
          <button
            @click="showPopup = false"
            class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100"
          >
            Hủy
          </button>
          <button
            @click="payNow"
            class="px-4 py-2 rounded-lg bg-green-500 hover:bg-green-600 text-white font-semibold"
          >
            Xác nhận
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useAxios from "@/composables/useAxios";
import axios from "axios";

const api = useAxios();
const payments = ref([]);
const loading = ref(true);

const showPopup = ref(false);
const selectedPayment = ref(null);

const formatCurrency = (num) => {
  return Number(num || 0).toLocaleString("vi-VN") + " VNĐ";
};

const getPayments = async () => {
  loading.value = true;
  try {
    const { data } = await api.get("/student/showmypayments");
    if (data.status) payments.value = data.payments;
  } catch (err) {
    console.error("Lỗi tải hóa đơn:", err);
  } finally {
    loading.value = false;
  }
};

const confirmPayment = (payment) => {
  selectedPayment.value = payment;
  showPopup.value = true;
};

const payNow = async () => {
  try {
    const paymentId = selectedPayment.value.payment_id;

    // Gọi API backend để tạo link thanh toán
    const res = await axios.post("http://localhost:8000/api/vnpay/create", {
      payment_id: paymentId,
    });

    if (res.data.payment_url) {
      // Redirect người dùng sang trang thanh toán VNPAY
      window.location.href = res.data.payment_url;
    } else {
      alert("Không thể khởi tạo thanh toán VNPAY!");
    }
  } catch (err) {
    console.error("Lỗi khi tạo thanh toán:", err);
    alert("Đã xảy ra lỗi khi kết nối VNPAY!");
  } finally {
    showPopup.value = false;
  }
};

onMounted(() => {
  getPayments();
});
</script>
