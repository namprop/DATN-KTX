<template>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-5xl mx-auto">
      <div v-if="pending" class="py-24 text-center text-slate-500">Đang tải bài viết...</div>
      <div v-else-if="error || !article" class="py-24 text-center">
        <h1 class="text-2xl font-bold text-slate-800">Không tìm thấy bài viết</h1>
        <NuxtLink to="/news" class="inline-block mt-5 text-cyan-700 hover:underline">Quay lại danh sách tin</NuxtLink>
      </div>

      <template v-else>
        <article>
          <div class="flex flex-wrap gap-3 text-sm text-slate-500 mb-4">
            <span class="font-semibold text-cyan-700">{{ typeLabel(article.type) }}</span>
            <time>{{ formatDate(article.created_at) }}</time>
            <span v-if="article.author">Đăng bởi {{ article.author }}</span>
          </div>
          <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-7">{{ article.title }}</h1>
          <img :src="article.image_url || fallbackImage" :alt="article.title" class="w-full max-h-[520px] object-cover rounded-2xl shadow mb-8" />
          <div class="text-slate-700 leading-8 whitespace-pre-line text-lg">{{ article.content }}</div>
          <NuxtLink to="/news" class="inline-flex items-center gap-2 mt-10 px-5 py-3 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách tin
          </NuxtLink>
        </article>

        <section v-if="related.length" class="mt-16">
          <h2 class="text-2xl font-bold mb-6 text-slate-800">Bài viết liên quan</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <NuxtLink v-for="item in related" :key="item.id" :to="`/news/${item.id}`" class="bg-white rounded-2xl shadow overflow-hidden hover:shadow-lg transition">
              <img :src="item.image_url || fallbackImage" :alt="item.title" class="w-full h-40 object-cover" />
              <div class="p-4">
                <p class="text-xs text-slate-500 mb-1">{{ formatDate(item.created_at) }}</p>
                <h3 class="font-semibold text-slate-800 line-clamp-2">{{ item.title }}</h3>
              </div>
            </NuxtLink>
          </div>
        </section>
      </template>
      <TagContact />
    </div>
  </main>
</template>

<script setup>
import { computed } from 'vue'

definePageMeta({ layout: 'index' })

const route = useRoute()
const config = useRuntimeConfig()
const fallbackImage = '/img/imageblog4.jpg'
const typeLabel = value => ({ news: 'Tin tức', event: 'Sự kiện', notice: 'Thông báo' }[value] || 'Tin tức')
const formatDate = value => value ? new Date(value).toLocaleDateString('vi-VN') : ''

const { data, pending, error } = await useAsyncData(
  () => `news-${route.params.id}`,
  () => $fetch(`${config.public.apiBase}/newspapers/${route.params.id}`),
  { watch: [() => route.params.id] },
)

const article = computed(() => data.value?.data || null)
const related = computed(() => data.value?.related || [])

useSeoMeta({
  title: () => article.value ? `${article.value.title} | DATN-KTX` : 'Bài viết | DATN-KTX',
  description: () => article.value?.summary || article.value?.content?.slice(0, 160) || 'Tin tức ký túc xá',
  ogTitle: () => article.value?.title || 'DATN-KTX',
  ogImage: () => article.value?.image_url || fallbackImage,
})
</script>
