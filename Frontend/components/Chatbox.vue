<template>
  <div class="fixed bottom-5 right-5 z-[9999]">
    <!-- Nút mở chat -->
    <button
      @click="toggleChat"
      class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-transform hover:scale-110"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="2"
        stroke="currentColor"
        class="w-6 h-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.77 9.77 0 0 1-4.17-.91L3 20l1.06-3.18A7.97 7.97 0 0 1 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
        />
      </svg>
    </button>

    <!-- Hộp chat -->
    <div
      v-if="isOpen"
      class="w-80 shadow-lg border rounded-xl bg-white mt-3 animate-fade-in"
    >
      <!-- Tiêu đề -->
      <div
        class="bg-green-500 text-white p-3 rounded-t-xl flex justify-between items-center"
      >
        <span>Trợ lý phụ huynh</span>
        <button @click="toggleChat" class="text-white text-lg font-bold">
          ✕
        </button>
      </div>

      <!-- Khu vực tin nhắn -->
      <div class="h-64 overflow-y-auto p-3 space-y-2">
        <div
          v-for="(msg, i) in messages"
          :key="i"
          :class="msg.sender === 'user' ? 'text-right' : 'text-left'"
        >
          <p
            :class="[
              'inline-block p-2 rounded-lg',
              msg.sender === 'user'
                ? 'bg-green-100 text-gray-800'
                : 'bg-gray-100 text-gray-700',
            ]"
          >
            {{ msg.text }}
          </p>
        </div>
      </div>

      <!-- Ô nhập -->
      <form @submit.prevent="sendMessage" class="flex border-t">
        <input
          v-model="input"
          type="text"
          placeholder="Nhập tin nhắn..."
          class="flex-1 p-2 outline-none"
        />
        <button
          type="submit"
          class="bg-green-500 text-white px-3 disabled:opacity-50"
          :disabled="loading"
        >
          {{ loading ? "..." : "Gửi" }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const isOpen = ref(false);
const input = ref("");
const messages = ref([
  { sender: "bot", text: "Xin chào! Tôi là trợ lý hỗ trợ phụ huynh." },
]);
const loading = ref(false);

function toggleChat() {
  isOpen.value = !isOpen.value;
}

// ✅ Hàm gửi tin nhắn thật đến Laravel backend
async function sendMessage() {
  if (!input.value.trim()) return;

  // Đẩy tin nhắn user lên UI
  messages.value.push({ sender: "user", text: input.value });
  const userMessage = input.value;
  input.value = "";
  loading.value = true;

  try {
    // Gửi request đến Laravel API
    const res = await fetch("http://localhost:8000/api/chat", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ message: userMessage }),
    });

    const data = await res.json();

    // ✅ Hiển thị phản hồi thật từ backend (OpenAI)
    messages.value.push({ sender: "bot", text: data.reply || "..." });
  } catch (error) {
    messages.value.push({
      sender: "bot",
      text: "⚠️ Lỗi kết nối đến máy chủ. Vui lòng thử lại sau.",
    });
  } finally {
    loading.value = false;
  }
}
</script>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
.animate-fade-in {
  animation: fade-in 0.3s ease;
}
</style>
