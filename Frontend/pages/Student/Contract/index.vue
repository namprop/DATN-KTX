<template>
  <div
    class="mx-auto bg-white text-gray-900 p-10 mt-6 shadow-lg rounded-lg font-[Times_New_Roman] print:shadow-none print:rounded-none"
    style="width: 210mm; min-height: 297mm"
  >
    <!-- HEADER -->
    <div class="text-center">
      <p class="font-bold uppercase">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
      <p class="font-semibold">Độc lập - Tự do - Hạnh phúc</p>
      <div class="w-32 h-px bg-black mx-auto my-2"></div>
    </div>

    <div class="text-center mt-6 mb-4">
      <p class="font-bold uppercase text-lg underline">Hợp đồng ký túc xá</p>
    </div>

    <!-- BODY -->
    <div class="mb-4">
      <p>
        <span class="font-semibold">
          Kính gửi: Trường trung học phổ thông Thanh Oai A
        </span>
      </p>
    </div>

    <div class="space-y-3 leading-relaxed">
      <div>
        - Họ và tên học sinh:
        <input
          :value="contract?.student?.full_name || ''"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        Mã số học sinh:
        <input
          :value="contract?.student?.student_code || ''"
          type="text"
          class="border-b border-black w-1/4 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
      </div>

      <div>
        - Ngày tháng năm sinh:
        <input
          :value="contract?.student?.date_of_birth || ''"
          type="text"
          class="border-b border-black w-1/3 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        Giới tính:
        <input
          :value="contract?.student?.gender === 'Male' ? 'Nam' : 'Nữ' || ''"
          class="border-b border-black w-20 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
      </div>

      <div>
        - Phòng đăng ký:
        <input
          :value="contract?.student?.room?.room_code || ''"
          type="text"
          class="border-b border-black w-1/4 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        ({{ contract?.student?.room?.area || "---" }} -
        {{
          contract?.student?.gender === "Male" ? "Phòng Nam" : "Phòng Nữ" || ""
        }} )
      </div>

      <div>
        - Thời hạn hợp đồng:
        <input
          :value="contract?.start_date || ''"
          type="text"
          class="border-b border-black w-40 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
        đến
        <input
          :value="contract?.end_date || ''"
          type="text"
          class="border-b border-black w-40 focus:outline-none ml-1 bg-gray-100"
          readonly
        />
      </div>
    </div>

    <!-- Nút gia hạn hợp đồng -->
    <div v-if="canExtendContract" class="mt-6 text-center">
      <button
        @click="showExtendPopup = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-full font-semibold transition"
      >
        Gia hạn hợp đồng
      </button>
    </div>

    <div class="text-right mr-10 mt-6">
      .........., ngày..... tháng..... năm.....
    </div>

    <!-- SIGNATURE AREA -->
    <div class="grid grid-cols-2 gap-6 mt-5 text-center text-sm">
      <div>
        <p class="font-semibold uppercase">
          Xác nhận của ban quản lý ký túc xá
        </p>
        <p class="italic">(Thuộc trường THPT Thanh Oai A)</p>
      </div>
      <div>
        <p class="font-semibold uppercase">Người làm đơn</p>
        <p class="italic">(Ký và ghi rõ họ tên)</p>
      </div>
    </div>

    <div class="pt-5">
      <NuxtLink
        to="/student/page"
        class="text-blue-600 underline hover:text-blue-800"
      >
        Quay lại Trang
      </NuxtLink>
    </div>

     <div class="pt-5">
      <NuxtLink
        to="/"
        class="text-blue-600 underline hover:text-blue-800"
      >
        Về trang chủ
      </NuxtLink>
    </div>

    <!-- Popup gia hạn hợp đồng -->
    <div
      v-if="showExtendPopup"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white rounded-xl p-6 w-[400px] shadow-xl text-center">
        <h2 class="text-lg font-bold text-gray-800 mb-3">
          Gia hạn hợp đồng
        </h2>
        <p class="text-gray-600 mb-4">
          Chọn ngày kết thúc mới cho hợp đồng của bạn:
        </p>
        <input
          type="date"
          v-model="newEndDate"
          class="border rounded-lg px-3 py-2 w-full mb-4"
          :min="today"
        />
        <div class="flex justify-center gap-4">
          <button
            @click="showExtendPopup = false"
            class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100"
          >
            Hủy
          </button>
          <button
            @click="extendContract"
            class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold"
          >
            Xác nhận
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import useAxios from "@/composables/useAxios";

const api = useAxios();
const contract = ref(null);

const showExtendPopup = ref(false);
const newEndDate = ref("");
const today = new Date().toISOString().split("T")[0];

const canExtendContract = ref(false);

const checkExtendEligibility = () => {
  if (!contract.value?.end_date) return;

  const todayDate = new Date();
  const endDate = new Date(contract.value.end_date);
  const diffDays = Math.ceil((endDate - todayDate) / (1000 * 60 * 60 * 24));

  // Hết hạn hoặc còn <=7 ngày
  if (diffDays <= 7) canExtendContract.value = true;
  else canExtendContract.value = false;
};

const extendContract = async () => {
  if (!newEndDate.value) {
    alert("Vui lòng chọn ngày kết thúc mới!");
    return;
  }

  try {
    const res = await api.post("/student/extendcontract", {
      end_date: newEndDate.value,
    });

    if (res.data.status) {
      alert(res.data.message);
      contract.value.end_date = newEndDate.value;
      canExtendContract.value = false;
      showExtendPopup.value = false;
    } else {
      alert(res.data.message);
    }
  } catch (err) {
    console.error(err);
    alert("Lỗi khi gia hạn hợp đồng!");
  }
};

onMounted(async () => {
  try {
    const { data } = await api.get("/student/showmycontract");
    if (data.status) {
      contract.value = data.contract;
      checkExtendEligibility();
    } else {
      alert(data.message || "Không lấy được hợp đồng.");
    }
  } catch (error) {
    console.error(error);
    alert("Lỗi khi tải hợp đồng.");
  }
});
</script>
