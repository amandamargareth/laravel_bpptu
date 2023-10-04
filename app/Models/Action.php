<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Action extends Model {

   use HasFactory;

   protected $fillable = ['name', 'address', 'city', 'phone', 'variety', 'quantity', 'to', 'status'];
}

?>
