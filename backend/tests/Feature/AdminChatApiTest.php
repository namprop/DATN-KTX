<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_use_system_assistant(): void
    {
        $this->postJson('/api/chat', ['message' => 'Có bao nhiêu phòng?'])
            ->assertUnauthorized();
    }

    public function test_student_cannot_use_admin_system_assistant(): void
    {
        Sanctum::actingAs($this->user('Student', 'student-chat@example.test'));

        $this->postJson('/api/chat', ['message' => 'Có bao nhiêu phòng?'])
            ->assertForbidden();
    }

    public function test_admin_question_includes_current_database_statistics(): void
    {
        config([
            'services.gemini.key' => 'test-key',
            'services.gemini.model' => 'gemini-test',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Hiện có 1/2 phòng đang được sử dụng.']]],
                ]],
            ]),
        ]);

        Sanctum::actingAs($this->user('Admin', 'admin-chat@example.test'));

        $usedRoom = Room::create([
            'room_code' => 'A101',
            'capacity' => 4,
            'status' => 'Available',
            'price' => 1000000,
        ]);

        Room::create([
            'room_code' => 'A102',
            'capacity' => 4,
            'status' => 'Available',
            'price' => 1000000,
        ]);

        Student::create([
            'user_id' => $this->user('Student', 'resident-chat@example.test')->id,
            'room_id' => $usedRoom->id,
            'student_code' => 'CHAT001',
            'full_name' => 'Sinh viên kiểm thử',
            'phone' => '0900000001',
            'gender' => 'Male',
            'date_of_birth' => '2005-01-01',
            'status' => 'Active',
        ]);

        $this->postJson('/api/chat', ['message' => 'Có bao nhiêu phòng đang sử dụng?'])
            ->assertOk()
            ->assertJsonPath('reply', 'Hiện có 1/2 phòng đang được sử dụng.');

        Http::assertSent(function (Request $request): bool {
            $instruction = data_get($request->data(), 'systemInstruction.parts.0.text', '');

            return str_contains($instruction, '"total":2')
                && str_contains($instruction, '"in_use":1');
        });
    }

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => $role . ' User',
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'Active',
        ]);
    }
}
