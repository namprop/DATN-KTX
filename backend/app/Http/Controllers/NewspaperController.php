<?php

namespace App\Http\Controllers;

use App\Models\Announcements;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class NewspaperController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in(['news', 'event', 'notice'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $newspapers = Announcements::query()
            ->with('user:id,name')
            ->where('status', 'Active')
            ->when($validated['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($validated['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->latest()
            ->paginate($validated['per_page'] ?? 9);

        return response()->json([
            'status' => true,
            'data' => collect($newspapers->items())->map(fn ($item) => $this->present($item, false)),
            'pagination' => [
                'total' => $newspapers->total(),
                'per_page' => $newspapers->perPage(),
                'current_page' => $newspapers->currentPage(),
                'last_page' => $newspapers->lastPage(),
            ],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $newspaper = Announcements::query()
            ->with('user:id,name')
            ->where('status', 'Active')
            ->findOrFail($id);

        $related = Announcements::query()
            ->where('status', 'Active')
            ->whereKeyNot($newspaper->id)
            ->when($newspaper->type, fn ($query) => $query->where('type', $newspaper->type))
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn ($item) => $this->present($item, false));

        return response()->json([
            'status' => true,
            'data' => $this->present($newspaper),
            'related' => $related,
        ]);
    }

    private function present(Announcements $announcement, bool $withContent = true): array
    {
        $data = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'summary' => mb_strimwidth(strip_tags($announcement->content), 0, 220, '...'),
            'image_url' => $announcement->image ? url(Storage::url($announcement->image)) : null,
            'type' => $announcement->type ?: 'news',
            'author' => $announcement->user?->name,
            'created_at' => $announcement->created_at,
        ];

        if ($withContent) {
            $data['content'] = $announcement->content;
        }

        return $data;
    }
}
