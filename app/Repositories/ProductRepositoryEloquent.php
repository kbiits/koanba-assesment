<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Validators\ProductValidator;

/**
 * Class ProductRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductRepositoryEloquent extends BaseRepository implements ProductRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Product::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function addProduct($data): Product
    {
        return $this->getModel()->create($data);
    }

    /**
     * @param Product $product
     */
    public function editProduct($data, $product): Product
    {
        $product->fill($data);
        $product->save();
        $product->refresh();
        return $product;
    }

    public function getProductById($id)
    {
        return $this->getModel()->find($id);
    }
}
