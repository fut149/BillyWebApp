<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Billy\BillyController;

class ProductsController extends _Controller
{
    protected $modelName = 'Product';


    /**
     * @param Request $request
     * @param int|null $id
     *
     * @return mixed
     */
    public function validateInput(Request $request, int $id = null)
    {
        $rules = [
            'name' => 'required|min:5|max:255',
        ];
        return $request->validate($rules);
    }

    public function store(Request $request)
    {
        $product= parent::store($request);
        $billyCtrl=new BillyController();
        $product->billy_product_id=$billyCtrl->productInBilly($product);
        $product->billy_created_at=date('Y-m-d H:i:s');
        $product->billy_updated_at=date('Y-m-d H:i:s');
        $product->save();
        return $product;
    }

    public function update(Request $request, int $id)
    {
        $product=parent::update($request, $id);
        $billyCtrl=new BillyController();
        $product->billy_product_id=$billyCtrl->productInBilly($product);
        $product->billy_updated_at=date('Y-m-d H:i:s');
        $product->save();
        return $product;
    }

    public function destroy(int $id)
    {
        $this->model = $this->model->findOrFail($id);
        $billyCtrl = new BillyController();
        $billyCtrl->deleteProductsInBilly($this->model);
        $this->model->forceDelete();
        return true;
    }

}
