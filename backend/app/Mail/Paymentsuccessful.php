<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Models\Payment; // Import Model Payment
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Paymentsuccessful extends Mailable
{
    use Queueable, SerializesModels;

    // Khai báo thuộc tính công khai để tự động gán và truyền vào view
    public User $user;
    public Payment $payment; // KHAI BÁO BIẾN $payment

    /**
     * Tạo một đối tượng Mailable mới.
     */
    // Sử dụng Property Promotion (từ PHP 8.0)
    public function __construct(User $user, Payment $payment)
    {
        $this->user = $user;
        $this->payment = $payment;
    }

    /**
     * Lấy thông tin Envelope (Tiêu đề) của thư.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // Tiêu đề email chuyên nghiệp hơn
            subject: 'XÁC NHẬN: Thanh toán Hóa đơn Ký túc xá thành công',
        );
    }

    /**
     * Lấy định nghĩa nội dung thư.
     */
    public function content(): Content
    {
        return new Content(

            view: '/payment',

        );
    }

    /**
     * Lấy các tập tin đính kèm cho thư.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
