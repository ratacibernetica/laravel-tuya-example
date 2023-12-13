<?php

namespace App\Livewire;

use Livewire\Component;
use App\TuyaClient;
use Illuminate\Support\Arr;

# This is just a proof of concept for consuming the TUYA API

class Tuya extends Component
{

    public $devices = [];
    public $method = 'GET';
    public $uri = '/v2.0/cloud/thing/vdevo170235469281956';
    public $response = 'nothing to see here';
    public $endpoint = 'https://openapi.tuyaus.com';
    public $responseHtml = '';

    public function client()
    {
        $config = [
            'endpoint'=> $this->endpoint,
            'access_id'=>env('TUYA_ACCESS_ID'),
            'access_secret'=>env('TUYA_ACCESS_SECRET'),
        ];
        return TuyaClient::getClient($config);
    }

    public function request()
    {
        $client = $this->client();
        
        // TODO: accept dynamic params
        $params = [ 'params' => ['uid' => 179542435 ]];

        // TODO: capture method: GET, POST, PUT, DELETE. Maybe a dropdown in the UI
        $response = $client->send($this->method, $this->uri, $params); 
        $this->response = json_encode($response);
        $this->setResponseHtml($response);
    }

    public function setResponseHtml($response){
        $icon = 'https://images.tuyacn.com/' .Arr::get($response, 'result.icon');
        $id = Arr::get($response, 'result.id');
        $product_id = Arr::get($response, 'result.product_id');
        $local_key = Arr::get($response, 'result.local_key');
        $is_online = Arr::get($response, 'result.is_online');
        $name = Arr::get($response, 'result.name');
        $model = Arr::get($response, 'result.model');
        $category = Arr::get($response, 'result.category');
        $product_name = Arr::get($response, 'result.product_name');
        $this->responseHtml = <<<HTML
            <h1>Device Information</h1>

            <ul>
                <li><strong>ID:</strong> {$id}</li>
                <li><strong>Name:</strong> {$name}</li>
                <li><strong>Category:</strong> {$category}</li>
                <li><strong>Model:</strong> {$model}</li>
                <li><strong>Product ID:</strong> {$product_id}</li>
                <li><strong>Product Name:</strong> {$product_name}</li>
                <li><strong>Active Time:</strong> 1702354692</li>
                <li><strong>Create Time:</strong> 1702354692</li>
                <li><strong>Update Time:</strong> 1702354692</li>
                <li><strong>Time Zone:</strong> +08:00</li>
                <li><strong>Is Online:</strong> {$is_online}</li>
                <li><strong>Local Key:</strong> {$local_key}</li>
                <li><strong>Icon:</strong> <img src="{$icon}"
                        alt="Device Icon"></li>
            </ul>
        HTML;
    }

    public function render()
    {
        return <<<'HTML'
        <div>
        <h1>Request</h1>
        <form wire:submit="request">
            <label for="uri">URI</label>
            <input id="uri" type="text" wire:model="uri" >

            <button type="submit">request</button>
            
        </form>
        {!! $responseHtml !!}
        <h1>Response</h1>
        <code>{{ $response }}</code>

        </div>
        HTML;
    }
}
