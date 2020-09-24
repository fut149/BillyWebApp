<?php

namespace App\Http\Controllers;


use App\Contact;
use App\Exceptions\HttpInvalidParamException;
use App\Models\_Model as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use PHPUnit\Exception;

class _Controller extends Controller
{
    protected $redirectTo = false;

    /**
     * The name of the model
     *
     * @var string
     */
    protected $modelName;

    /**
     * The full path to the model
     *
     * @var Model
     */
    protected $model;

    /**
     * The location of all models
     *
     * @var string
     */
    protected $modelsDir = "App\\";

    /**
     * How many items to show per page
     *
     * @var int
     */
    protected $itemsPerPage = 20;

    /**
     * Ignore second validation when using parent:: method
     *
     * @var bool
     */
    protected $isValidated = false;

    /**
     * Ignore additional relation setter
     *
     * @var bool
     */
    protected $withoutRelationInsert = false;

    /**
     * @var array
     */
    protected $defaultOrder = ['id', 'desc'];

    /**
     * @var bool
     */
    protected $ignoreOrder = false;

    /**
     * @var array
     */
    protected $orderAble;

    /**
     * @var array
     */
    protected $filterAble;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        if ( ! empty($this->modelName)) {
            $this->modelName = $this->modelsDir . $this->modelName;

            $this->model = new $this->modelName;

            $this->orderAble = $this->model->getOrderAble();

            $this->filterAble = $this->model->getFilterAble();

            $this->getItemsPerPageFromGet();
        }
    }

    /**
     * Searches the request for 'limit' parameter;
     * If it's available, overrides the default itemsPerPage property value
     *
     * @return void
     */
    public function getItemsPerPageFromGet(): void
    {
        if (isset(request()->limit) && ! empty(request()->limit)) {
            $itemsPerPage = (int)request()->limit;
            if ( ! empty($itemsPerPage) && $itemsPerPage > 0) {
                $this->itemsPerPage = $itemsPerPage;
            }
        }
    }

    /**
     * Returns a collection of items from the current model after a GET request
     *
     * @param Request $request
     * @param boolean $listingAll
     * @return mixed
     */
    public function index(Request $request, bool $listingAll = false)
    {
        $items = $this->model;
        if (!empty($request->get('query'))) {
            /** @var Model $searchIndex */
            $searchModel = new $this->modelName;
            $searchWord = mb_strtolower($request->get('query'));
            $searchColumns = $searchModel->getQuerySearch();
            if (\is_array($searchColumns)) {
                $items = $items->where(
                    function ($query) use ($searchColumns, $searchWord) {
                        foreach ($searchColumns as $column => $value) {
                            switch ($value) {
                                case '^%':
                                case '%^':
                                    $tmpSearch = $searchWord . '%';
                                    break;
                                case '$%':
                                case '%$':
                                    $tmpSearch = '%' . $searchWord;
                                    break;
                                default:
                                    $tmpSearch='%'.$searchWord.'%';
                            }
                            $query=$query->orWhereRaw('LOWER(`'.$column.'`) LIKE "'.$tmpSearch.'"');
                        }
                    });
            }
        }
        // Set default order
        if ( ! $request->filled('orderby')) {
            $items = $items->orderBy($this->defaultOrder[0] ?? 'id', $this->defaultOrder[1] ?? 'desc');
        }

        // Set custom order
        if ( ! empty($this->orderAble)) {
            // Add ordering to the index
            // nest in case the model doesn't want ordering we don't need all those checks..
            if ($request->filled('orderby') &&
                in_array($request->input('orderby'), $this->orderAble, true)) {
                $items = $items->orderBy(
                    $request->input('orderby'),
                    $request->sort === 'desc' ? 'desc' : 'asc'
                );
            }
        }

        $filters = $request->input('filters');
        if (is_array($filters) && !empty($filters)) {
            $approvedFilters = [];
            foreach ($filters as $filterKey => $filterValue) {
                if (is_numeric($filterValue) && in_array($filterKey, $this->filterAble, true)) {
                    $approvedFilters[] = [$filterKey, '=', $filterValue];
                }
            }
            $items = $items->where($approvedFilters);
        }
        if (!$listingAll) {
            if (method_exists($this->model, 'getAuth') && $this->model->getAuth()) {
                $items = $items->where('user_id', '=', auth()->user()->id);
            }
        }
        $items = $items->paginate($this->itemsPerPage);

        return $items;
    }

    /**
     * Returns a single item from the current model after a GET request
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show(int $id = 0)
    {
        try {
            $this->model = $this->model->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $this->response->errorNotFound();
        }

        return  $this->model;
    }

    /**
     * Inserts an item from the current model after a POST request
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        if ( ! $this->isValidated) {
            try {
                $k=$this->validateInput($request);
            }catch (\Exception $e){
                throw new HttpInvalidParamException($e->getMessage(),400);
            }
        }
        foreach ($this->model->getFillable() as $fillable) {
            if($fillable==='user_id'){
                $this->model->user_id=auth()->user()->id;
            }else {
                $this->model->$fillable = $request->filled($fillable)
                    ? $request->input($fillable)
                    : $this->model->getDefaultValue($fillable);
            }
        }

        if ( ! $this->withoutRelationInsert) {
            foreach ($this->model->getWith() as $relation) {
                $relationId               = "{$relation}_id";
                $relationRequest          = $request->input($relation);
                $this->model->$relationId = $relationRequest['data']['id'] ?? null;
            }
        }
        $this->model->save();

        $this->model->loadMissing($this->model->getWith());

        return $this->model;
    }

    /**
     * Updates an item from the current model after a PUT/PATCH request
     *
     * @param Request $request
     * @param int     $id
     *
     * @return mixed
     */
    public function update(Request $request, int $id)
    {
        if ( ! $this->isValidated) {
            try {
                $k=$this->validateInput($request);
            }catch (\Exception $e){
                throw new HttpInvalidParamException($e->getMessage(),400);
            }
        }
        try {
            $this->model = $this->model->findOrFail($id);
            if (method_exists($this->model, 'getAuth') && $this->model->getAuth() && $this->model->user_id!==auth()->user()->id) {
                throw new \Exception('Not found',404);
            }
        } catch (ModelNotFoundException $e) {
            throw new \Exception('Not found',404);
        }

        $fillables = $this->model->getFillable();

        foreach ($fillables as $fillable) {
            if(strpos($fillable,'billy_')===0){
                continue;
            }elseif($fillable==='user_id'){
                $this->model->user_id=auth()->user()->id;
            }elseif($request->$fillable === null ){
                $this->model->$fillable=$this->model->getDefaultValue($fillable);
            }elseif ($request->has($fillable)){
                $this->model->$fillable = $request->input($fillable);
            }
        }

        if ( ! $this->withoutRelationInsert) {
            foreach ($this->model->getWith() as $relation) {
                $relationId               = "{$relation}_id";
                $relationRequest          = $request->input($relation);
                $this->model->$relationId = $relationRequest['data']['id'] ?? null;
            }
        }
        $this->model->save();

        // Refresh model data to populate all required values
        $this->model->refresh();

        return $this->model;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function destroy(int $id)
    {
        $this->model = $this->model->findOrFail($id);

        $this->model->forceDelete();

        return true;
    }

    /**
     * @param Request $request
     * @param $validator
     * @throws ValidationException
     */
    protected function throwValidationException(Request $request, $validator):void
    {
        throw new ValidationException($validator->getMessageBag()->toArray());
    }

    /**
     * @param Request $request
     * @param $validator
     * @throws ValidationException
     */
    protected function validateByValidator(Request $request, $validator):void
    {
        if ($validator->fails()) {
            $this->throwValidationException($request, $validator);
        }
    }

    /**
     * Validates the input when inserting/updating.
     * All models should override this method
     *
     * @param Request $request
     * @param int     $id
     */
    protected function validateInput(Request $request, int $id = null)
    {

    }
}
