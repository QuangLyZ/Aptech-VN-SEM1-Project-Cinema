# CineBook API

Laravel app dùng Supabase PostgreSQL làm database và expose API để team thao tác dữ liệu.

## 1. Tạo database trên Supabase

1. Tạo project mới trên Supabase.
2. Vào `SQL Editor`.
3. Copy toàn bộ nội dung file `database/schema.sql` và chạy.
4. Kiểm tra các bảng như `movies`, `cinemas`, `showtimes`, `feedbacks` đã được tạo.

## 2. Cấu hình Laravel nối tới Supabase

Đổi `.env` sang PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=db.<your-project-ref>.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<your-db-password>
DB_SSLMODE=require
```

Nếu Supabase cung cấp `Session pooler` thay vì host trực tiếp thì dùng host/port mà Supabase ghi trong phần `Connect`.

## 3. API hiện có

Base URL local:

```text
http://127.0.0.1:8000/api
```

Endpoints:

- `GET /api/health`
- `GET|POST|PUT|PATCH|DELETE /api/movies`
- `GET|POST|PUT|PATCH|DELETE /api/cinemas`
- `GET|POST|PUT|PATCH|DELETE /api/showtimes`
- `GET|POST|GET by id|DELETE /api/feedbacks`

## 4. Ví dụ gọi API

Tạo phim:

```bash
curl -X POST http://127.0.0.1:8000/api/movies \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Avengers",
    "genre": "Action",
    "duration": 143,
    "release_date": "2026-04-01"
  }'
```

Lấy danh sách phim:

```bash
curl http://127.0.0.1:8000/api/movies
```

Tạo feedback:

```bash
curl -X POST http://127.0.0.1:8000/api/feedbacks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "UI booking",
    "context": "Need clearer seat selection flow"
  }'
```

## 5. Chạy project

```bash
composer install
php artisan key:generate
php artisan serve
```

## 6. Ghi chú triển khai

- Không để frontend hoặc team client dùng `service_role` key của Supabase.
- Nếu cần phân quyền team nội bộ, nên để Laravel làm API trung gian rồi thêm auth sau.
- Schema đang dùng lowercase snake_case để tương thích tốt với PostgreSQL và Eloquent.
