<template>
  <div class="p-6">
    <button
      @click="payNow"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
    >
      Thanh toán qua VNPAY
    </button>
  </div>
</template>

<script setup>
const { createVNPayPayment } = usePayment()

const payNow = () => {
  createVNPayPayment(150000, "Thanh toán đơn hàng #12345")
}
</script>
