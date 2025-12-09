<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo Hóa đơn Ký túc xá</title>
</head>

<body
    style="font-family: 'Arial', sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div
        style="max-width: 600px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">

        <div style="text-align: center; padding-bottom: 15px; border-bottom: 2px solid #007bff;">
            <h1 style="color: #007bff; margin: 0; font-size: 24px;">
                THÔNG BÁO HÓA ĐƠN KÝ TÚC XÁ
            </h1>
            <p style="color: #6c757d; margin: 5px 0 0;">Ký túc xá Trường THPT Thanh Oai A</p>
        </div>

        <div style="padding: 20px 0;">
            <h2 style="color: #00796b; font-size: 20px; margin-top: 0;">
                Kính chào Quý phụ huynh {{ $user->name }},
            </h2>

            <p>Hệ thống Ký túc xá xin thông báo: Hóa đơn mới cho sinh viên **{{ $student->full_name ?? $student->name }}** đã được tạo thành công.</p>

            <p>Dưới đây là chi tiết hóa đơn tháng **{{ $payment->month }}/{{ $payment->year }}**.</p>

            <h3 style="color: #004d40; margin-top: 25px;">Chi tiết Hóa đơn:</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; width: 40%;"><strong>Học sinh:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right;">{{ $student->full_name ?? $student->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd;"><strong>Mã hóa đơn:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right;">{{ $payment->payment_code }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd;"><strong>Nội dung:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right;">{{ $payment->description ?? 'Phí KTX hàng tháng' }}</td>
                </tr>

                <tr style="font-size: 1.1em; font-weight: bold; background-color: #e6f7ff;">
                    <td style="padding: 10px 0; border-bottom: 1px solid #007bff; border-top: 1px solid #007bff;"><strong>TỔNG TIỀN CẦN THANH TOÁN:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #007bff; border-top: 1px solid #007bff; text-align: right; color: #dc3545;">
                        **{{ number_format($payment->total_amount, 0, ',', '.') }} VNĐ**
                    </td>
                </tr>
                <tr style="font-weight: bold;">
                    <td style="padding: 8px 0;"><strong>Hạn thanh toán:</strong></td>
                    <td style="padding: 8px 0; text-align: right; color: #ff9800;">
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                    </td>
                </tr>
            </table>

            {{-- <div style="margin-top: 30px; text-align: center; padding: 15px; border: 1px solid #007bff; border-radius: 4px;">
                <p>Vui lòng đăng nhập hệ thống để xem chi tiết hóa đơn và tiến hành thanh toán trước thời hạn.</p>
                <a href="[ĐIỀN ĐƯỜNG DẪN ĐĂNG NHẬP/THANH TOÁN]"
                   style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px;">
                    THANH TOÁN NGAY
                </a>
            </div> --}}

            <p style="margin-top: 25px;">Trân trọng,</p>
            <p>
                <strong>Ban Quản lý Ký túc xá</strong><br>
                Trường THPT Thanh Oai A
            </p>
        </div>

        <div
            style="text-align: center; padding-top: 15px; border-top: 1px solid #eeeeee; font-size: 12px; color: #999999;">
            <p>Mọi thắc mắc, vui lòng liên hệ [SỐ ĐIỆN THOẠI HỖ TRỢ].</p>
            <p>&copy; {{ date('Y') }} Trường THPT Thanh Oai A.</p>
        </div>

    </div>
</body>

</html>
