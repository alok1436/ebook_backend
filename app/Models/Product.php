<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title','writer','price','tags','image'];
    /**
     * @OA\Schema(
     * schema="products",
     * description="products",
     *
     * @OA\Property(property="id", type="integer", readOnly="true", example="1"),
     * @OA\Property(property="name", type="string" ),
     * @OA\Property(property="icon", type="string"),
     * ),
     */
}
