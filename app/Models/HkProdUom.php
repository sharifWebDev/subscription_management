<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HkProdUom extends Model
{
    use HasFactory;

    protected $table = 'hk_prod_uoms';

    protected $fillable = ['code', 'name', 'is_active', 'sequence', 'created_by', 'updated_by'];

    // handleFileUpload

}
