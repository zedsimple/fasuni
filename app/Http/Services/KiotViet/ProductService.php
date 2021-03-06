<?php

namespace App\Http\Services\KiotViet;

use App\Http\HttpClient\HttpClient;
use App\Exceptions\HttpClient\RequestException;

class ProductService
{
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getAll($page = 1)
    {
        $perPage = 100;
        $current = ($page - 1) * $perPage;

        try {
            $response = $this->httpClient->get('products?pageSize=' . $perPage . '&includeRemoveIds=true&includeInventory=true&currentItem=' . $current);

            $response = $response->getBody()->getContents();
            $response = json_decode($response);

            return $response;
        } catch (RequestException $e) {
            \Log::error($e->getFile() . ' ' . $e->getLine() . ' error: Cannot get products: ' . $e->getMessage());
            if (is_object(json_decode($e->getMessage()))) {
                response()->json(['error' => 'Cannot get products: ' . json_decode($e->getMessage())->ResponseStatus->Message], 500)->send();
            } else {
                response()->json(['error' => 'Cannot get products: ' . $e->getMessage()], 500)->send();
            }

            die;
        }
    }

    public function getOne($id)
    {
        try {
            $response = $this->httpClient->get('products/' . $id);

            $response = $response->getBody()->getContents();
            $response = json_decode($response);

            return $response;
        } catch (RequestException $e) {
            \Log::error($e->getFile() . ' ' . $e->getLine() . ' error: Cannot get product: ' . $e->getMessage());
            $message = json_decode($e->getMessage());
            if (is_object($message)) {
                if (isset($message->ResponseStatus)) {
                    // \Log::error('Cannot get product: ' . json_decode($e->getMessage())->ResponseStatus->Message);
                    response()->json(['error' => 'Cannot get product: ' . json_decode($e->getMessage())->ResponseStatus->Message], 500)->send();
                } else if (isset($message->responseStatus)) {
                    throw $e;
                }
            } else {
                response()->json(['error' => 'Cannot get product: ' . $e->getMessage()], 500)->send();
            }

            die;
        }
    }
}
