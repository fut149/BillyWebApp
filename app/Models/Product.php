<?php

namespace App;

use App\_Model as Model;

class Product extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * Select values you can order by
     *
     * @var array
     */
    protected $orderAble = [
        'id',
        'name',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'inventoryAccountId',
        'suppliersProductNo',
        'isArchived',
        'isInInventory',
        'imageId',
        'imageUrl',
        'billy_product_id',
        'user_id',
        'billy_created_at',
        'billy_updated_at',
    ];

    protected $casts = [
        'billy_created_at' => 'datetime',
        'billy_updated_at' => 'datetime',
    ];

    /**
     * @var array $defaultValues of columns
     */
    protected $defaultValues =[
        'isArchived' => false,
        'isInInventory' => false,
    ];

    /**
     * @var array ['column' => '','column' =>'^%','column' => '$%','column' => '%%'] search query parameter fields
     */
    protected $querySearch = [
        'name' => '%%',
        'description' => '%%',
        'suppliersProductNo' => '^%',
    ];
    /**
     * @return bool - view only user content
     */
    public function getAuth(){
        return true;
    }
}
