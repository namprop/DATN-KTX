<?php

namespace App\Service\Payment;

use App\Repositories\Payment\PaymentRepositoryInterface;
use App\Service\BaseService;

class PaymentService extends BaseService implements PaymentServiceInterface
{
    public $repository;

    public function __construct(PaymentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
}
