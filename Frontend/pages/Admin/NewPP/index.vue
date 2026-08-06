<template>
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <KTXAddAndSearch
      title="Danh sách bài viết"
      title-add="Thêm bài viết"
      placeholder-search="Tìm theo tiêu đề hoặc nội dung..."
      @search="handleSearch"
      @openForm="openAddForm"
    />

    <div class="flex flex-wrap gap-3 px-6 py-4 border-b bg-gray-50">
      <select v-model="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white" @change="fetchAnnouncements(1)">
        <option value="">Tất cả trạng thái</option>
        <option value="Active">Đang hiển thị</option>
        <option value="Inactive">Đang ẩn</option>
      </select>
      <select v-model="typeFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white" @change="fetchAnnouncements(1)">
        <option value="">Tất cả loại bài</option>
        <option value="news">Tin tức</option>
        <option value="event">Sự kiện</option>
        <option value="notice">Thông báo</option>
      </select>
    </div>

    <FormAddAndEdit
      :fields="formFields"
      :show="openForm"
      :title="isEdit ? 'Cập nhật bài viết' : 'Thêm bài viết mới'"
      v-model="form"
      :mess="formErrors"
      @close="closeForm"
      @submit="submitAnnouncement"
    />

    <KTXLoadingSkeleton v-if="isLoading" :is-loading="isLoading" :title-table="headers" />

    <div v-else class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50"><KTXTilteTable :title-table="headers" /></thead>
        <tbody class="divide-y divide-gray-200">
          <tr v-for="(item, index) in announcements" :key="item.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ rowNumber(index) }}</td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs"><div class="font-medium line-clamp-2">{{ item.title }}</div></td>
            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs"><div class="line-clamp-2">{{ item.content }}</div></td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ typeLabel(item.type) }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">
              <span class="px-2 py-1 rounded-full text-xs font-semibold" :class="statusClass(item.status)">
                {{ item.status === 'Active' ? 'Đang hiển thị' : 'Đang ẩn' }}
              </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900">
              <div>{{ item.author || 'Không xác định' }}</div>
              <div class="text-xs text-gray-400">{{ formatDate(item.created_at) }}</div>
            </td>
            <td class="px-6 py-2">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="w-20 h-12 object-cover rounded" />
              <span v-else class="text-gray-400 italic">Chưa có ảnh</span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
              <button class="text-blue-600 hover:text-blue-900 mr-3" @click="editAnnouncement(item)">Sửa</button>
              <button class="text-red-600 hover:text-red-900" @click="deleteAnnouncement(item.id)">Xóa</button>
            </td>
          </tr>
          <tr v-if="announcements.length === 0">
            <td colspan="8" class="px-6 py-10 text-center text-gray-500">Không có bài viết phù hợp.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <Paginate
    v-if="pagination.last_page > 1"
    :current-page="pagination.current_page"
    :total="pagination.total"
    :last-page="pagination.last_page"
    @change="fetchAnnouncements"
  />
</template>

<script setup>
import { computed, onMounted, ref, toRaw } from 'vue'
import useAxios from '@/composables/useAxios'

definePageMeta({ middleware: 'auth', layout: 'dashboard' })

const api = useAxios()
const announcements = ref([])
const pagination = ref({ total: 0, per_page: 20, current_page: 1, last_page: 1 })
const form = ref({})
const formErrors = ref({})
const openForm = ref(false)
const isEdit = ref(false)
const isLoading = ref(false)
const searchKeyword = ref('')
const statusFilter = ref('')
const typeFilter = ref('')
let debounceTimer

const headers = ['STT', 'Tiêu đề', 'Nội dung', 'Loại bài', 'Trạng thái', 'Người đăng', 'Hình ảnh', 'Hành động']
const typeLabel = type => ({ news: 'Tin tức', event: 'Sự kiện', notice: 'Thông báo' }[type] || 'Chưa phân loại')
const formatDate = value => value ? new Date(value).toLocaleDateString('vi-VN') : ''
const rowNumber = index => (pagination.value.current_page - 1) * pagination.value.per_page + index + 1
const statusClass = status => status === 'Active' ? 'text-green-700 bg-green-100' : 'text-gray-700 bg-gray-100'

const fetchAnnouncements = async (page = 1) => {
  isLoading.value = true
  try {
    const { data } = await api.get('/admin/announcement', {
      params: {
        page,
        search: searchKeyword.value || undefined,
        status: statusFilter.value || undefined,
        type: typeFilter.value || undefined,
      },
    })
    announcements.value = data.data || []
    pagination.value = data.pagination
  } catch (error) {
    console.error('Không thể tải danh sách bài viết:', error)
  } finally {
    isLoading.value = false
  }
}

const openAddForm = () => {
  isEdit.value = false
  formErrors.value = {}
  form.value = { type: 'news', status: 'Active' }
  openForm.value = true
}

const editAnnouncement = item => {
  isEdit.value = true
  formErrors.value = {}
  form.value = {
    ...item,
    type: ['news', 'event', 'notice'].includes(item.type) ? item.type : 'news',
    image: item.image_url,
  }
  openForm.value = true
}

const closeForm = () => {
  openForm.value = false
  formErrors.value = {}
}

const submitAnnouncement = async values => {
  const data = toRaw(values)
  const payload = new FormData()
  payload.append('title', data.title || '')
  payload.append('content', data.content || '')
  payload.append('type', data.type || 'news')
  payload.append('status', data.status || 'Active')
  if (data.image instanceof File) payload.append('image', data.image)

  try {
    formErrors.value = {}
    if (isEdit.value) {
      payload.append('_method', 'PUT')
      await api.post(`/admin/announcement/${data.id}`, payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    } else {
      await api.post('/admin/announcement', payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
    }
    closeForm()
    await fetchAnnouncements(isEdit.value ? pagination.value.current_page : 1)
  } catch (error) {
    if (error.response?.status === 422) formErrors.value = error.response.data.errors || {}
    else console.error('Không thể lưu bài viết:', error)
  }
}

const deleteAnnouncement = async id => {
  if (!confirm('Bạn có chắc muốn xóa bài viết này?')) return
  try {
    await api.delete(`/admin/announcement/${id}`)
    const targetPage = announcements.value.length === 1 && pagination.value.current_page > 1
      ? pagination.value.current_page - 1
      : pagination.value.current_page
    await fetchAnnouncements(targetPage)
  } catch (error) {
    console.error('Không thể xóa bài viết:', error)
  }
}

const handleSearch = keyword => {
  searchKeyword.value = keyword
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => fetchAnnouncements(1), 400)
}

const formFields = computed(() => [{
  personal: {
    title: isEdit.value ? 'Cập nhật bài viết' : 'Thêm bài viết mới',
    icon: 'fas fa-newspaper',
    fields: [
      { key: 'title', label: 'Tiêu đề', type: 'text', placeholder: 'Nhập tiêu đề bài viết', icon: 'fas fa-heading' },
      { key: 'type', label: 'Loại bài', type: 'select', icon: 'fas fa-list', options: [
        { value: 'news', text: 'Tin tức' }, { value: 'event', text: 'Sự kiện' }, { value: 'notice', text: 'Thông báo' },
      ] },
      { key: 'status', label: 'Trạng thái', type: 'select', icon: 'fas fa-eye', options: [
        { value: 'Active', text: 'Đang hiển thị' }, { value: 'Inactive', text: 'Đang ẩn' },
      ] },
      { key: 'image', label: 'Hình ảnh', type: 'file', icon: 'fas fa-image' },
      { key: 'content', label: 'Nội dung', type: 'textarea', placeholder: 'Nhập nội dung bài viết', icon: 'fas fa-align-left' },
    ],
  },
}])

onMounted(() => fetchAnnouncements())
</script>
