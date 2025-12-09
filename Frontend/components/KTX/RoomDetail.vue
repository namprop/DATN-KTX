<template>
  <div
    v-if="show"
    @click.self="emit('close')"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white w-full max-w-3xl rounded-2xl shadow-xl overflow-hidden animate-fadeIn"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-cyan-500 to-blue-500"
      >
        <h2 class="text-white font-semibold text-lg flex items-center gap-2">
          <i class="fa-solid fa-door-closed"></i>
          Chi tiết phòng {{ room?.room_code || "" }}
        </h2>
        <button
          @click="emit('close')"
          class="text-white hover:bg-white/20 rounded-full p-2 transition"
          title="Đóng"
        >
          <i class="fa-solid fa-xmark text-xl"></i>
        </button>
      </div>

      <!-- Nội dung -->
      <div class="p-6 space-y-6">
        <!-- Thông tin phòng -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p class="text-gray-500 text-sm">Số phòng</p>
            <p class="font-semibold text-gray-800">{{ room?.room_code }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Sức chứa</p>
            <p class="font-semibold text-gray-800">{{ room?.capacity }}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Mô tả</p>
            <p class="font-semibold text-gray-800">{{ room?.description === "Female Dormitory" ? "Phòng Nữ" : "Phòng Nam"}}</p>
          </div>
          <div>
            <p class="text-gray-500 text-sm">Giá thuê/người/tháng</p>
            <p class="font-semibold text-cyan-600">
              {{ Number(room?.price).toLocaleString("vi-VN") }}đ
            </p>
          </div>
        </div>

        <!-- Danh sách sinh viên -->
        <div>
          <h3
            class="font-semibold text-gray-800 mb-3 flex items-center gap-2"
          >
            <i class="fa-solid fa-users text-cyan-600"></i>
            Người đang ở ({{ room?.students?.length || 0 }}/{{
              room?.capacity
            }})
          </h3>

          <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 rounded-lg">
              <thead class="bg-gray-100">
                <tr>
                  <th
                    class="px-4 py-2 text-left text-sm font-semibold text-gray-700"
                  >
                    Họ tên
                  </th>
                  <th
                    class="px-4 py-2 text-left text-sm font-semibold text-gray-700"
                  >
                    MSSV
                  </th>
                  <th
                    class="px-4 py-2 text-left text-sm font-semibold text-gray-700"
                  >
                    Phòng
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr
                  v-for="student in room?.students"
                  :key="student.id"
                  class="hover:bg-gray-50"
                >
                  <td class="px-4 py-2 text-sm text-gray-800">
                    {{ student.full_name }}
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-800">
                    {{ student.student_code }}
                  </td>
                  <td class="px-4 py-2 text-sm text-gray-800">
                    {{ room.room_code }}
                  </td>
                </tr>

                <tr
                  v-if="!room?.students || room.students.length === 0"
                  class="text-center"
                >
                  <td colspan="3" class="py-3 text-gray-500 italic">
                    Chưa có sinh viên nào trong phòng.
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex justify-end px-6 py-4 border-t border-gray-200 bg-gray-50"
      >
        <button
          @click="emit('close')"
          class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition font-medium"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  room: Object,
});

const emit = defineEmits(["close"]);
</script>
