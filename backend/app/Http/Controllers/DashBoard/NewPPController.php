<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewPPController extends Controller
{
    private const TYPES = ['news', 'event', 'notice'];

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'type' => ['nullable', Rule::in(self::TYPES)],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $announcements = Announcements::query()
            ->with('user:id,name')
            ->when($validated['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($validated['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($validated['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->latest()
            ->paginate($validated['per_page'] ?? 20);

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách bài viết thành công.',
            'data' => collect($announcements->items())->map(fn ($item) => $this->present($item)),
            'pagination' => [
                'total' => $announcements->total(),
                'per_page' => $announcements->perPage(),
                'current_page' => $announcements->currentPage(),
                'last_page' => $announcements->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules());
        $validated['user_id'] = $request->user()->id;
        $validated['status'] ??= 'Active';
        $validated['type'] ??= 'news';

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/announcements', 'public');
        }

        $announcement = Announcements::create($validated)->load('user:id,name');

        return response()->json([
            'status' => true,
            'message' => 'Thêm bài viết thành công.',
            'data' => $this->present($announcement),
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $announcement = Announcements::with('user:id,name')->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $this->present($announcement),
        ]);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $announcement = Announcements::findOrFail($id);
        $validated = $request->validate($this->rules(true));
        $oldImage = $announcement->image;

        if ($request->boolean('remove_image')) {
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/announcements', 'public');
        }

        unset($validated['remove_image']);
        $validated['user_id'] = $request->user()->id;
        $announcement->update($validated);

        if ($oldImage && $oldImage !== $announcement->image) {
            Storage::disk('public')->delete($oldImage);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật bài viết thành công.',
            'data' => $this->present($announcement->load('user:id,name')),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $announcement = Announcements::findOrFail($id);
        $image = $announcement->image;
        $announcement->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return response()->json([
            'status' => true,
            'message' => 'Xóa bài viết thành công.',
        ]);
    }

    private function rules(bool $updating = false): array
    {
        $presence = $updating ? 'sometimes' : 'required';

        return [
            'title' => [$presence, 'string', 'max:255'],
            'content' => [$presence, 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::in(['Active', 'Inactive'])],
            'type' => ['nullable', Rule::in(self::TYPES)],
        ];
    }

    private function present(Announcements $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'image' => $announcement->image,
            'image_url' => $announcement->image ? url(Storage::url($announcement->image)) : null,
            'status' => $announcement->status,
            'type' => $announcement->type,
            'author' => $announcement->user?->name,
            'created_at' => $announcement->created_at,
            'updated_at' => $announcement->updated_at,
        ];
    }
}
