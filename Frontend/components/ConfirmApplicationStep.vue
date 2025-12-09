<template>
  <div
    class="mx-auto bg-white text-gray-900 p-10 mt-6 shadow-lg rounded-lg font-[Times_New_Roman] print:shadow-none print:rounded-none"
    style="width: 210mm; min-height: 297mm"
  >
    <!-- HEADER -->
    <div class="text-center">
      <p class="font-bold uppercase">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
      <p class="font-semibold">Độc lập - Tự do - Hạnh phúc</p>
      <div class="w-32 h-px bg-black mx-auto my-2"></div>
    </div>

    <div class="text-center mt-6 mb-4">
      <p class="font-bold uppercase text-lg underline">
        ĐƠN XIN VÀO Ở KÝ TÚC XÁ (Bước 3/3)
      </p>
    </div>

    <!-- BODY -->
    <div class="mb-4">
      <p>
        <span class="font-semibold">
          Kính gửi: Trường trung học phổ thông Thanh Oai A
        </span>
      </p>
    </div>

    <div class="space-y-3 leading-relaxed">
      <div>
        - Họ và tên học sinh:
        <input
          v-model="localForm.fullName"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        Mã số học sinh:
        <input
          v-model="localForm.studentId"
          type="text"
          class="border-b border-black w-1/4 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
      </div>

      <div>
        - Ngày tháng năm sinh:
        <input
          v-model="localForm.dob"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        Giới tính:
        <input
          v-model="localForm.gender"
          class="border-b border-black w-20 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
      </div>

      <!-- Phòng đăng ký -->
      <div>
        - Phòng đăng ký:
        <input
          :value="selectedRoom?.code || ''"
          type="text"
          class="border-b border-black w-1/4 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        ({{ selectedRoom?.area || '---' }} - {{ selectedRoom?.type || '---' }})
      </div>
    </div>

    <p class="mt-6 leading-relaxed indent-8">
      Nay em làm đơn này mong muốn được
      <span class="font-semibold">Ban giám hiệu Trường THPT Thanh Oai A</span>
      xem xét tạo điều kiện cho em được ở Ký túc xá bắt đầu từ ngày
      <input
        v-model="localForm.startDate"
        type="text"
        class="border-b border-black w-40 focus:outline-none mx-1"
        placeholder="VD: 01/09/2025"
      />
      đến ngày
      <input
        v-model="localForm.endDate"
        type="text"
        class="border-b border-black w-40 focus:outline-none mx-1"
        placeholder="VD: 31/05/2026"
      />.
      Nếu được chấp thuận và bố trí chỗ ở, em xin cam kết thực hiện tốt Quy chế
      học sinh nội trú của Bộ Giáo dục và Đào tạo, cũng như nội quy Ký túc xá
      của Trường.
    </p>

    <div class="text-right mr-10 mt-6">
      .........., ngày..... tháng..... năm.....
    </div>

    <!-- SIGNATURE AREA -->
    <div class="grid grid-cols-2 gap-6 mt-5 text-center text-sm">
      <div>
        <p class="font-semibold uppercase">
          Xác nhận của ban quản lý ký túc xá
        </p>
        <p class="italic">(Thuộc trường THPT Thanh Oai A)</p>
      </div>
      <div>
        <p class="font-semibold uppercase">Người làm đơn</p>
        <p class="italic">(Ký và ghi rõ họ tên)</p>
      </div>
    </div>

    <!-- BUTTONS -->
    <div class="mt-12 text-center flex justify-center gap-4 print:hidden">
      <button
        @click="$emit('back')"
        class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 transition"
      >
        ⬅️ Quay lại
      </button>

      <button
        @click="submitForm"
        class="bg-green-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-green-700 transition"
      >
        ✅ Hoàn tất & Nộp đơn
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue';

const props = defineProps({
  formData: { type: Object, required: true },
  selectedRoom: { type: Object, required: true }
});

const emit = defineEmits(['back', 'submit']);

// 🔹 Tạo reactive copy từ props để dùng trong template
const localForm = reactive({ ...props.formData });

// 🔹 Khi localForm thay đổi, đồng bộ lại vào props.formData
watch(localForm, (newVal) => {
  Object.assign(props.formData, newVal);
});

// 🔹 Nộp đơn
function submitForm() {
  // 1️⃣ Validation
  if (!localForm.startDate || !localForm.endDate) {
    alert('⚠️ Vui lòng nhập đầy đủ ngày bắt đầu và ngày kết thúc.');
    return;
  }

  if (!props.selectedRoom?.id) {
    alert('⚠️ Vui lòng chọn phòng trước khi nộp đơn.');
    return;
  }

  // 2️⃣ Tạo payload gửi lên parent
  const payload = {
    fullName: localForm.fullName,
    studentId: localForm.studentId,
    dob: localForm.dob,
    gender: localForm.gender,
    phone: localForm.phone,
    email: localForm.email,
    startDate: localForm.startDate,
    endDate: localForm.endDate,
    room_id: props.selectedRoom.id
  };

  // 3️⃣ Emit payload
  emit('submit', payload);
}
</script>
