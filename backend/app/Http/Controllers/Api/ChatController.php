<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    //
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = $request->input('message');

        // Gọi OpenAI API
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo', // hoặc 'gpt-4o-mini' nếu bạn có quyền
            'messages' => [
                ['role' => 'system', 'content' => 'Bạn là trợ lý phụ huynh trường học. Hãy trả lời thân thiện và ngắn gọn.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);

        // Lấy nội dung trả lời
        $reply = $response['choices'][0]['message']['content'] ?? 'Xin lỗi, tôi chưa hiểu câu hỏi của bạn.';

        return response()->json([
            'reply' => $reply,
        ]);
    }


}
