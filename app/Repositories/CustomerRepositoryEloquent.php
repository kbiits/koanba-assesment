<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CustomerRepository;
use App\Models\Customer;
use App\Validators\CustomerValidator;

/**
 * Class CustomerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CustomerRepositoryEloquent extends BaseRepository implements CustomerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Customer::class;
    }

    public function addCustomer($data): Customer
    {
        return $this->getModel()->create($data);
    }

    /**
     * @param Customer $customer
     */
    public function editCustomer($data, $customer): Customer
    {
        $customer->fill($data);
        $customer->save();
        $customer->refresh();
        return $customer;
    }

    public function getCustomerById($id)
    {
        return $this->getModel()->find($id);
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
