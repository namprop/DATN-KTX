export default defineNuxtRouteMiddleware((to, from) => {
  const token = useCookie('auth-token').value
  const user = useCookie('auth-user').value 
  const userRole = user?.role
  const isAuthenticated = !!(token && user)

  const isAuthPage = to.path === '/login'
  const isAdminArea = to.path.startsWith('/admin')
  const isStudentArea = to.path.startsWith('/student')
  const isParentArea = to.path.startsWith('/parent')

  // --- 1. Nếu chưa đăng nhập ---
  if (!isAuthenticated) {
    // Nếu cố vào bất kỳ khu vực cần đăng nhập (admin, student, parent)
    if (isAdminArea || isStudentArea || isParentArea) {
      return navigateTo('/login')
    }
    // Trang công khai thì để yên
    return
  }

  // --- 2. Nếu đã đăng nhập ---
  if (isAuthPage) {
    // Tùy vai trò, chuyển hướng đúng dashboard
    if (userRole === 'admin' || userRole === 'staff') return navigateTo('/admin')
    if (userRole === 'student') return navigateTo('/student')
    if (userRole === 'parent') return navigateTo('/parent')
  }

  // --- 3. Kiểm tra truy cập theo role ---
  if (isAdminArea && !(userRole === 'admin' || userRole === 'staff')) {
    return navigateTo('/')
  }

  if (isStudentArea && userRole !== 'student') {
    return navigateTo('/')
  }

  if (isParentArea && userRole !== 'parent') {
    return navigateTo('/')
  }

  // --- 4. Cho phép nếu đúng quyền ---
  return
})





// export default defineNuxtRouteMiddleware((to, from) => {
//   // Lấy thông tin xác thực từ cookie
//   const token = useCookie('auth-token').value
//   const user = useCookie('auth-user').value

//   // Lấy vai trò (role) một cách an toàn
//   const userRole = user?.role
//   // Kiểm tra xem đã đăng nhập hay chưa
//   const isAuthenticated = !!(token && user)

//   const isAuthPage = to.path === '/login'
//   const isAdminArea = to.path.startsWith('/admin')

//   // --- 1. XỬ LÝ KHI CHƯA ĐĂNG NHẬP ---
//   if (!isAuthenticated) {
//     // Nếu chưa đăng nhập mà cố vào /admin
//     if (isAdminArea) {
//       // Bắt buộc quay về trang login
//       return navigateTo('/login')
//     }
//     // Nếu vào trang login hoặc các trang công khai khác (/, /about)
//     // thì cứ để yên (return không làm gì cả)
//     return
//   }

//   // --- 2. XỬ LÝ KHI ĐÃ ĐĂNG NHẬP ---

//   // Nếu đã đăng nhập mà quay lại trang login
//   if (isAuthPage) {
//     // Tự động chuyển hướng về trang dashboard
//     return navigateTo('/admin')
//   }

//   // Nếu đã đăng nhập và đang cố vào khu vực /admin
//   if (isAdminArea) {
//     // Kiểm tra vai trò: Phải là 'admin' HOẶC 'staff'
//     if (userRole === 'admin' || userRole === 'staff') {
//       // OK, đúng quyền, cho phép truy cập
//       return
//     } else {
//       // Vai trò khác (ví dụ: student) cố vào /admin
//       // Chuyển hướng về trang chủ hoặc trang "Không có quyền"
//       return navigateTo('/')
//     }
//   }

//   // Các trang công khai khác (/, /about...)
//   // Đã đăng nhập, cứ để họ xem
//   return
// })

