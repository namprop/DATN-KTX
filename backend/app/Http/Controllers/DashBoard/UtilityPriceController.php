<?php

namespace App\Http\Controllers\DashBoard;

use App\Http\Controllers\Controller;
use App\Models\UtilityPrice;
use Illuminate\Http\Request;

class UtilityPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $price = UtilityPrice::all();
        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách giá thành công',
            'data' => $price
        ]);
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
        $validated = $request->validate([
            'electricity_price' => 'required|numeric|min:0',
            'water_price' => 'required|numeric|min:0',
        ], [
            'electricity_price.required' => 'Vui lòng nhập giá điện',
            'electricity_price.numeric' => 'Giá điện phải là số',
            'water_price.required' => 'Vui lòng nhập giá nước',
            'water_price.numeric' => 'Giá nước phải là số',
        ]);

        $price = UtilityPrice::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Thêm giá điện nước thành công',
            'data' => $price
        ]);
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
        $price = UtilityPrice::find($id);

        if (!$price) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản ghi'
            ], 404);
        }

        $validated = $request->validate([
            'electricity_price' => 'required|numeric|min:0',
            'water_price' => 'required|numeric|min:0',
        ], [
            'electricity_price.required' => 'Vui lòng nhập giá điện',
            'water_price.required' => 'Vui lòng nhập giá nước',
        ]);

        $price->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật giá điện nước thành công',
            'data' => $price
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
