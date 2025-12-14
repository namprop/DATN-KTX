<!-- components/UserFormModal.vue -->
<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <!-- Overlay -->
      <div class="absolute inset-0 bg-black/40" @click="$emit('close')"></div>

      <div
        class="relative bg-white rounded-lg shadow-2xl w-full max-md:max-w-[400px] max-md:max-h-[65vh] max-w-2xl max-h-[90vh] overflow-y-auto"
      >
        <!-- Header -->
        <FormHeader :nameForm="title" @closes="$emit('close')" />

        <!-- Form Content -->
        <div class="px-6 py-4">
          <!-- @submit.prevent="$emit('submit', modelValue)" -->

          <form @submit.prevent="$emit('submit', modelValue)" class="space-y-5">
            <div v-for="(items, index) in fields" :key="index">
              <!-- Account Information -->
              <div v-if="items.account" class="border-b border-gray-200 pb-4">
                <h3
                  class="text-sm font-semibold text-gray-700 mb-3 flex items-center"
                >
                  <i :class="`${items.account.icon} mr-2 text-blue-600`"></i>
                  {{ items.account.title }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div v-for="field in items.account.fields" :key="field.key">
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                      {{ field.label }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                      >
                        <i :class="`${field.icon} text-gray-400 text-sm`"></i>
                      </div>
                      <input
                        :type="field.type"
                        v-model="modelValue[field.key]"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :placeholder="field.placeholder"
                      />
                    </div>

                    <p
                      v-if="mess && mess[field.key]"
                      class="text-red-600 text-[12px] mt-1 mx-1"
                    >
                      {{
                        Array.isArray(mess[field.key])
                          ? mess[field.key][0]
                          : mess[field.key]
                      }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Personal Information -->
              <!-- v-else-if="items.title === 'Thông tin cá nhân'" -->
              <div v-if="items.personal" class="border-b border-gray-200 pb-4">
                <h3
                  class="text-sm font-semibold text-gray-700 mb-3 flex items-center"
                >
                  <i :class="`${items.personal.icon} mr-2 text-blue-600`"></i>
                  {{ items.personal.title }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div
                    v-for="field in items.personal.fields"
                    :key="field.key"
                    :class="
                      ['Họ và tên', 'Địa chỉ'].includes(field.label)
                        ? 'md:col-span-2'
                        : ''
                    "
                  >
                    <label class="block text-xs font-medium text-gray-700 mb-1">
                      {{ field.label }} <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                      <div
                        class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                      >
                        <i :class="`${field.icon} text-gray-400 text-sm`"></i>
                      </div>
                      <input
                        v-if="field.type === 'text'"
                        v-model="modelValue[field.key]"
                        :type="`${field.type}`"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        :placeholder="`${field.placeholder}`"
                      />

                      <input
                        v-else-if="field.type === 'tel'"
                        v-model="modelValue[field.key]"
                        type="tel"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="0987654321"
                      />

                      <input
                        v-else-if="field.type === 'date'"
                        v-model="modelValue[field.key]"
                        :type="`${field.type}`"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />

                      <div v-else-if="field.type === 'select'">
                        <select
                          v-model="modelValue[field.key]"
                          class="w-full pl-9 pr-8 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none"
                        >
                          <option
                            v-for="opt in field.options"
                            :key="opt.value"
                            :value="opt.value"
                          >
                            {{ opt.text }}
                          </option>
                        </select>
                        <div
                          class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
                        >
                          <i
                            class="fas fa-chevron-down text-gray-400 text-xs"
                          ></i>
                        </div>
                      </div>

                      <textarea
                        v-model="modelValue[field.key]"
                        v-else-if="field.type === 'textarea'"
                        rows="2"
                        class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                        :placeholder="`${field.placeholder}`"
                      ></textarea>

                      <!-- Upload image -->
                      <div v-else-if="field.type === 'file'" class="space-y-2">
                        <input
                          type="file"
                          accept="image/*"
                          class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          @change="onFileChange($event, field.key)"
                        />

                        <!-- Preview image -->
                        <img
                          v-if="modelValue[field.key]"
                          :src="previewImage(modelValue[field.key])"
                          class="w-32 h-24 object-cover rounded border"
                          alt="Preview"
                        />
                      </div>

                      <p
                        v-if="mess && mess[field.key]"
                        class="text-red-600 text-[12px] mt-1 mx-1"
                      >
                        {{
                          Array.isArray(mess[field.key])
                            ? mess[field.key][0]
                            : mess[field.key]
                        }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Role & Permissions -->

              <!-- Notes -->
            </div>
            <FormFooter @closes="$emit('close')" />
          </form>
        </div>

        <!-- Footer Buttons -->
      </div>
    </div>
  </transition>
</template>

<script setup>
import { ref, computed } from "vue";
import { FormFooter, FormHeader } from "#components";
import useAxios from "@/composables/useAxios";

const props = defineProps({
  show: Boolean,
  title: String,

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

// xử lý khi chọn file
const onFileChange = (event, key) => {
  const file = event.target.files[0];
  if (!file) return;

  emit("update:modelValue", {
    ...props.modelValue,
    [key]: file,
  });
};

// preview ảnh (file hoặc url)
const previewImage = (fileOrUrl) => {
  if (fileOrUrl instanceof File) {
    return URL.createObjectURL(fileOrUrl);
  }
  return fileOrUrl; // url từ server
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
