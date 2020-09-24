<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersGroupsController extends _Controller
{
    protected $modelName = 'UserGroup';


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
        $userGroup= parent::store($request);
        $billyCtrl=new BillyController();
        $userGroup->billy_gorup_id=$billyCtrl->accountGroupInBilly($userGroup);
        $userGroup->billy_created_at=date('Y-m-d H:i:s');
        $userGroup->billy_updated_at=date('Y-m-d H:i:s');
        $userGroup->save();
        return $userGroup;
    }

    public function update(Request $request, int $id)
    {
        $userGroup=parent::update($request, $id);
        $billyCtrl=new BillyController();
        $userGroup->billy_gorup_id=$billyCtrl->accountGroupInBilly($userGroup);
        $userGroup->billy_updated_at=date('Y-m-d H:i:s');
        $userGroup->save();
        return $userGroup;
    }

    public function destroy(int $id)
    {
        $this->model = $this->model->findOrFail($id);
        $billyCtrl = new BillyController();
        $billyCtrl->deleteUserGroups($this->model);
        $this->model->forceDelete();
        return true;
    }
}
