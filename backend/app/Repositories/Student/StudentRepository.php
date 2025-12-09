<?php


namespace App\Repositories\Student;

use App\Models\Room;
use App\Repositories\BaseRepository;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class StudentRepository extends BaseRepository implements StudentRepositoryInterface
{
    public function getModel()
    {
        return Student::class;
    }

    public function getAllStudentsWithUser($columns, $keyword, $perPage)
    {
        return Student::with(['user', 'room'])
            ->where(function ($q) use ($columns, $keyword) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            })
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $keyword]);
    }

    public function findStudentWithUser($id)
    {
        return Student::with('user')->findOrFail($id);
    }



  public function createStudent($data, $request)
{
    // Lấy phòng nếu room_code tồn tại trong $data, còn không thì null
    $roomCode = $data['room_code'] ?? null;
    $room = $roomCode ? Room::where('room_code', $roomCode)->first() : null;

    // Kiểm tra phòng đầy nếu có
    if ($room) {
        $studentCount = Student::where('room_id', $room->id)->count();
        if ($studentCount >= $room->capacity) {
            throw new \Exception("Phòng {$room->room_code} đã đầy, không thể thêm sinh viên mới.");
        }
    }

    // Tạo user
    $user = User::create([
        'name'     => $data['name'],
        'email'    => $data['email'],
        'password' => bcrypt($data['password']),
        'role'     => 'Student',
    ]);

    // Xử lý avatar
    $avatarPath = null;
    if ($request->hasFile('avatar')) {
        $avatarPath = $request->file('avatar')->store('uploads/avatars', 'public');
    }

    // Tạo student
    $student = Student::create([
        'user_id'       => $user->id,
        'student_code'  => $data['student_code'],
        'full_name'     => $data['full_name'],
        'gender'        => $data['gender'] ?? null,
        'phone'         => $data['phone'] ?? null,
        'avatar'        => $avatarPath,
        'date_of_birth' => $data['date_of_birth'] ?? null,
        'status'        => $data['status'] ?? 'Active',
        'room_id'       => $room?->id, // Nếu room null thì tự động null
    ]);

    return $student;
}


    public function updateStudent($id, $data, $request)
    {
        $student = Student::find($id);

        if (!$student) {
            return null; // hoặc throw Exception
        }

        $room = Room::where('room_code', $data['room_code'])->first();

        if ($room) {
            // Đếm số sinh viên trong phòng
            $studentCount = Student::where('room_id', $room->id)->count();

            if ($studentCount >= $room->capacity) {
                // Nếu đủ rồi thì throw exception (controller sẽ bắt)
                throw new \Exception("Phòng {$room->room_code} đã đầy, không thể thêm sinh viên mới.");
            }
        }

        // Update user
        $user = $student->user;
        if ($user) {
            $user->name  = $data['name'] ?? $user->name;
            $user->email = $data['email'] ?? $user->email;



            $user->save();
        }

        // Xử lý avatar
        $avatarPath = $student->avatar;
        if ($request->hasFile('avatar')) {
            // Xoá avatar cũ nếu có
            if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            // Upload avatar mới
            $avatarPath = $request->file('avatar')->store('uploads/avatars', 'public');
        }

        // Update student
        $student->update([
            'student_code'  => $data['student_code'] ?? $student->student_code,
            'full_name'     => $data['full_name'] ?? $student->full_name,
            'gender'        => $data['gender'] ?? $student->gender,
            'phone'         => $data['phone'] ?? $student->phone,
            'avatar'        => $avatarPath,
            'date_of_birth' => $data['date_of_birth'] ?? $student->date_of_birth,
            'status'        => $data['status'] ?? $student->status,
            'room_id'       => $room ? $room->id : $student->room_id,
        ]);

        return $student;
    }
}
