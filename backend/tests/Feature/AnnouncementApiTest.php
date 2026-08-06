<?php

namespace Tests\Feature;

use App\Models\Announcements;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AnnouncementApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_list_only_returns_active_articles(): void
    {
        $author = $this->user('Admin', 'admin@example.test');
        $this->article($author, 'Bài đang hiển thị', 'Active');
        $this->article($author, 'Bài đang ẩn', 'Inactive');

        $this->getJson('/api/newspapers')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Bài đang hiển thị');
    }

    public function test_inactive_article_cannot_be_viewed_publicly(): void
    {
        $article = $this->article($this->user('Admin', 'admin@example.test'), 'Bài nháp', 'Inactive');

        $this->getJson("/api/newspapers/{$article->id}")->assertNotFound();
    }

    public function test_student_cannot_manage_articles(): void
    {
        Sanctum::actingAs($this->user('Student', 'student@example.test'));

        $this->postJson('/api/admin/announcement', [
            'title' => 'Không được tạo',
            'content' => 'Nội dung',
            'type' => 'news',
        ])->assertForbidden();
    }

    public function test_admin_can_create_and_search_articles(): void
    {
        Sanctum::actingAs($this->user('Admin', 'admin@example.test'));

        $this->postJson('/api/admin/announcement', [
            'title' => 'Thông báo bảo trì',
            'content' => 'Nội dung bảo trì ký túc xá.',
            'type' => 'notice',
            'status' => 'Active',
        ])->assertCreated()
            ->assertJsonPath('data.type', 'notice')
            ->assertJsonPath('data.status', 'Active');

        $this->getJson('/api/admin/announcement?search=bảo+trì')
            ->assertOk()
            ->assertJsonCount(1, 'data');
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

    private function article(User $author, string $title, string $status): Announcements
    {
        return Announcements::create([
            'user_id' => $author->id,
            'title' => $title,
            'content' => 'Nội dung kiểm thử',
            'status' => $status,
            'type' => 'news',
        ]);
    }
}
