<template>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-7xl mx-auto">
      <!-- Tin tức chi tiết -->
      <section id="news-detail">
        <div class="mb-12">
          <!-- Tiêu đề -->
          <h1
            class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-cyan-600 to-blue-600 bg-clip-text text-transparent mb-4"
          >
            {{ news.title }}
          </h1>

          <!-- Ngày đăng -->
          <p class="text-sm text-slate-500 mb-6">{{ news.date }}</p>

          <!-- Ảnh -->
          <img
            :src="news.image"
            :alt="news.title"
            class="mx-auto w-full max-w-[1000px] h-[400px] md:h-[450px] object-cover rounded-2xl shadow-lg mb-8"
          />

          <!-- Nội dung chi tiết -->
          <div
            class="prose prose-lg max-w-none text-slate-700 leading-relaxed whitespace-pre-line font-serif text-lg"
          >
            <p
              v-for="(paragraph, index) in news.content.split('\n\n')"
              :key="index"
              class="mb-4"
            >
              {{ paragraph }}
            </p>
          </div>

          <!-- Quay lại -->
          <NuxtLink
            to="/"
            class="inline-flex items-center gap-2 mt-8 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-semibold rounded-lg hover:from-cyan-700 hover:to-blue-700 transition shadow-md"
          >
            <i class="fa-solid fa-arrow-left"></i>
            Quay lại trang chủ
          </NuxtLink>
        </div>
      </section>

      <!-- Bài viết liên quan -->
      <section id="related-news" class="mt-16">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">
          Bài viết liên quan
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            v-for="item in relatedNews"
            :key="item.id"
            class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-lg transition"
          >
            <NuxtLink :to="`/news/${item.id}`" class="block">
              <img
                :src="item.image"
                :alt="item.title"
                class="w-full h-40 object-cover"
              />
              <div class="p-4">
                <p class="text-sm text-slate-500 mb-1">{{ item.date }}</p>
                <h3
                  class="font-semibold text-slate-800 hover:text-cyan-600 transition"
                >
                  {{ item.title }}
                </h3>
              </div>
            </NuxtLink>
          </div>
        </div>
      </section>

      <!-- Liên hệ -->
      <TagContact />
    </div>
  </div>
</template>

<script setup>
import { useRoute } from "vue-router";

definePageMeta({
  layout: "index",
});

// Dữ liệu tạm thời, xuống dòng đầy đủ
const newsList = [
  {
    id: 2,
    title:
      "Hội thảo trao đổi kinh nghiệm triển khai một số kỹ thuật dạy học phát huy tính tích cực của học sinh THPT",
    date: "Ngày 14 tháng 10, 2025",
    image: "/img/imageblog3.jpg",
    content: `Năm học 2022 - 2023 là năm học đầu tiên triển khai giáo dục phổ thông tổng thể 2018 đối với cấp THPT với mục tiêu là giúp học sinh tiếp tục "phát triển những phẩm chất, năng lực cần thiết".

Sáng ngày 16/4/2023 tại trường THPT Thanh Oai A đã tổ chức buổi hội thảo trao đổi kinh nghiệm triển khai một số kỹ thuật dạy học phát huy tính tích cực của học sinh THPT.

🌻 Đến dự chỉ đạo tại buổi hội thảo:
- Về phía sở GD & ĐT Hà Nội: Ông Lê Hồng Vũ, Phó trưởng phòng GD Trung học; Ông Đinh Hữu Lâm, chuyên viên.
- Đại biểu khách mời trường Đại học Sư phạm Hà Nội: PGS TS Trần Trung Ninh.
- Đại biểu lãnh đạo các trường THPT huyện Thanh Oai: Thầy Đỗ Danh Tuyến, Thầy Nguyễn Văn Triểu cùng các tổ trưởng chuyên môn.
- Toàn bộ CBGV trường Thanh Oai A.

🌻 Tại buổi hội thảo đại diện cho 5 tổ chuyên môn đã trao đổi 6 phương pháp, kỹ thuật dạy học tích cực:
- Kỹ thuật mảnh ghép-chuyên gia
- Phương pháp đóng vai
- Phương pháp dạy học dự án
- Phương pháp dạy học theo góc
- Sử dụng sơ đồ tư duy
- Phương pháp lớp học đảo ngược

Cũng tại buổi hội thảo các thành viên được nghe ý kiến trao đổi của chuyên gia, thảo luận sôi nổi về các phương pháp dạy học.`,
  },
  {
    id: 1,
    title:
      "THÔNG BÁO VỀ VIỆC TỔ CHỨC LỄ KỶ NIỆM 60 NĂM THÀNH LẬP TRƯỜNG VÀ ĐÓN NHẬN BẰNG KHEN CỦA BỘ GIÁO DỤC VÀ ĐÀO TẠO",
    date: "Ngày 10 tháng 10, 2025",
    image: "/img/imageblog4.jpg",
    content: `Trường THPT Thanh Oai A được thành lập tháng 9 năm 1965 đến nay vừa tròn 60 năm. Trải qua hơn một nửa thế kỷ xây dựng, phát triển, trưởng thành với biết bao biến cố, thăng trầm, nhà trường đã không ngừng lớn mạnh và khẳng định được vị thế vững vàng trong khối các trường THPT trên địa bàn Thành phố Hà Nội.

Được sự đồng ý của Sở GD&ĐT Hà Nội, Đảng ủy, UBND xã Thanh Oai, trường THPT Thanh Oai A tổ chức Lễ kỷ niệm 60 năm thành lập trường và đón nhận bằng khen của Bộ GD&ĐT.

Thời gian: 7h30’ chủ nhật, ngày 16 tháng 11 năm 2025.

Địa điểm: Trường THPT Thanh Oai A - Thôn Văn Quán - xã Thanh Oai - Hà Nội.

Nhà trường trân trọng kính mời các thế hệ Cán bộ, Giáo viên, Nhân viên, Học sinh của nhà trường về tham dự Lễ kỷ niệm 60 năm thành lập trường và đón nhận bằng khen của Bộ GD&ĐT. Rất hân hạnh được đón tiếp Quý đại biểu! (Thông báo này thay cho giấy mời)`,
  },
  {
    id: 3,
    title: 'LỄ KHAI GIẢNG NĂM HỌC 2025 – 2026 – TRƯỜNG THPT THANH OAI A 🎉',
    date: "Ngày 8 tháng 10, 2025",
    image: "/img/imageblog5.jpg",
    content: `Sáng 5-9-2025, trong không khí trang nghiêm và rộn ràng, thầy trò THPT Thanh Oai A đã long trọng tổ chức Lễ khai giảng năm học mới 2025 – 2026.

💃🕺 16 lớp khối 10 đã chính thức được đón vào trường – trở thành những thành viên mới đầy tự hào.

🌹 Nhà trường vinh dự chào đón các vị đại biểu, lắng nghe cô Nguyễn Thị Hạnh – Phó Hiệu trưởng đọc thư của Chủ tịch nước gửi ngành giáo dục nhân ngày khai giảng.

🚩 Tiếp đó là nghi thức trao cờ truyền thống: cựu học sinh tiêu biểu nhất Hồng Sơn, Xuân Mạnh trang trọng trao lại cho Huyền Trang, Ngọc Lê – những gương mặt tiêu biểu của thế hệ kế cận, ghi dấu một thế hệ kế thừa đầy bản lĩnh.

🎤 Hòa trong không khí cả nước trước kỉ niệm 80 năm ngày thành lập Bộ GD&ĐT, toàn thể thầy trò cũng cùng dự trực tuyến Lễ khai giảng quốc gia do Bộ GD&ĐT tổ chức. Các tiết mục văn nghệ như “Việt Nam Tôi”, “Còn gì đẹp hơn” qua phần trình bày của học sinh đã làm bầu không khí thêm rộn rã và giàu cảm xúc.

🥁 Kết thúc buổi lễ, thầy Hiệu trưởng phát biểu, đánh trống khai trường và khen thưởng học sinh đạt thành tích xuất sắc: đỗ đại học điểm cao và thủ khoa đầu vào lớp 10.

✨ Lễ khai giảng đã khép lại trong niềm hân hoan, mở ra một năm học mới đầy quyết tâm, sáng tạo và thành công. Một năm học đánh dấu kỉ niệm 60 năm của trường THPT Thanh Oai A hứa hẹn là 1 năm học mang đầy sự đổi mới và chuyển mình mạnh mẽ bước vào kỉ nguyên mới.`,
  },
];

// Lấy bài hiện tại
const route = useRoute();
const id = parseInt(route.params.id);
const news = newsList.find((n) => n.id === id) || newsList[0];

// Bài viết liên quan (bỏ qua bài hiện tại)
const relatedNews = newsList.filter((n) => n.id !== news.id);
</script>
