<?php

namespace App\Http\Controllers\Billy;

use App\Exceptions\BillyException;
use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function createUserInBilly(User $user){
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
        return $response['accounts'][0]['id'] ?? $response['accounts'][0]['id'];
    }

    private function createProduct(array $data)
    {
        $product = [
            'product' => [
                "organizationId" => $this->organisationId,
                "name" => "Book 4",
                "description" => "desc",
                "accountId" => "4qAjMzZRRoO7sOAjzkorjw",//One my account
                "inventoryAccountId" => null,
                "suppliersProductNo" => "",
//            "salesTaxRulesetId" => "K5A89XDhQJeiyC9HtTX6Hw",
//            "isArchived" => false,
//            "isInInventory" => false,
//            "imageId" => null,
//            "imageUrl" => null,
            ]
        ];
        return $this->request('post', '/products', $product)->json();
    }
    private function createContact(array $data)
    {
        $contact = [
            'contact' => [
                        "type" => "company",
                        "organizationId" => $this->organisationId,
                        "name" => "Goshko",
                        "countryId" => "BG",
                        "street" => "Nikola Novi",
//                        "cityId" => null,
                        "cityText" => "Sofia",
//                        "stateId" => null,
                        "stateText" => "",
//                        "zipcodeId" => null,
                        "zipcodeText" => "1000",
                        "phone" => "",
//                        "fax" => "",
//                        "currencyId" => null,
//                        "registrationNo" => "",
//                        "ean" => "",
//                        "localeId" => null,
//                        "isCustomer" => true,
//                        "isSupplier" => false,
//                        "paymentTermsMode" => null,
//                        "paymentTermsDays" => null,
//                        "accessCode" => "H6FRoslEBX7iOoVx",
//                        "emailAttachmentDeliveryMode" => null,
//                        "isArchived" => false,
//                        "isSalesTaxExempt" => false,
//                        "defaultExpenseProductDescription" => null,
//                        "defaultExpenseAccountId" => null,
//                        "defaultTaxRateId" => null,
            ]
        ];
        return $this->request('post', '/contacts', $contact)->json();
    }

    public function index()
    {
        dd($this->createContact([]));
        $response = $this->getResurse('/contacts');
        dd($response);
        dd($this->createProduct([]));
        $response = $this->getResurse('/accountGroups');
        dd($response);
        dd($this->createAccount([]));
        $response = $this->getResurse('/accounts');
        dd($response);
    }

}
