<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VnpayAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('vnpay.vnp_TmnCode', 'TESTCODE');
        config()->set('vnpay.vnp_HashSecret', 'test-secret');
        config()->set('vnpay.vnp_Url', 'https://sandbox.example.test/pay');
        config()->set('vnpay.vnp_ReturnUrl', 'http://localhost/api/vnpay/return');
    }

    public function test_guest_cannot_create_a_vnpay_payment(): void
    {
        $this->postJson('/api/vnpay/create', ['payment_id' => 1])
            ->assertUnauthorized();
    }

    public function test_student_cannot_pay_another_students_invoice(): void
    {
        [$owner, $payment] = $this->createStudentWithPayment('owner@example.test');
        [$otherUser] = $this->createStudentWithPayment('other@example.test');

        Sanctum::actingAs($otherUser);

        $this->postJson('/api/vnpay/create', ['payment_id' => $payment->payment_id])
            ->assertForbidden();
    }

    public function test_student_can_create_payment_url_for_own_unpaid_invoice(): void
    {
        [$user, $payment] = $this->createStudentWithPayment('student@example.test');
        Sanctum::actingAs($user);

        $this->postJson('/api/vnpay/create', ['payment_id' => $payment->payment_id])
            ->assertOk()
            ->assertJsonStructure(['payment_url']);
    }

    private function createStudentWithPayment(string $email): array
    {
        $user = User::create([
            'name' => 'Test Student',
            'email' => $email,
            'password' => 'password',
            'role' => 'Student',
            'status' => 'Active',
        ]);

        $room = Room::create([
            'room_code' => 'ROOM-' . uniqid(),
            'capacity' => 4,
            'status' => 'Available',
            'price' => 1000000,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'student_code' => 'SV-' . uniqid(),
            'full_name' => 'Test Student',
            'status' => 'Active',
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'room_id' => $room->id,
            'payment_code' => 'PAY-' . uniqid(),
            'total_amount' => 100000,
            'payment_status' => 'unpaid',
            'month' => '08',
            'year' => '2026',
        ]);

        return [$user, $payment];
    }
}
