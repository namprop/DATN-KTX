<?php


namespace App\Http\Controllers;

use App\Models\Announcements;

class NewspaperController extends Controller
{
    //

    public function index()
    {
        //
        $newspapers = Announcements::all();
        return response()->json([
            'status' => true,
            'data' => $newspapers
        ]);
    }

    public function show($id)
    {
        //
        $newspaper = Announcements::find($id);
        if (!$newspaper) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy bản tin'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data' => $newspaper
        ]);
    }
}
