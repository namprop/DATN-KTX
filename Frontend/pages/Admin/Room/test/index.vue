<template>
    <!-- Modal - Chi tiết phòng -->
<div
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
        <i class="fa-solid fa-door-closed"></i> Chi tiết phòng 101
      </h2>
      <button
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
          <p class="font-semibold text-gray-800">101</p>
        </div>
        <div>
          <p class="text-gray-500 text-sm">Tầng</p>
          <p class="font-semibold text-gray-800">Tầng 1</p>
        </div>
        <div>
          <p class="text-gray-500 text-sm">Loại phòng</p>
          <p class="font-semibold text-gray-800">Phòng Nam</p>
        </div>
        <div>
          <p class="text-gray-500 text-sm">Giá thuê/tháng</p>
          <p class="font-semibold text-cyan-600">500.000đ</p>
        </div>
      </div>

      <!-- Danh sách người ở -->
      <div>
        <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
          <i class="fa-solid fa-users text-cyan-600"></i> Người đang ở (6/6)
        </h3>

        <div class="overflow-x-auto">
          <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                  Họ tên
                </th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                  MSSV
                </th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                  Giới tính
                </th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                  SĐT
                </th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">
                  Ngày vào
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-800">Nguyễn Văn A</td>
                <td class="px-4 py-2 text-sm text-gray-800">SV001</td>
                <td class="px-4 py-2 text-sm text-gray-800">
                  <i class="fa-solid fa-mars text-blue-500"></i> Nam
                </td>
                <td class="px-4 py-2 text-sm text-gray-800">0912345678</td>
                <td class="px-4 py-2 text-sm text-gray-800">01/09/2025</td>
              </tr>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-800">Trần Văn B</td>
                <td class="px-4 py-2 text-sm text-gray-800">SV002</td>
                <td class="px-4 py-2 text-sm text-gray-800">
                  <i class="fa-solid fa-mars text-blue-500"></i> Nam
                </td>
                <td class="px-4 py-2 text-sm text-gray-800">0987654321</td>
                <td class="px-4 py-2 text-sm text-gray-800">03/09/2025</td>
              </tr>
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm text-gray-800">Phạm Văn C</td>
                <td class="px-4 py-2 text-sm text-gray-800">SV003</td>
                <td class="px-4 py-2 text-sm text-gray-800">
                  <i class="fa-solid fa-mars text-blue-500"></i> Nam
                </td>
                <td class="px-4 py-2 text-sm text-gray-800">0909009999</td>
                <td class="px-4 py-2 text-sm text-gray-800">10/09/2025</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="flex justify-end px-6 py-4 border-t border-gray-200 bg-gray-50">
      <button
        class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition font-medium"
      >
        Đóng
      </button>
    </div>
  </div>
</div>

</template>