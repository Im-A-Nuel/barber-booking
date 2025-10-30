<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    protected $table = 'barbers';

    protected $fillable = ['name','specialty','experience_years','phone','rating_avg','is_active'];
}
