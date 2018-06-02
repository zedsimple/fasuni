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
            ->where(function ($query) use ($category) {
                $query->whereHas('category', function ($query) use ($category) {
                    $query->whereSlug($category);
                })
                ->orWhereHas('category.parent', function ($query) use ($category) {
                    $query->whereSlug($category);
                })
                ->orWhereHas('category.parent.parent', function ($query) use ($category) {
                    $query->whereSlug($category);
                });
            })
            ->whereIsActive(true);

        if ($request->has('colors')) {
            $colors = explode(',', $request->colors);

            $products = $products->where(function ($query) use ($colors) {
                $query->whereHas('productAttributeValues', function ($query) use ($colors) {
                    $query->whereIn('attribute_value_id', $colors);
                })
                ->orWhereHas('subProducts', function ($query) use ($colors) {
                    $query->whereHas('productAttributeValues', function ($query) use ($colors) {
                        $query->whereIn('attribute_value_id', $colors);
                    });
                });
            });
        }

        if ($request->has('sizes')) {
            $sizes = explode(',', $request->sizes);

            $products = $products->where(function ($query) use ($sizes) {
                $query->whereHas('productAttributeValues', function ($query) use ($sizes) {
                    $query->whereIn('attribute_value_id', $sizes);
                })
                ->orWhereHas('subProducts', function ($query) use ($sizes) {
                    $query->whereHas('productAttributeValues', function ($query) use ($sizes) {
                        $query->whereIn('attribute_value_id', $sizes);
                    });
                });
            });
        }

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

    public function getRelevant($id, Request $request)
    {
        $products = [];

        if ($request->has('category')) {
            $category = $request->category;

            $products = Product::with('images')
            ->whereNull('master_product_id')
            ->where(function ($query) use ($category) {
                $query->whereHas('category', function ($query) use ($category) {
                    $query->whereSlug($category);
                })
                ->orWhereHas('category.parent', function ($query) use ($category) {
                    $query->whereSlug($category);
                })
                ->orWhereHas('category.parent.parent', function ($query) use ($category) {
                    $query->whereSlug($category);
                });
            })
            ->where('id', '!=', $id)
            ->whereIsActive(true)
            ->paginate(16);

            $products->appends($request->except('page'))->links();

            $products->each(function ($product) {
                $product->append('size', 'color');
            });
        }

        return response()->json($products, 200);
    }
}
