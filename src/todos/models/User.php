<?php

namespace Todos\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = [
        'password',
        'apikey'
    ];
    
    public function authenticate($apikey)
    {
        $user = User::where('apikey', '=', $apikey)->first();
        if(!is_null($user)){
                return $user->id;
        }
        return false;
    }
}