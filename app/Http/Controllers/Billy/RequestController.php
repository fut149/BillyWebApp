<?php

namespace App\Http\Controllers\Billy;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class RequestController extends Controller
{
    private $token;
    private $apiUrl;

    function __construct()
    {
        $this->token=config('billy.access_token');
        $this->apiUrl=config('billy.api_url');
    }

    public function index()
    {
        $response = Http::withToken($this->token)->withHeaders(
            [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        )->post('https://api.b illysbilling.com/v2', );
    }
}
