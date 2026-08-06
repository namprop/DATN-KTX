<template>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-7xl mx-auto">
      <header class="text-center mb-10">
        <h1 class="text-4xl font-extrabold text-slate-800">Tin tức và sự kiện</h1>
        <p class="mt-3 text-slate-600">Thông tin mới nhất từ Ban Quản lý ký túc xá.</p>
      </header>

      <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <input v-model="search" class="flex-1 border rounded-lg px-4 py-3" placeholder="Tìm kiếm bài viết..." @keyup.enter="loadNews(1)" />
        <select v-model="type" class="border rounded-lg px-4 py-3 bg-white" @change="loadNews(1)">
          <option value="">Tất cả loại bài</option>
          <option value="news">Tin tức</option>
          <option value="event">Sự kiện</option>
          <option value="notice">Thông báo</option>
        </select>
        <button class="px-6 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700" @click="loadNews(1)">Tìm kiếm</button>
      </div>

      <div v-if="loading" class="py-20 text-center text-slate-500">Đang tải bài viết...</div>
      <div v-else-if="errorMessage" class="py-20 text-center text-red-600">{{ errorMessage }}</div>
      <div v-else-if="news.length === 0" class="py-20 text-center text-slate-500">Chưa có bài viết phù hợp.</div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
        <article v-for="item in news" :key="item.id" class="bg-white rounded-2xl overflow-hidden border shadow-sm hover:shadow-lg transition">
          <NuxtLink :to="`/news/${item.id}`">
            <img :src="item.image_url || fallbackImage" :alt="item.title" class="w-full h-52 object-cover" />
            <div class="p-5">
              <div class="flex justify-between text-xs text-slate-500 mb-2">
                <span class="font-semibold text-cyan-700">{{ typeLabel(item.type) }}</span>
                <time>{{ formatDate(item.created_at) }}</time>
              </div>
              <h2 class="text-lg font-bold text-slate-800 line-clamp-2">{{ item.title }}</h2>
              <p class="mt-3 text-slate-600 line-clamp-3">{{ item.summary }}</p>
            </div>
          </NuxtLink>
        </article>
      </div>

      <Paginate v-if="pagination.last_page > 1" :current-page="pagination.current_page" :last-page="pagination.last_page" :total="pagination.total" @change="loadNews" />
    </div>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import useAxios from '@/composables/useAxios'

definePageMeta({ layout: 'index' })
useSeoMeta({ title: 'Tin tức và sự kiện | DATN-KTX', description: 'Tin tức, sự kiện và thông báo mới nhất từ ký túc xá.' })

const api = useAxios()
const news = ref([])
const search = ref('')
const type = ref('')
const loading = ref(false)
const errorMessage = ref('')
const pagination = ref({ total: 0, per_page: 9, current_page: 1, last_page: 1 })
const fallbackImage = '/img/imageblog4.jpg'
const typeLabel = value => ({ news: 'Tin tức', event: 'Sự kiện', notice: 'Thông báo' }[value] || 'Tin tức')
const formatDate = value => value ? new Date(value).toLocaleDateString('vi-VN') : ''

const loadNews = async (page = 1) => {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await api.get('/newspapers', { params: { page, search: search.value || undefined, type: type.value || undefined } })
    news.value = data.data || []
    pagination.value = data.pagination
  } catch (error) {
    errorMessage.value = 'Không thể tải danh sách bài viết. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

onMounted(() => loadNews())
</script>
