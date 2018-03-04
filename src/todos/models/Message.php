<?php

namespace Todos\Models;


use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
        public function sender() {
            return $this->hasOne(User::class, "id", "sender_id");
        }
}