<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\UserGroup;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\HttpInvalidParamException;
use App\Http\Controllers\Billy\BillyController;



class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = false;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param array $data
     * @return bool
     * @throws HttpInvalidParamException
     */
    protected function validator(array $data)
    {
        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_groups_id' => ['sometimes','numeric'],
        ]);
       try{
          $validator->validate();
       }catch (\Exception $e){
           if($validator->fails()) {
               throw new HttpInvalidParamException(json_encode($validator->errors()),402);
           }else{
               throw $e;
           }
       }
        return  true;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/register",
     * summary="Register user",
     * description="Register by email, password",
     * operationId="authRegister",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password","name"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *       @OA\Property(property="name", type="string",format="text", example="gosho"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="The email has already been taken.")
     *        )
     *     )
     * )
     */
    public function register(Request $request)
    {

        $this->validator($request->all());

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    protected function registered(Request $request, $user)
    {
        $billyCtrl=new BillyController();
        $user->user_groups_id=(int)$request->input('user_groups_id')>0 ? $request->input('user_groups_id'): 1;
        $billy_gorup_id=UserGroup::findOrFail($user->user_groups_id)->billy_gorup_id;
        $user->billy_account_id=$billyCtrl->createUserInBilly($user,$billy_gorup_id);
        $user->billy_created_at=date('Y-m-d H:i:s');
        $user->billy_updated_at=date('Y-m-d H:i:s');
        $user->save();
        return $user;
    }
}
