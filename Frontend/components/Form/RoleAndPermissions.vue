<template>

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
                      <option value="student_affairs">
                        Công tác sinh viên
                      </option>
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
                      <span
                        class="ml-2 text-sm text-gray-700 flex items-center"
                      >
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
                      <span
                        class="ml-2 text-sm text-gray-700 flex items-center"
                      >
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
                      <span class="ml-2 text-xs text-gray-700"
                        >Xem báo cáo</span
                      >
                    </label>
                  </div>
                </div>
              </div>
            </div>

</template>