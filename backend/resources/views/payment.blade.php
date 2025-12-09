<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Thanh toán Tiền cọc</title>
</head>

<body
    style="font-family: 'Arial', sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div
        style="max-width: 600px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">

        <div style="text-align: center; padding-bottom: 15px; border-bottom: 2px solid #28a745;">
            <h1 style="color: #28a745; margin: 0; font-size: 24px;">
                XÁC NHẬN THANH TOÁN THÀNH CÔNG
            </h1>
            <p style="color: #6c757d; margin: 5px 0 0;">Ký túc xá Trường THPT Thanh Oai A</p>
        </div>

        <div style="padding: 20px 0;">
            <h2 style="color: #00796b; font-size: 20px; margin-top: 0;">
                Kính gửi Quý phụ huynh {{ $user->name }},
            </h2>

            <p>Chúng tôi xin thông báo xác nhận rằng chúng tôi đã nhận được khoản tiền thanh toán của Quý phụ huynh
                </p>

                <p>Email này được gửi để xác nhận thông tin giao dịch</p>



            <h3 style="color: #004d40; margin-top: 25px;">Chi tiết Giao dịch:</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd;"><strong>Số tiền:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right; color: #dc3545;">
                        {{ $payment->total_amount }} VND
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Nội Dung</strong></td>
                    <td style="padding: 8px 0; text-align: right;">{{$payment->description}}</td>
                </tr>
            </table>

            <div
                style="margin-top: 25px; padding: 15px; background-color: #f0f8ff; border: 1px solid #cce5ff; border-radius: 4px;">
                <p style="margin: 0; font-weight: bold; color: #004085;">
                    📌 **Bước tiếp theo:**
                </p>
                <p style="margin: 5px 0 0;">
                    Thông tin về ngày nhập Ký túc xá, danh sách vật dụng cần chuẩn bị và lịch họp phụ huynh sẽ được gửi
                    đến Quý phụ huynh trong email tiếp theo vào ngày **[Điền ngày cụ thể]**.
                </p>
            </div>

            <p style="margin-top: 25px;">Xin chân thành cảm ơn sự hợp tác của Quý phụ huynh. Mọi thắc mắc, xin vui lòng
                liên hệ Ban Quản lý Ký túc xá:</p>
            <p>
                ☎️ **Điện thoại:** [Điền số điện thoại liên hệ KTX]<br>
                📧 **Email:** [Điền email KTX]
            </p>
        </div>

        <div
            style="text-align: center; padding-top: 15px; border-top: 1px solid #eeeeee; font-size: 12px; color: #999999;">
            <p>&copy; {{ date('Y') }} Trường THPT Thanh Oai A. Trân trọng.</p>
        </div>

    </div>
</body>

</html>
