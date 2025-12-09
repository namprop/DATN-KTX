<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">
        Cập nhật trạng thái thanh toán
      </h3>

      <!-- Chọn trạng thái -->
      <div class="space-y-2">
        <label for="status" class="text-sm font-medium text-gray-700"
          >Trạng thái</label
        >
        <select
          id="status"
          v-model="status"
          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
        >
          <option value="">-- Chọn trạng thái --</option>
          <option value="paid">Đã thanh toán</option>
          <option value="pending">Đang xử lý</option>
          <option value="unpaid">Chưa thanh toán</option>
        </select>
      </div>

      <!-- Nút hành động -->
      <div class="flex justify-end mt-6 gap-3">
        <button
          @click="onClose"
          class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition"
        >
          Hủy
        </button>
        <button
          @click="handleSubmit"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
        >
          Lưu
        </button>
      </div>

      <p v-if="error" class="text-red-500 text-sm mt-3 text-center">
        {{ error }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
  show: Boolean,
  payment: Object,
});
const emit = defineEmits(["close", "submit"]);

const status = ref("");
const error = ref("");

watch(
  () => props.payment,
  (val) => {
    if (val) {
      // đảm bảo props.payment tồn tại trước khi set
      status.value = val.payment_status ?? "";
      error.value = "";
    }
  },
  { immediate: true }
);

const onClose = () => {
  error.value = "";
  emit("close");
};

const handleSubmit = () => {
  if (!status.value) {
    error.value = "Vui lòng chọn trạng thái thanh toán";
    return;
  }

  // đảm bảo props.payment có payment_id
  const paymentId = props.payment?.payment_id ?? props.payment?.id;
  if (!paymentId) {
    error.value = "Không xác định được bản ghi thanh toán.";
    return;
  }

  emit("submit", {
    payment_id: paymentId,
    payment_status: status.value,
  });
};
</script>
