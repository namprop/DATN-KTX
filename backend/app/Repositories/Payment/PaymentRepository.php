<?php


namespace App\Repositories\Payment;

use App\Repositories\BaseRepository;

use App\Models\Payment;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function getModel()
    {
        return Payment::class;
    }
}
