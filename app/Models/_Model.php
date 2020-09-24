<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class _Model extends Model
{

    /**
     * In case we have values we can order by, custom and default
     *
     * @var array
     */
    protected $orderAble;

    /**
     * In case we have values we can filter by, no filter default
     *
     * @var array
     */
    protected $filterAble;

    /**
     * @var array $defaultValues of columns
     */
    protected $defaultValues =[];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * @var array ['column' => '','column' =>'^%','column' => '$%','column' => '%%'] search query parameter fields
     */
    protected $querySearch = [];

    /**
     * @return array
     */
    public function getQuerySearch(): array
    {
        return $this->querySearch;
    }

    /**
     * A quick method to get table names for migrations
     *
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @return bool - view only user content
     */
    public function getAuth(){
        return false;
    }

    /**
     * @return array
     */
    public function getOrderAble()
    {
        return $this->orderAble;
    }

    /**
     * @return array
     */
    public function getFilterAble()
    {
        return $this->filterAble;
    }

    /**
     * @param $column
     * @return string|null default value or null
     */
    public function getDefaultValue($column)
    {
        return isset($this->defaultValues[$column]) ? $this->defaultValues[$column] : null;
    }

    /**
     * Gets an array of the fields, related to the object.
     *
     * Fixed to work alongside the with() and without() eager loading methods
     *
     * @return array
     */
    public function getWith()
    {
        return array_keys($this->relationsToArray()) ?: $this->with; // This is still a hack for storing relationships by id
        // the system is unable to load them that way..
    }

    /**
     * Gets an array of the fields, related to the object without the eager loaded
     *
     * @return array
     */
    public function getWithRelation(): array
    {
        return array_keys($this->relationsToArray());
    }

    /**
     * TODO: DEPRECATED
     *
     * Adds a new relation to the relation list
     *
     * @param string $relation - the new relation to add
     */
    public function addToWith(string $relation)
    {
        $this->with[] = $relation;
    }

    /**
     * TODO: DEPRECATED
     *
     * @param string $key
     */
    public function removeWith(string $key)
    {
        unset($this->with[$key]);
    }

    /**
     * Creates or updates an entry in the database, using the provided array.
     * Initializes model fillables from the array elements.
     *
     * @param array $data
     */
    public function saveFromArray(array $data): void
    {
        foreach ($this->getFillable() as $fillable) {
            if (isset($data[$fillable])) {
                $this->$fillable = $data[$fillable];
            }
        }
        $this->save();
    }

    /**
     * Allows to store data for multiple languages in one attribute
     *
     * @param $attributeName
     * @param $value
     */
    protected function setAttributeTranslation($attributeName, $value)
    {
        $attribute = [];
        if ($this->{$attributeName}) {
            $attribute = $this->{$attributeName};
        }
        $attribute[app()->getLocale()] = $value;

        $this->attributes[$attributeName] = json_encode($attribute);
    }
}
