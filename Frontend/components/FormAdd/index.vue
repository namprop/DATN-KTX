<!-- components/UserFormModal.vue -->
<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <div class="absolute inset-0 bg-black/40" @click="$emit('close')"></div>

      <div
        class="bg-white w-full max-w-6xl max-h-[90vh] mx-4 p-6 rounded-lg shadow-lg relative overflow-y-auto"
      >
        <h1 class="text-xl font-semibold mb-4">
          {{ modelValue?.id ? "Thông Tin Thành Viên" : "Thêm Thành Viên" }}
        </h1>

        <form
          @submit.prevent="$emit('submit', modelValue)"
          class="grid grid-cols-2 gap-4"
        >
          <div
            v-for="field in fields"
            :key="field.key"
            class="flex flex-col"
            :class="field.full ? 'col-span-2' : ''"
          >
            <label class="mb-1 font-medium">
              {{ labels[field.key] ?? field.label }}
            </label>

            <!-- Select -->
            <select
              v-if="field.type === 'select'"
              v-model="modelValue[field.key]"
              class="w-full border border-gray-300 rounded px-3 py-3"
            >
              <option disabled value="">-- Chọn --</option>
              <option
                v-for="option in field.options"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </option>
            </select>

            <!-- Textarea -->
            <textarea
              v-else-if="field.type === 'textarea'"
              v-model="modelValue[field.key]"
              class="w-full border border-gray-300 rounded px-3 py-6 resize-none"
              rows="3"
            ></textarea>

            <!-- File upload -->
            <div v-else-if="field.type === 'file'" class="flex flex-col gap-2">
              <input
                type="file"
                accept="image/*"
                class="w-full border border-gray-300 rounded px-3 py-3"
                @change="onFileChange($event, field.key)"
              />
              <img
                v-if="modelValue[field.key]"
                :src="previewImage(modelValue[field.key])"
                alt="Preview"
                class="mt-2 w-24 h-24 object-cover rounded border"
              />
            </div>

            <!-- Default input -->
            <input
              v-else
              v-model="modelValue[field.key]"
              :type="field.type"
              class="w-full border border-gray-300 rounded px-3 py-3"
            />

            <p v-if="mess && mess[field.key]" class="text-red-600 text-sm mt-1">
              {{ mess[field.key][0] }}
            </p>
          </div>

          <!-- Buttons full width dưới cùng -->
          <div class="col-span-2 flex justify-end gap-2 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
            >
              Hủy
            </button>
            <button
              type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Lưu
            </button>
          </div>
        </form>
      </div>
    </div>
  </transition>
</template>

<script setup>
import useAxios from "@/composables/useAxios";

const props = defineProps({
  show: Boolean,
  modelValue: {
    type: Object,
    default: () => ({}),
  },
  fields: {
    type: Array,
    required: true,
  },
  labels: {
    type: Object,
    default: () => ({}),
  },
  mess: {
    type: Object,
    default: () => ({}),
  },
});
const emit = defineEmits(["close", "submit", "update:modelValue"]);

const api = useAxios();

// Xử lý file input
const onFileChange = (event, key) => {
  const file = event.target.files[0];
  if (file) {
    emit("update:modelValue", {
      ...props.modelValue,
      [key]: file,
    });
  }
};

const previewImage = (fileOrUrl) => {
  if (fileOrUrl instanceof File) {
    return URL.createObjectURL(fileOrUrl);
  }
  return fileOrUrl; // nếu server trả về URL ảnh
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
