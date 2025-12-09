<script setup>
definePageMeta({
  layout: "student",
  middleware: "auth",
});

import { ref, onMounted } from "vue";
import useAxios from "@/composables/useAxios";
import { useRouter } from "vue-router";

const api = useAxios();
const router = useRouter();
const { user } = useAuth();

// 🔹 Step hiện tại
const step = ref(0);

const isLoading = ref(false);

// 🔹 Dữ liệu form
const formData = ref({
  fullName: "",
  gender: "",
  dob: "",
  studentId: "",
  phone: "",
  email: "",
  startDate: "",
  endDate: "",
  room: null,
});

// 🔹 Danh sách phòng
const roomList = ref([]);

// 🔹 Điền sẵn thông tin user và tải danh sách phòng an toàn
onMounted(async () => {
  if (user.value) {
    formData.value.fullName = user.value.name || "";
    formData.value.email = user.value.email || "";
  }

  try {
    const res = await api.get("/student/displayroom");

    if (!res.data?.status || !Array.isArray(res.data.data)) {
      console.warn("API không trả về danh sách phòng hợp lệ.");
      return;
    }

    roomList.value = res.data.data.map((r) => {
      const desc = typeof r.description === "string" ? r.description : "";
      const parts = desc.split("-");
      return {
        id: r.id,
        code: r.room_code || "",
        area: parts[0]?.trim() || "",
        type: parts[1]?.trim() || "",
        capacity: typeof r.capacity === "number" ? r.capacity : 4,
        available:
          (typeof r.capacity === "number" ? r.capacity : 4) -
          (typeof r.students_count === "number" ? r.students_count : 0),
        status: r.status || "Không rõ",
      };
    });
  } catch (err) {
    console.error("Lỗi khi tải danh sách phòng:", err);
    roomList.value = []; // đảm bảo luôn là mảng
  }
});

// 🔹 Bước 1: thông tin cá nhân -> qua bước 2
function handleNextStep(updatedData) {
  Object.assign(formData.value, updatedData);
  step.value = 2;
}

// 🔹 Bước 2: chọn phòng
function handleRoomConfirm(room) {
  if (room.available <= 0) {
    alert("Phòng đã đầy. Vui lòng chọn phòng khác.");
    return;
  }
  formData.value.room = room;
  step.value = 3;
}

// 🔹 Bước 3: submit đơn
const handleSubmit = async () => {
  if (!formData.value.room) {
    alert("Bạn chưa chọn phòng.");
    return;
  }

  const payload = {
    full_name: formData.value.fullName,
    gender:
      formData.value.gender === "Nam"
        ? "Male"
        : formData.value.gender === "Nữ"
        ? "Female"
        : "Other",
    date_of_birth: formData.value.dob,
    student_code: formData.value.studentId,
    phone: formData.value.phone,
    start_date: formData.value.startDate,
    end_date: formData.value.endDate,
    room_id: formData.value.room.id,
  };

  try {
    await api.post("/student/submitonboardingform", payload);
    alert("Nộp đơn thành công!");
    step.value = 4;
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors;
      let msg = "Vui lòng kiểm tra lại các thông tin:\n";
      for (const key in errors) {
        msg += `- ${errors[key].join(", ")}\n`;
      }
      alert(msg);
    } else {
      alert(error.response?.data?.message || "Đã có lỗi xảy ra.");
    }
    console.error("Error:", error);
  }
};

const checkContractStatus = async () => {
  try {
    const res = await api.post("/student/checkstatusstudent");
    const { contract_status } = res.data;

    if (
      contract_status === "Active" ||
      contract_status === "Approved" ||
      contract_status === "Completed"
    ) {
      router.push("/student/page");
    }
  } catch (err) {
    console.error("Lỗi kiểm tra trạng thái:", err);
  }
};

onMounted(() => {
  checkContractStatus();
});
</script>

<template>
  <div
    v-if="isLoading"
    class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-[9999]"
  >
    <div
      class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin"
    ></div>
  </div>
  <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
    <!-- STEP 0: TRANG CHÀO MỪNG -->
    <StepWelcome v-if="step === 0" @next="step = 1" />

    <!-- STEP 1: THÔNG TIN CÁ NHÂN -->
    <StudentPersonalInfo
      v-if="step === 1"
      :formData="formData"
      @back="step = 0"
      @next="handleNextStep"
    />

    <!-- STEP 2: CHỌN PHÒNG -->
    <SelectRoomStep
      v-if="step === 2"
      :rooms="roomList"
      :studentGender="formData.gender"
      @back="step = 1"
      @confirm="handleRoomConfirm"
    />

    <!-- STEP 3: ĐƠN XIN VÀO KTX -->
    <ConfirmApplicationStep
      v-if="step === 3"
      :formData="formData"
      :selectedRoom="formData.room"
      @back="step = 2"
      @submit="handleSubmit"
    />

    <!-- STEP 4: HOÀN TẤT -->
    <StepSuccess v-if="step === 4" />
  </main>
</template>
