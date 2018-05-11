<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class ProductAttributeValue extends Model
{
    use HasCompositePrimaryKey;

    protected $primaryKey = ['product_id', 'branch_id'];
    protected $fillable = ['product_id', 'attribute_value_id'];
    public $incrementing = false;
}
