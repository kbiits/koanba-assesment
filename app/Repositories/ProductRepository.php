<?php

namespace App\Repositories;

use App\Models\Product;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ProductRepository.
 *
 * @package namespace App\Repositories;
 */
interface ProductRepository extends RepositoryInterface
{
    //

    public function addProduct($data): Product;
    public function editProduct($data, $product): Product;
    public function getProductById($id);
}
