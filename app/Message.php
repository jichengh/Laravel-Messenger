<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';

    protected $guarded = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'from_user_id', 'to_user_id', 'message', 'status'
    ];

    public function fromContact()
    {
        return $this->hasOne('App\User', 'id', 'from_user_id');
    }
}
