# DATN-KTX Frontend

Giao diện Nuxt 3/Vue 3 cho hệ thống quản lý ký túc xá.

## Cấu hình

Tạo `.env` nếu backend không dùng địa chỉ mặc định:

```dotenv
NUXT_PUBLIC_API_BASE=http://localhost:8000/api
NUXT_PUBLIC_STORAGE_BASE=http://localhost:8000/storage
```

## Chạy local

```bash
npm install
npm run dev
```

Ứng dụng chạy tại `http://localhost:4000`.

## Kiểm tra bản production

```bash
npm run build
npm run preview
```

Chi tiết kiến trúc, ERD và luồng nghiệp vụ nằm trong [README dự án](../README.md).
