import useAxios from "@/composables/useAxios"

export default function useAuth() {
  const user = useState('auth-user', () => null)
  const token = useState('auth-token', () => null)
  const api = useAxios()

  // 🟢 Khi chạy ở client, khôi phục user + token từ localStorage
  if (import.meta.client) {
    const savedUser = localStorage.getItem('auth-user')
    const savedToken = localStorage.getItem('auth-token')

    if (savedUser) user.value = JSON.parse(savedUser)
    if (savedToken) {
      token.value = savedToken
      api.defaults.headers.common['Authorization'] = `Bearer ${savedToken}`
    }
  }

  // 🟩 LOGIN
  // 🟩 LOGIN
  // ... bên trong file useAuth ...

  const login = async ({ form }) => {
    try {
      const res = await api.post("/login", form)

      if (res.data.status) {
        // ✨ SỬA Ở ĐÂY: Lấy user data và chuẩn hóa role
        const userData = res.data.user
        if (userData && userData.role) {
          userData.role = userData.role.toLowerCase() // Chuyển "Admin" -> "admin"
        }

        user.value = userData // user state giờ sẽ có role 'admin'
        token.value = res.data.token

        if (import.meta.client) {
          // Lưu vào localStorage với role đã chuẩn hóa
          localStorage.setItem('auth-user', JSON.stringify(userData))
          localStorage.setItem('auth-token', res.data.token)

          // Gán vào cookie với role đã chuẩn hóa
          useCookie('auth-user').value = userData
          useCookie('auth-token').value = res.data.token
        }

        api.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`

        // ✨ TRẢ VỀ DATA ĐÃ CHUẨN HÓA
        return { ...res.data, user: userData }

      } else {
        throw new Error(res.data.message || 'Đăng nhập không thành công')
      }
    } catch (err) {
      // console.error('Đăng nhập thất bại:', err)
      throw err
    }
  }

  // ...

  // 🟩 LOGOUT
  const logout = async (role = 'admin') => {
    try {
      await api.post(`/${role}/logout`)
    } catch (err) {
      console.warn('Đăng xuất thất bại:', err)
    } finally {
      user.value = null
      token.value = null

      if (import.meta.client) {
        localStorage.removeItem('auth-user')
        localStorage.removeItem('auth-token')
        useCookie('auth-user').value = null
        useCookie('auth-token').value = null
      }

      delete api.defaults.headers.common['Authorization']

      return true
    }
  }

  return { login, logout, user, token }
}
