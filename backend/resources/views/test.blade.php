<h2>Chào phụ huynh {{$user->name}} đến mới ký túc xá Trường THPT Thanh Oai A</h2>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thư chào mừng từ Ký túc xá</title>
</head>
<body style="font-family: 'Arial', sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div style="max-width: 600px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);">

        <div style="text-align: center; padding-bottom: 15px; border-bottom: 1px solid #eeeeee;">
            <h1 style="color: #00796b; margin: 0; font-size: 24px;">
                TRƯỜNG THPT THANH OAI A
            </h1>
            <p style="color: #666666; margin: 5px 0 0;">Ký túc xá - Nơi an tâm học tập và phát triển</p>
        </div>

        <div style="padding: 20px 0;">
            <h2 style="color: #00796b; font-size: 20px; margin-top: 0;">
                Chào mừng Quý phụ huynh {{ $user->name }}
            </h2>

            <p>Kính gửi **Quý phụ huynh {{ $user->name }}**, </p>

            <p>Trường THPT Thanh Oai A xin trân trọng thông báo việc đăng ký cho học sinh của Quý phụ huynh vào Ký túc xá đã **hoàn tất thành công**.</p>

            <p>Chúng tôi rất vui được chào đón và cam kết cung cấp một môi trường sống **an toàn, sạch sẽ và kỷ luật** để hỗ trợ tối đa cho việc học tập và sinh hoạt của học sinh. Sự an tâm của Quý phụ huynh là ưu tiên hàng đầu của chúng tôi.</p>

            <h3 style="color: #004d40; border-bottom: 1px dashed #dddddd; padding-bottom: 5px;">Thông tin đã đăng ký:</h3>
            <ul style="list-style-type: none; padding-left: 0;">
                <li style="margin-bottom: 8px;"><strong>Tên Phụ huynh:</strong> {{ $user->name }}</li>
                <li style="margin-bottom: 8px;"><strong>Email liên hệ:</strong> {{ $user->email }}</li>
                {{-- Nếu bạn có thể truy cập thông tin học sinh qua $user->student, bạn có thể thêm: --}}
                {{-- <li style="margin-bottom: 8px;"><strong>Học sinh liên quan:</strong> {{ $user->student->full_name ?? 'Đang cập nhật' }}</li> --}}
            </ul>

            <p style="margin-top: 20px; padding: 10px; background-color: #e0f2f1; border-left: 4px solid #00796b; font-style: italic;">
                **LƯU Ý QUAN TRỌNG:** Trong vòng 24 giờ tới, Ban Quản lý Ký túc xá sẽ gửi một email chi tiết về nội quy, hướng dẫn nhập học, và các mốc thời gian quan trọng. Xin Quý phụ huynh vui lòng kiểm tra hộp thư thường xuyên.
                **Đồng thời thanh toán tiền cọc nhanh nhất để xác nhận đăng ký học tập.**
            </p>

            <p>Nếu cần hỗ trợ khẩn cấp, Quý phụ huynh vui lòng liên hệ:</p>
            <p>
                ☎️ **Điện thoại:** [Điền số điện thoại liên hệ KTX]<br>
                📧 **Email:** [Điền email KTX]
            </p>
        </div>

        <div style="text-align: center; padding-top: 15px; border-top: 1px solid #eeeeee; font-size: 12px; color: #999999;">
            <p>&copy; {{ date('Y') }} Trường THPT Thanh Oai A. Xin chân thành cảm ơn.</p>
        </div>

    </div>
</body>
</html>
