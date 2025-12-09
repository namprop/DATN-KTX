<template>
  <tr>
    <th
      v-for="title in titleTable"
      :key="title"
      class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
    >
      {{ title }}
    </th>
  </tr>
</template>

<script setup>
defineProps({
  titleTable: {
    type: Array,
    required: true,
  },
});
</script>
