<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    
    protected $table= 'categories';

    protected $fillable=[ 'name'];


  public function posts(){
        return $this->hasMany('App\Post');
    }
}
