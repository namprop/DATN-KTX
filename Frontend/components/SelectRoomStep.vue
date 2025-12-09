<template>
  <div
    class="mx-auto bg-white text-gray-900 p-10 mt-6 shadow-lg rounded-lg font-[Times_New_Roman]"
    style="width: 210mm; min-height: 297mm"
  >
    <div class="text-center mt-6 mb-8">
      <p class="font-bold uppercase text-lg underline">
        CHỌN PHÒNG Ở KÝ TÚC XÁ (Bước 2/3)
      </p>
    </div>

    <!-- Bộ lọc -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
      <div class="flex items-center gap-3">
        <label class="font-semibold">Loại phòng:</label>
        <select
          v-model="filters.type"
          class="border border-gray-300 rounded-lg px-3 py-2"
        >
          <option value="">Tất cả</option>
          <option v-for="t in roomTypes" :key="t" :value="t">{{ t }}</option>
        </select>
      </div>

      <input
        v-model="filters.search"
        type="text"
        placeholder="Tìm theo mã phòng..."
        class="border border-gray-300 rounded-lg px-3 py-2 w-60"
      />
    </div>

    <!-- Danh sách phòng -->
    <div v-if="loading" class="text-center py-20 text-gray-500">
      Đang tải danh sách phòng...
    </div>

    <div
      v-else-if="filteredRooms.length === 0"
      class="text-center py-20 text-gray-500"
    >
      Không có phòng phù hợp.
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="room in filteredRooms"
        :key="room.id"
        class="border border-gray-300 rounded-xl shadow-sm hover:shadow-lg transition cursor-pointer"
        :class="{
          'ring-2 ring-green-500': selectedRoom?.id === room.id,
          'opacity-60 pointer-events-none':
            room.available === 0 || room.status === 'Đang bảo trì',
        }"
        @click="selectRoom(room)"
      >
        <div class="p-6">
          <h3 class="text-xl font-bold text-blue-700 mb-2">
            Phòng {{ room.code }}
          </h3>
          <p>
            <span class="font-semibold">Trạng thái:</span>
            <span
              :class="room.available > 0 ? 'text-green-600' : 'text-red-600'"
            >
              {{ room.status }}
            </span>
          </p>

          <p>
            <span class="font-semibold">Loại:</span>
            {{ room.type }}
          </p>
          <p>
            <span class="font-semibold">Khu:</span>
            {{ room.area }}
          </p>
          <p>
            <span class="font-semibold">Sức chứa:</span>
            {{ room.capacity }} người
          </p>
          <p>
            <span class="font-semibold">Hiện còn:</span>
            <span
              :class="room.available > 0 ? 'text-green-600' : 'text-red-600'"
              >{{ room.available }}</span
            >
            chỗ
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="errorMessage"
      class="text-center text-red-600 mt-4 font-semibold"
    >
      {{ errorMessage }}
    </div>

    <!-- Nút điều hướng -->
    <div class="mt-12 text-center flex justify-center gap-4">
      <button
        @click="$emit('back')"
        class="bg-gray-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-600 transition"
      >
        ⬅️ Quay lại
      </button>

      <button
        @click="confirmRoom"
        class="bg-green-600 text-white font-semibold px-6 py-2 rounded-lg hover:bg-green-700 transition"
      >
        Xác nhận phòng ➡️
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from "vue";
import useAxios from "@/composables/useAxios";

const emit = defineEmits(["back", "confirm"]);
const api = useAxios();

const rooms = ref([]);
const loading = ref(true);
const errorMessage = ref("");
const selectedRoom = ref(null);

// Nhận giới tính từ bước 1
const props = defineProps({
  studentGender: {
    type: String,
    default: "Nam",
  },
});

const filters = reactive({
  area: "",
  type: "",
  search: "",
});

// 🧩 Dịch status từ API sang hiển thị
function translateStatus(status) {
  if (!status) return "Không rõ";
  const s = String(status).toLowerCase();
  if (s.includes("avail")) return "Còn trống";
  if (s.includes("full")) return "Đầy";
  if (s.includes("maint")) return "Đang bảo trì";
  return status;
}

// 🧩 Chuẩn hoá rawType (vd: "Male_Dormitory" -> "maledormitory")
function normalizeRawType(raw) {
  if (!raw) return "";
  return raw.toString().toLowerCase().replace(/[_\s]+/g, "");
}

function translateTypeFromRaw(raw) {
  const n = normalizeRawType(raw);
  if (!n) return "Không rõ loại";
  if (n.includes("female")) return "Phòng Nữ";
  if (n.includes("male")) return "Phòng Nam";
  return raw;
}

// 🧩 Suy khu từ desc hoặc room_code
function deriveArea(desc, room_code) {
  if (typeof desc === "string" && desc.includes("-")) {
    const left = desc.split("-")[0].trim();
    if (left) return left;
  }
  if (typeof room_code === "string" && room_code.length > 0) {
    const first = room_code.charAt(0).toUpperCase();
    return `Khu ${first}`;
  }
  return "Không rõ khu";
}

// 🧩 Load danh sách phòng
onMounted(async () => {
  try {
    loading.value = true;
    const res = await api.get("/student/displayroom");

    if (res.data?.status && Array.isArray(res.data.data)) {
      rooms.value = res.data.data.map((r) => {
        const desc = typeof r.description === "string" ? r.description : "";
        let rawType = "";
        let area = "";

        if (desc.includes("-")) {
          const parts = desc.split("-").map((s) => s.trim());
          area = parts[0] || deriveArea(desc, r.room_code);
          rawType = parts[1] || "";
        } else {
          rawType = desc || "";
          area = deriveArea(desc, r.room_code);
        }

        const nRaw = normalizeRawType(rawType);
        const capacity = Number(r.capacity) || 4;
        const occupied = Number(r.students_count) || 0;
        const available = Math.max(capacity - occupied, 0);

        return {
          id: r.id,
          code: r.room_code || "",
          area,
          rawType,
          type: translateTypeFromRaw(rawType),
          status: translateStatus(r.status),
          capacity,
          students_count: occupied,
          available,
        };
      });
    } else {
      errorMessage.value = "Không có dữ liệu phòng.";
    }

    console.log("🧩 Giới tính nhận từ bước 1:", props.studentGender);
  } catch (err) {
    console.error("❌ Lỗi khi tải danh sách phòng:", err);
    errorMessage.value = "Không thể tải danh sách phòng.";
  } finally {
    loading.value = false;
  }
});

const areas = computed(() => {
  const s = rooms.value.map((r) => r.area || "Không rõ khu");
  return [...new Set(s)];
});
const roomTypes = computed(() => {
  const s = rooms.value.map((r) => r.type || "Không rõ loại");
  return [...new Set(s)];
});

// 🧩 Lọc theo giới tính
const filteredRooms = computed(() => {
  const genderRaw = (props.studentGender || "").toString().toLowerCase().trim();
  const isMale = genderRaw.includes("nam") || genderRaw.includes("male");
  const isFemale = genderRaw.includes("nữ") || genderRaw.includes("nu") || genderRaw.includes("female");

  return rooms.value.filter((r) => {
    const matchArea = !filters.area || r.area === filters.area;
    const matchType = !filters.type || r.type === filters.type;
    const matchSearch =
      !filters.search ||
      r.code.toLowerCase().includes(filters.search.toLowerCase());

    // lấy từ description của API
    const desc = (r.rawType || r.description || "").toLowerCase();

    // lọc giới tính
    let matchGender = true;
    if (isMale) matchGender = desc.includes("male");
    if (isFemale) matchGender = desc.includes("female");

    return matchArea && matchType && matchSearch && matchGender;
  });
});


function selectRoom(room) {
  if (room.available === 0 || room.status === "Đang bảo trì") return;
  selectedRoom.value = room;
  errorMessage.value = "";
}

function confirmRoom() {
  if (!selectedRoom.value) {
    errorMessage.value = "⚠️ Vui lòng chọn phòng trước khi tiếp tục.";
    return;
  }
  emit("confirm", selectedRoom.value);
}
</script>
