<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\AuthController;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $authController = new AuthController(new Auth);
        $user = $authController->user();

        $appends = [
            'category',
            'images',
            'inventories',
            'subProducts.images',
            'subProducts.category',
            'subProducts.inventories'
        ];

        $product = new Product;

        $products = Product::with($appends)
            ->whereNull('master_product_id')
            ->whereIsActive(true);

        switch ($request->type) {
            case 'newest': {
                $products = $products->orderBy('created_at', 'desc');
                break;
            }
            case 'best-seller': {
                $products = $products->orderBy('buy_count', 'desc');
            }
            case 'most-like': {
                $products = $products->orderBy('like_count', 'desc');
            }
        }

        $products = $products->paginate(12);
        $products->appends($request->except('page'))->links();

        $products->each(function ($product) use ($user) {
            $product->append('size', 'color');
            if ($user) {
                $product->setUserId($user->id);
                $product->append('liked');
            }
            $product->subProducts->each(function ($subProduct) use ($user) {
                $subProduct->append('size', 'color');
                if ($user) {
                    $subProduct->setUserId($user->id);
                    $subProduct->append('liked');
                }
            });
        });

        return response()->json($products, 200);
    }

    public function show($id)
    {
        $authController = new AuthController(new Auth);
        $user = $authController->user();

        $product = Product::with(
                'category',
                'images',
                'inventories',
                'subProducts.images',
                'subProducts.category',
                'subProducts.inventories'
            )
            ->whereId($id)
            ->whereNull('master_product_id')
            ->whereIsActive(true)
            ->first();

        if (!is_null($product)) {
            $product = $product->append('size', 'color');

            if ($user) {
                $product->setUserId($user->id);
                $product->append('liked');
            }
    
            $product->subProducts->each(function ($subProduct) use ($user) {
                $subProduct->append('size', 'color');
                if ($user) {
                    $subProduct->setUserId($user->id);
                    $subProduct->append('liked');
                }
            });
        }

        return response()->json($product, 200);
    }

    public function getByCategory($category, Request $request)
    {
        $authController = new AuthController(new Auth);
        $user = $authController->user();

        $appends = [
            'category',
            'images',
            'inventories',
            'subProducts.images',
            'subProducts.category',
            'subProducts.inventories'
        ];

        $product = new Product;

        $products = Product::with($appends)
            ->whereNull('master_product_id')
            ->whereHas('category', function ($query) use ($category) {
                $query->whereSlug($category);
            })
            ->whereIsActive(true);

        if ($request->has('type')) {
            $type = $request->type;
        } else {
            $type = 'newest';
        }

        switch ($type) {
            case 'newest': {
                $products = $products->orderBy('created_at', 'desc');
                break;
            }
            case 'best-seller': {
                $products = $products->orderBy('buy_count', 'desc');
                break;
            }
            case 'most-like': {
                $products = $products->orderBy('like_count', 'desc');
                break;
            }
            default: {
                $products = $products->orderBy('created_at', 'desc');
                break;
            }
        }

        $products = $products->paginate(12);
        $products->appends($request->except('page'))->links();

        $products->each(function ($product) use ($user) {
            $product->append('size', 'color');
            if ($user) {
                $product->setUserId($user->id);
                $product->append('liked');
            }
            $product->subProducts->each(function ($subProduct) use ($user) {
                $subProduct->append('size', 'color');
                if ($user) {
                    $subProduct->setUserId($user->id);
                    $subProduct->append('liked');
                }
            });
        });

        return response()->json($products, 200);
    }
}
