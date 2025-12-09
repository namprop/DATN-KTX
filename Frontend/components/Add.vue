<template>
  <body
    class="bg-gray-900 bg-opacity-50 min-h-screen flex items-center justify-center p-4"
  >
    <!-- Modal Form -->
    <div
      class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
    >
      <!-- Header -->
      <div
        class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between"
      >
        <div class="flex items-center space-x-3">
          <div class="bg-blue-100 p-2 rounded-lg">
            <i class="fas fa-user-plus text-blue-600 text-lg"></i>
          </div>
          <div>
            <h2 class="text-xl font-bold text-gray-800">Thêm người dùng mới</h2>
            <p class="text-xs text-gray-500">Điền thông tin để tạo tài khoản</p>
          </div>
        </div>
        <button class="text-gray-400 hover:text-gray-600 transition-colors">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Form Content -->
      <div class="px-6 py-4">
        <form class="space-y-5">
          <!-- Account Information -->
          <div class="border-b border-gray-200 pb-4">
            <h3
              class="text-sm font-semibold text-gray-700 mb-3 flex items-center"
            >
              <i class="fas fa-user-circle mr-2 text-blue-600"></i>
              Thông tin tài khoản
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Tên đăng nhập <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-user text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="text"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="username123"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Email <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-envelope text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="email"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="user@ktx.edu.vn"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="password"
                    class="w-full pl-9 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="••••••••"
                  />
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <i class="fas fa-eye text-sm"></i>
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Xác nhận mật khẩu <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-lock text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="password"
                    class="w-full pl-9 pr-10 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="••••••••"
                  />
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                  >
                    <i class="fas fa-eye text-sm"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="border-b border-gray-200 pb-4">
            <h3
              class="text-sm font-semibold text-gray-700 mb-3 flex items-center"
            >
              <i class="fas fa-id-card mr-2 text-blue-600"></i>
              Thông tin cá nhân
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Họ và tên <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-signature text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="text"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Nguyễn Văn A"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Số điện thoại <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-phone text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="tel"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="0987654321"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Ngày sinh
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-calendar text-gray-400 text-sm"></i>
                  </div>
                  <input
                    type="date"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Giới tính
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-venus-mars text-gray-400 text-sm"></i>
                  </div>
                  <select
                    class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none"
                  >
                    <option value="">Chọn giới tính</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                  </select>
                  <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                  </div>
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Địa chỉ
                </label>
                <div class="relative">
                  <div class="absolute top-2.5 left-0 pl-3 pointer-events-none">
                    <i class="fas fa-map-marker-alt text-gray-400 text-sm"></i>
                  </div>
                  <textarea
                    rows="2"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                    placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Role & Permissions -->
          <div class="pb-4">
            <h3
              class="text-sm font-semibold text-gray-700 mb-3 flex items-center"
            >
              <i class="fas fa-user-shield mr-2 text-blue-600"></i>
              Vai trò & Quyền hạn
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Vai trò <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-user-tag text-gray-400 text-sm"></i>
                  </div>
                  <select
                    class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none"
                  >
                    <option value="">Chọn vai trò</option>
                    <option value="admin">Quản trị viên</option>
                    <option value="staff">Nhân viên</option>
                    <option value="accountant">Kế toán</option>
                    <option value="manager">Quản lý</option>
                  </select>
                  <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">
                  Phòng ban
                </label>
                <div class="relative">
                  <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-building text-gray-400 text-sm"></i>
                  </div>
                  <select
                    class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none"
                  >
                    <option value="">Chọn phòng ban</option>
                    <option value="admin">Hành chính</option>
                    <option value="student_affairs">Công tác sinh viên</option>
                    <option value="finance">Tài chính</option>
                    <option value="maintenance">Bảo trì</option>
                  </select>
                  <div
                    class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                  >
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                  </div>
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-2">
                  Trạng thái tài khoản
                </label>
                <div class="flex items-center space-x-6">
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      name="status"
                      value="active"
                      checked
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                      <i
                        class="fas fa-check-circle text-green-600 mr-1 text-xs"
                      ></i>
                      Hoạt động
                    </span>
                  </label>
                  <label class="flex items-center cursor-pointer">
                    <input
                      type="radio"
                      name="status"
                      value="inactive"
                      class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                    />
                    <span class="ml-2 text-sm text-gray-700 flex items-center">
                      <i
                        class="fas fa-times-circle text-red-600 mr-1 text-xs"
                      ></i>
                      Tạm khóa
                    </span>
                  </label>
                </div>
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-2">
                  Quyền truy cập
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                  <label
                    class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-xs text-gray-700"
                      >Quản lý học sinh</span
                    >
                  </label>
                  <label
                    class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-xs text-gray-700"
                      >Quản lý phòng</span
                    >
                  </label>
                  <label
                    class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-xs text-gray-700"
                      >Quản lý thanh toán</span
                    >
                  </label>
                  <label
                    class="flex items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                  >
                    <input
                      type="checkbox"
                      class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                    <span class="ml-2 text-xs text-gray-700">Xem báo cáo</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="pb-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">
              Ghi chú
            </label>
            <textarea
              rows="2"
              class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
              placeholder="Thêm ghi chú về người dùng này (tùy chọn)..."
            ></textarea>
          </div>
        </form>
      </div>

      <!-- Footer Buttons -->
      <div
        class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4"
      >
        <div class="flex flex-col sm:flex-row gap-2">
          <button
            class="flex-1 bg-white border border-gray-300 text-gray-700 px-4 py-2.5 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm flex items-center justify-center"
          >
            <i class="fas fa-redo mr-2 text-xs"></i>
            Làm mới
          </button>
          <button
            class="flex-1 bg-gray-600 text-white px-4 py-2.5 rounded-lg hover:bg-gray-700 transition-colors font-medium text-sm flex items-center justify-center"
          >
            <i class="fas fa-times mr-2 text-xs"></i>
            Hủy bỏ
          </button>
          <button
            class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg hover:bg-blue-700 transition-colors font-medium text-sm flex items-center justify-center shadow-lg"
          >
            <i class="fas fa-save mr-2 text-xs"></i>
            Lưu người dùng
          </button>
        </div>
      </div>
    </div>
    
  </body>
</template>
