<?php

namespace App;

use App\_Model as Model;
use App\Models\User;

class Contact extends Model
{

    /**
 * The table associated with the model.
 *
 * @var string
 */
    protected $table = 'contacts';

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
        'user_id',
        'type',
        'name',
        'countryId',
        'street',
        'cityText',
        'stateText',
        'zipcodeText',
        'phone',
        'billy_contact_id',
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
        'type' => 'company',
        'countryId' => 'BG',
    ];

    /**
     * @var array ['column' => '','column' =>'^%','column' => '$%','column' => '%%'] search query parameter fields
     */
    protected $querySearch = [
        'name' => '%%',
        'phone' => ''
    ];
    /**
     * @return bool - view only user content
     */
    public function getAuth(){
        return false;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
