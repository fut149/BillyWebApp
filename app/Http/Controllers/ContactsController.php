<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Billy\BillyController;

class ContactsController extends _Controller
{
    protected $modelName = 'Contact';


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
            'phone' => 'sometimes|regex:/^[0-9]+$/',
        ];
        return $request->validate($rules);
    }

    public function store(Request $request)
    {
        $contact = parent::store($request);
        $billyCtrl = new BillyController();
        $contact->billy_contact_id = $billyCtrl->contactInBilly($contact);
        $contact->billy_created_at = date('Y-m-d H:i:s');
        $contact->billy_updated_at = date('Y-m-d H:i:s');
        $contact->save();
        return $contact;
    }

    public function update(Request $request, int $id)
    {
        $contact = parent::update($request, $id);
        $billyCtrl = new BillyController();
        $contact->billy_contact_id = $billyCtrl->contactInBilly($contact);
        $contact->billy_updated_at = date('Y-m-d H:i:s');
        $contact->save();
        return $contact;
    }

    public function destroy(int $id)
    {
        $this->model = $this->model->findOrFail($id);
        $billyCtrl = new BillyController();
        $billyCtrl->deleteContactInBilly($this->model);
        $this->model->forceDelete();
        return true;
    }

    public function import(){
        $billyCtrl = new BillyController();
        $contacts=$billyCtrl->getAllContacts();
        foreach ($contacts as $contact){
            $contact['user_id']=auth()->user()->id;
            $contact['billy_contact_id']=$contact['id'];
            $contact['billy_created_at']=date('Y-m-d H:i:s');
            $contact['billy_updated_at']=date('Y-m-d H:i:s');
            unset($contact['id']);
            $this->model=new $this->model();
            try {
                $this->model->saveFromArray($contact);
            }catch (\Exception $e){
                if(strstr($e->getMessage(),'Duplicate entry')){
                    continue;
                }
            }
        }
        return true;
    }
}
