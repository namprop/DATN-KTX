<template>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-7xl mx-auto">
      <!-- Tin tức -->
      <section id="news">
        <div class="text-center mb-12">
          <h2
            class="text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent"
          >
            TIN TỨC & SỰ KIỆN KÝ TÚC XÁ
          </h2>
          <p class="mt-3 text-slate-600 max-w-2xl mx-auto">
            Cập nhật các hoạt động, thông báo mới nhất từ Ban Quản lý KTX.
          </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Tin lớn -->
          <NuxtLink
            :to="`/news/${newsList[0].id}`"
            class="lg:col-span-2 bg-gradient-to-br from-white to-slate-50 rounded-2xl overflow-hidden border-2 border-cyan-200 shadow-md hover:shadow-2xl transition-all duration-500 hover:-translate-y-1"
          >

          
            <div class="relative">
              <img
                class="w-full h-64 md:h-80 object-cover"
                :src="newsList[0].image"
                :alt="newsList[0].title"
              />
              <div
                class="absolute bottom-4 left-4 bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-xs font-semibold px-3 py-1 rounded-full shadow-md"
              >
                {{ newsList[0].category }}
              </div>
            </div>
            <div class="p-6 md:p-8">
              <p class="text-sm text-slate-500 mb-1">{{ newsList[0].date }}</p>
              <h3
                class="text-2xl font-bold mb-3 leading-snug text-slate-800 hover:text-cyan-600 transition-colors"
              >
                {{ newsList[0].title }}
              </h3>
              <p class="text-slate-700 line-clamp-3">
                {{ newsList[0].summary }}
              </p>
            </div>
          </NuxtLink>

          <!-- Danh sách nhỏ -->
          <div
            class="space-y-5 bg-gradient-to-br from-cyan-50 to-blue-50 p-4 rounded-2xl border-2 border-cyan-300 shadow-md"
          >
            <NuxtLink
              v-for="news in newsList.slice(1)"
              :key="news.id"
              :to="`/news/${news.id}`"
              class="flex items-center gap-4 bg-gradient-to-r from-white to-cyan-50 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all hover:-translate-y-1 border border-cyan-100"
            >
              <img
                :src="news.image"
                class="w-24 h-24 object-cover rounded-lg"
                :alt="news.title"
              />
              <div>
                <p
                  class="text-sm font-semibold"
                  :class="
                    news.category === 'SỰ KIỆN'
                      ? 'text-blue-600'
                      : 'text-cyan-600'
                  "
                >
                  {{ news.category }}
                </p>
                <h4 class="font-medium text-slate-800 leading-snug">
                  {{ news.title }}
                </h4>
              </div>
            </NuxtLink>
          </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-10">
          <nav
            class="inline-flex bg-white rounded-full shadow-md overflow-hidden border-2 border-cyan-200"
          >
            <a
              href="#"
              class="px-3 py-2 text-slate-500 hover:bg-cyan-100 transition"
              >‹</a
            >
            <a
              href="#"
              class="px-4 py-2 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold"
              >1</a
            >
            <a
              href="#"
              class="px-4 py-2 text-slate-600 hover:bg-cyan-100 transition"
              >2</a
            >
            <a
              href="#"
              class="px-4 py-2 text-slate-600 hover:bg-cyan-100 transition"
              >3</a
            >
            <a
              href="#"
              class="px-3 py-2 text-slate-500 hover:bg-cyan-100 transition"
              >›</a
            >
          </nav>
        </div>
      </section>

      <!-- Liên hệ -->
      <TagContact />
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useAxios from "@/composables/useAxios";

definePageMeta({
  layout: "index",
});

const api = useAxios();

/**
 * ✅ DỮ LIỆU CỨNG (ID 1–2–3)
 */
const newsList = ref([
  {
    id: 1,
    title: "THÔNG BÁO VỀ VIỆC TỔ CHỨC LỄ KỶ NIỆM 60 NĂM THÀNH LẬP TRƯỜNG...",
    date: "Ngày 30 tháng 10, 2025",
    image: "/img/imageblog4.jpg",
    summary: "Trường THPT Thanh Oai A được thành lập tháng 9 năm 1965...",
    category: "THÔNG BÁO",
  },
  {
    id: 2,
    title: "HỘI THẢO TRAO ĐỔI KINH NGHIỆM TRIỂN KHAI...",
    date: "Ngày 14 tháng 10, 2025",
    image: "/img/imageblog3.jpg",
    summary: "Năm học 2022 - 2023 là năm học đầu tiên...",
    category: "Tin Tức",
  },
  {
    id: 3,
    title: "LỄ KHAI GIẢNG NĂM HỌC 2025 – 2026 🎉",
    date: "Ngày 7 tháng 9, 2025",
    image: "/img/imageblog5.jpg",
    summary: "Sáng 5-9-2025, trong không khí trang nghiêm...",
    category: "SỰ KIỆN",
  },
]);

/**
 * ✅ FETCH API & NỐI DỮ LIỆU
 */
const fetchNews = async () => {
  try {
    const res = await api.get("/newspapers");

    if (res.data?.status && res.data.data?.length) {
      const apiNews = res.data.data.map((item) => ({
        id: item.id + 3, // ✅ cộng 3 tránh trùng 1–2–3
        title: item.title,
        date: new Date(item.created_at).toLocaleDateString("vi-VN"),
        image: item.image
          ? `http://localhost:8000/storage/${item.image}`
          : "/img/imageblog4.jpg",
        summary: item.content,
        category: item.type ?? "TIN TỨC",
      }));

      // ✅ NỐI – KHÔNG GHI ĐÈ
      newsList.value = [...newsList.value, ...apiNews];
    }
  } catch (err) {
    console.warn("Không load được API, dùng dữ liệu cứng");
  }
};

onMounted(fetchNews);
</script>
