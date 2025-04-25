<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $table = "Password_reset_tokens";
    protected $fillable = [
        'email',
        'token',
        'created_at'
    ];


}
