<template>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-7xl mx-auto">
      <section id="news">
        <div class="text-center mb-12">
          <h1 class="text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent">TIN TỨC & SỰ KIỆN KÝ TÚC XÁ</h1>
          <p class="mt-3 text-slate-600">Cập nhật hoạt động và thông báo mới nhất từ Ban Quản lý KTX.</p>
        </div>

        <div v-if="loading" class="py-20 text-center text-slate-500">Đang tải tin mới...</div>
        <div v-else-if="news.length === 0" class="py-20 text-center text-slate-500">
          Chưa có bài viết nào được xuất bản.
        </div>

        <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <NuxtLink :to="`/news/${news[0].id}`" class="lg:col-span-2 bg-white rounded-2xl overflow-hidden border-2 border-cyan-200 shadow-md hover:shadow-xl transition">
            <div class="relative">
              <img :src="news[0].image_url || fallbackImage" :alt="news[0].title" class="w-full h-64 md:h-80 object-cover" />
              <span class="absolute bottom-4 left-4 bg-cyan-600 text-white text-xs font-semibold px-3 py-1 rounded-full">{{ typeLabel(news[0].type) }}</span>
            </div>
            <div class="p-6 md:p-8">
              <p class="text-sm text-slate-500 mb-2">{{ formatDate(news[0].created_at) }}</p>
              <h2 class="text-2xl font-bold mb-3 text-slate-800 line-clamp-2">{{ news[0].title }}</h2>
              <p class="text-slate-700 line-clamp-3">{{ news[0].summary }}</p>
            </div>
          </NuxtLink>

          <div class="space-y-5 bg-cyan-50 p-4 rounded-2xl border-2 border-cyan-200">
            <NuxtLink v-for="item in news.slice(1)" :key="item.id" :to="`/news/${item.id}`" class="flex items-center gap-4 bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition">
              <img :src="item.image_url || fallbackImage" :alt="item.title" class="w-24 h-24 object-cover rounded-lg" />
              <div class="min-w-0">
                <p class="text-xs font-semibold text-cyan-700">{{ typeLabel(item.type) }}</p>
                <h3 class="font-medium text-slate-800 line-clamp-3">{{ item.title }}</h3>
              </div>
            </NuxtLink>
          </div>
        </div>

        <div class="text-center mt-10">
          <NuxtLink to="/news" class="inline-flex px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-lg hover:shadow-lg transition">Xem tất cả bài viết</NuxtLink>
        </div>
      </section>

      <TagContact />
    </div>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import useAxios from '@/composables/useAxios'

definePageMeta({ layout: 'index' })
useSeoMeta({ title: 'DATN-KTX | Tin tức ký túc xá', description: 'Tin tức và thông báo mới nhất từ Ban Quản lý ký túc xá.' })

const api = useAxios()
const news = ref([])
const loading = ref(false)
const fallbackImage = '/img/imageblog4.jpg'
const typeLabel = value => ({ news: 'Tin tức', event: 'Sự kiện', notice: 'Thông báo' }[value] || 'Tin tức')
const formatDate = value => value ? new Date(value).toLocaleDateString('vi-VN') : ''

const fetchLatestNews = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/newspapers', { params: { per_page: 4 } })
    news.value = data.data || []
  } catch (error) {
    news.value = []
    console.error('Không thể tải tin mới:', error)
  } finally {
    loading.value = false
  }
}

onMounted(fetchLatestNews)
</script>
