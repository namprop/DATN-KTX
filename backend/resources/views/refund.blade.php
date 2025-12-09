<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Hoàn tiền Cọc Thành công</title>
</head>

<body
    style="font-family: 'Arial', sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">

    <div
        style="max-width: 600px; margin: 20px auto; padding: 20px; background: #ffffff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">

        <div style="text-align: center; padding-bottom: 15px; border-bottom: 2px solid #ffc107;">
            <h1 style="color: #ffc107; margin: 0; font-size: 24px;">
                XÁC NHẬN HOÀN TIỀN THÀNH CÔNG
            </h1>
            <p style="color: #6c757d; margin: 5px 0 0;">Ký túc xá Trường THPT Thanh Oai A</p>
        </div>

        <div style="padding: 20px 0;">
            <h2 style="color: #00796b; font-size: 20px; margin-top: 0;">
                Kính gửi Quý phụ huynh {{ $user->name }},
            </h2>

            <p>Trường THPT Thanh Oai A xin trân trọng thông báo xác nhận việc **hoàn trả tiền cọc** cho Quý phụ huynh đã được xử lý thành công.</p>

            <p>Email này được gửi để xác nhận thông tin giao dịch hoàn trả, đồng thời xác nhận rằng thông tin học sinh và phụ huynh đã được xóa khỏi hệ thống của Nhà trường theo yêu cầu.</p>


            <h3 style="color: #004d40; margin-top: 25px;">Chi tiết Hoàn trả:</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd;"><strong>Số tiền đã Hoàn:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right; color: #007bff;">
                        **{{ number_format($payment->total_amount, 0, ',', '.') }} VND**
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd;"><strong>Mã Hoàn trả/Giao dịch:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px dashed #dddddd; text-align: right;">{{ $payment->vnp_transaction_no ?? $payment->payment_code }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Thời gian xử lý:</strong></td>
                    <td style="padding: 8px 0; text-align: right;">{{ now()->format('d/m/Y') }}</td>
                </tr>
            </table>

            <div
                style="margin-top: 25px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;">
                <p style="margin: 0; font-weight: bold; color: #856404;">
                    ⚠️ **LƯU Ý:**
                </p>
                <p style="margin: 5px 0 0;">
                    Khoản tiền hoàn trả sẽ được chuyển vào tài khoản Quý phụ huynh đã cung cấp, thông thường sẽ mất từ 1-3 ngày làm việc tùy thuộc vào ngân hàng xử lý.
                </p>
            </div>

            <p style="margin-top: 25px;">Xin chân thành cảm ơn Quý phụ huynh đã đồng hành cùng Nhà trường trong thời gian qua. Mọi thắc mắc liên quan đến quá trình hoàn tiền, xin vui lòng liên hệ Ban Quản lý Ký túc xá:</p>
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
