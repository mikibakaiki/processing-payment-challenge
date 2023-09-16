<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Profile extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email'];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}