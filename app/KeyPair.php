<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KeyPair extends Model
{
    protected $table = 'keypairs';
    
    protected $fillable = ['user_id', 'private_key', 'public_key'];
}
