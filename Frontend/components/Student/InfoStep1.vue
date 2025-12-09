<template>
  <div
    class="mx-auto bg-white text-gray-900 p-10 mt-6 shadow-lg rounded-lg font-[Times_New_Roman] print:shadow-none print:rounded-none"
    style="width: 210mm; min-height: 297mm"
  >
    <div class="text-center mt-6 mb-4">
      <p class="font-bold uppercase text-lg underline">
        PHIẾU THÔNG TIN CÁ NHÂN HỌC SINH (Bước 1/2)
      </p>
    </div>

    <div class="mb-4">
      <p>Trường THPT Thanh Oai A</p>
    </div>

    <div class="space-y-3 leading-relaxed">
      <div>
        - Họ và tên học sinh (full_name):
        <input
          v-model="localForm.fullName"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1"
          placeholder="VD: Nguyễn Văn A"
          required
        />
        Giới tính (gender):
        <select
          v-model="localForm.gender"
          class="border-b border-black w-20 focus:outline-none ml-1 bg-transparent"
          required
        >
          <option value="">...</option>
          <option value="Nam">Nam</option>
          <option value="Nữ">Nữ</option>
          <option value="Khác">Khác</option>
        </select>
      </div>

      <div>
        - Ngày tháng năm sinh (date_of_birth):
        <input
          v-model="localForm.dob"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1"
          placeholder="VD: 12/05/2007"
          required
        />
      </div>

      <div>
        - Mã học sinh (student_code):
        <input
          v-model="localForm.studentId"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1"
          placeholder="VD: HS001"
          required
        />
      </div>

      <div>
        - Số điện thoại liên hệ (phone):
        <input
          v-model="localForm.phone"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1"
          placeholder="VD: 0901234567"
          required
        />
      </div>

      <div>
        - Email liên hệ (email):
        <input
          v-model="localForm.email"
          type="text"
          class="border-b border-black w-1/2 focus:outline-none ml-1 bg-gray-100"
          placeholder="VD: nguyenvana@gmail.com"
          readonly
        />
      </div>
    </div>

    <div class="text-right mr-10 mt-6">
      .........., ngày..... tháng..... năm.....
    </div>

    <div class="text-right mr-10 mt-6">
      <p class="font-semibold uppercase">Học sinh lập phiếu</p>
      <p class="italic">(Ký và ghi rõ họ tên)</p>
    </div>

    <div class="mt-12 text-center flex justify-center gap-4 print:hidden">
      <button
        @click="$emit('back')"
        class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 transition"
      >
        ⬅️ Quay lại
      </button>
      <button
        @click="$emit('next', localForm)"
        class="bg-green-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-green-700 transition"
      >
        Tiếp theo ➡️
      </button>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

// Nhận dữ liệu form từ cha
const props = defineProps({
  formData: {
    type: Object,
    required: true
  }
})

// Gửi sự kiện khi người dùng nhấn nút
const emit = defineEmits(['next', 'back'])

// Dùng reactive copy để chỉnh mà không phá vỡ reactivity của cha
const localForm = reactive({ ...props.formData })

// Cập nhật ngược lại khi người dùng thay đổi
watch(localForm, (newVal) => {
  Object.assign(props.formData, newVal)
})
</script>
