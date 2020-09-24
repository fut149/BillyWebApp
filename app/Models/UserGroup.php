<?php

namespace App;

use App\_Model as Model;

class UserGroup extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_groups';

    /**
     * Select values you can order by
     *
     * @var array
     */
    protected $orderAble = [
        'id',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'natureId',
        'priority',
        'building',
        'billy_gorup_id',
        'billy_created_at',
        'billy_updated_at',
    ];

    protected $casts = [
        'billy_created_at' => 'datetime',
        'billy_updated_at' => 'datetime',
    ];


    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
