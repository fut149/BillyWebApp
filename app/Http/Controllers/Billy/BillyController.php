<?php

namespace App\Http\Controllers\Billy;

use App\Contact;
use App\Exceptions\BillyException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Product;
use Illuminate\Support\Facades\Http;

class BillyController extends Controller
{
    private $token;
    private $apiUrl;
    private $organisationId;

    function __construct()
    {
        $this->token = config('billy.access_token');
        $this->apiUrl = config('billy.api_url');
        try {
            $this->organisationId = $this->getOrganizationId();
        } catch (\Exception $e) {
            $this->organisationId = null;
        }
    }

    private function getOrganizationId()
    {
        $organization = $this->getOrganization();
        return $organization['id'];
    }

    private function request(string $method, string $url, array $data = [])
    {
        $method = strtolower(trim($method));
        $request = Http::withHeaders(
            [
                'X-Access-Token' => $this->token,
                'Content-Type' => 'application/json',
            ]
        );
        $url = $this->apiUrl . $url;
        switch ($method) {
            case 'post':
            case 'put':
            case 'delete':
                $request = $request->$method($url, $data);
                break;
            case 'get':
                $request = $request->get($url);
                break;
            default:
                throw new \HttpException('Undefined http method!', 500);
        }
        return $request;
    }

    private function getOrganization()
    {
        return $this->getResurse('/organization');
    }

    private function getResurse($url)
    {
        $organization = $this->request("GET", $url);
        if (preg_match('{(\/|)([^\/]+).*}', $url, $matches) && isset($matches[2])) {
            $resurse = $matches[2];
        } else {
            $resurse = $url;
        }
        if (!$organization || !($data = $organization->json()) || !isset($data[$resurse])) {
            throw new BillyException('Don`t have ' . $resurse, 400);
        }
        return $data[$resurse];
    }

    private function testGet($url)
    {
        return $this->request("GET", $url);
    }

    private function createAccountGroup(array $data)
    {
        $accountGroup = [
            'accountGroup' =>
                [
                    "organizationId" => $this->organisationId,
                    "name" => "test",
                    "type" => "group",
                    "natureId" => "expense",
                    "sumFrom" => null,
                    "style" => null,
                    "priority" => 0
                ]
        ];
        $response = $this->request('post', '/accountGroups', $accountGroup)->json();
        return $response['accountGroups'][0]['id'] ?? $response['accountGroups'][0]['id'];
    }

    public function createUserInBilly(User $user)
    {
        return $this->createAccount($user->getAttributes());
    }

    private function createAccount(array $data): string
    {
        $groupId = $this->createAccountGroup([]);
        $account = [
            'account' => [
                "organizationId" => $this->organisationId,
                "name" => $data['name'],
                "description" => $data['email'],
                "groupId" => $groupId,
                "natureId" => "revenue",
//                "accountGroup" => $group,
//                "systemRole" => null,
//                "isPaymentEnabled" => false,
//                "isBankAccount" => false,
//                "isArchived" => false,
//                "bankName" => null,
//                "bankRoutingNo" => null,
//                "bankAccountNo" => null,
//                "bankSwift" => null,
//                "bankIban" => null
            ]
        ];
        $response = $this->request('post', '/accounts', $account)->json();
//        dd($response,$account);
        return $response['accounts'][0]['id'] ?? $response['accounts'][0]['id'];
    }

    private function getProductRequest(array $data): array
    {
        return [
            'product' => [
                "organizationId" => $this->organisationId,
                "name" => $data['name'],
                "description" => $data['description'],
                "accountId" => auth()->user()->billy_account_id,
                "inventoryAccountId" => $data['inventoryAccountId'],
                "suppliersProductNo" => $data['suppliersProductNo'],
                "isArchived" => (int)$data['isArchived'],
                "isInInventory" => (int)$data['isInInventory'],
                "imageId" =>  $data['imageId'],
            ]
        ];
    }

    private function storeProduct(array $data)
    {
        $method = 'post';
        $url = '/products';
        if (isset($data['billy_product_id']) && !empty($data['billy_product_id'])) {
            $url .= '/' . $data['billy_product_id'];
            $method = 'put';
        }
        $response = $this->request(
            $method,
            $url,
            $this->getProductRequest($data)
        )->json();
        return isset($response['products'][0]['id']) ? $response['products'][0]['id'] : null;
    }

    public function productInBilly(Product $product)
    {
        return $this->storeProduct($product->getAttributes());
    }

    public function contactInBilly(Contact $contact)
    {
        return $this->storeContact($contact->getAttributes());
    }

    private function getContactRequest(array $data): array
    {
        return [
            'contact' => [
                "type" => $data['type'],
                "organizationId" => $this->organisationId,
                "name" => $data['name'],
                "countryId" => strtoupper(trim($data['countryId'])),
                "street" => $data['street'],
                "cityText" => $data['cityText'],
                "stateText" => $data['stateText'],
                "zipcodeText" => $data['zipcodeText'],
                "phone" => $data['phone'],
            ]
        ];
    }

    private function storeContact(array $data)
    {
        $method = 'post';
        $url = '/contacts';
        if (isset($data['billy_contact_id']) && !empty($data['billy_contact_id'])) {
            $url .= '/' . $data['billy_contact_id'];
            $method = 'put';
        }
        $response = $this->request(
            $method,
            $url,
            $this->getContactRequest($data)
        )->json();
        return isset($response['contacts'][0]['id']) ? $response['contacts'][0]['id'] : null;
    }

    public function index()
    {
        $response = $this->getResurse('/accounts');
        foreach ($response as $acc){
            if($acc['id']==='juBsyNOAQMeEp5B2fEEFXQ'){
                dd($acc);
            }
        }
        dd($response);
        dd($this->createContact([]));
        $response = $this->getResurse('/contacts');
        dd($response);
        dd($this->createProduct([]));
        $response = $this->getResurse('/accountGroups');
        dd($response);
        dd($this->createAccount([]));
        dd($response);
    }

}
