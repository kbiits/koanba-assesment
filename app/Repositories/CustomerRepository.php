<?php

namespace App\Repositories;

use App\Models\Customer;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface CustomerRepository.
 *
 * @package namespace App\Repositories;
 */
interface CustomerRepository extends RepositoryInterface
{
    public function addCustomer($data): Customer;
    public function editCustomer($data, $customer): Customer;
    public function getCustomerById($id);
}
