<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class NewPPController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = 20;

        $announcements = Announcements::orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            "status" => true,
            "message" => "Lấy danh sách bản tin thành công",
            "data" => $announcements->items(),
            "pagination" => [
                "total" => $announcements->total(),
                "per_page" => $announcements->perPage(),
                "current_page" => $announcements->currentPage(),
                "last_page" => $announcements->lastPage(),
            ]
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // 'user_id' => 'nullable|exists:users,id|unique:announcements,user_id',
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'status'  => 'nullable|in:Active,Inactive',
                'type'    => 'nullable|string',
            ],
            [
                'title.required' => 'Tiêu đề không được để trống',
                'content.required' => 'Nội dung không được để trống',
                'image.image' => 'File phải là hình ảnh',
                'image.mimes' => 'Ảnh phải là jpg, jpeg, png, webp',
                // 'image.max'   => 'Ảnh tối đa 2MB',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Dữ liệu không hợp lệ",
                "errors" => $validator->errors()
            ], 422);
        }

        // ✅ xử lý upload ảnh
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/announcements', 'public');
        }

        $announcement = Announcements::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'content' => $request->content,
            'image'   => $imagePath, // lưu path
            'status'  => $request->status ?? 'Active',
            'type'    => $request->type,
        ]);

        return response()->json([
            "status" => true,
            "message" => "Thêm bản tin thành công",
            "data" => $announcement
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $announcement = Announcements::find($id);

        if (!$announcement) {
            return response()->json([
                "status" => false,
                "message" => "Bản tin không tồn tại"
            ], 404);
        }

        // ✅ validate
        $validator = Validator::make(
            $request->all(),
            [
                'title'   => 'required|string|max:255',
                'content' => 'required|string',
                'image'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'status'  => 'nullable|in:Active,Inactive',
                'type'    => 'nullable|string',
            ],
            [
                'title.required' => 'Tiêu đề không được để trống',
                'content.required' => 'Nội dung không được để trống',
                'image.image' => 'File phải là hình ảnh',
                'image.mimes' => 'Ảnh phải là jpg, jpeg, png, webp',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Dữ liệu không hợp lệ",
                "errors" => $validator->errors()
            ], 422);
        }

        // ✅ xử lý ảnh mới (nếu có)
        $imagePath = $announcement->image;

        if ($request->hasFile('image')) {

            // xóa ảnh cũ
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            // upload ảnh mới
            $imagePath = $request->file('image')
                ->store('uploads/announcements', 'public');
        }

        // ✅ update dữ liệu
        $announcement->update([
            'title'   => $request->title,
            'content' => $request->content,
            'image'   => $imagePath,
            'status'  => $request->status ?? $announcement->status,
            'type'    => $request->type,
            'user_id' => Auth::id(), // người sửa
        ]);

        return response()->json([
            "status" => true,
            "message" => "Cập nhật bản tin thành công",
            "data" => $announcement
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $announcement = Announcements::find($id);

        if (!$announcement) {
            return response()->json([
                "status" => false,
                "message" => "Bản tin không tồn tại"
            ], 404);
        }

        if (Storage::disk('public')->exists($announcement->image)) {
            Storage::disk('public')->delete($announcement->image);
        }

        $announcement->delete();
        return response()->json([
            "status" => true,
            "message" => "Xóa bản tin thành công"
        ], 200);
    }
}
