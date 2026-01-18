<?php

namespace App\FileManager;

use Alexusmai\LaravelFileManager\Services\ACLService\ACLRepository;
use Illuminate\Support\Facades\Auth;

class UserACLRepository implements ACLRepository
{
    public function getUserID()
    {
        return Auth::id();
    }

    public function getRules(): array
    {
        $id = Auth::id();
        if (!$id) {
            return [];
        }

        $base = 'files/'.$id;

        return [
            ['disk' => 'public', 'path' => '/',       'access' => 1],
            ['disk' => 'public', 'path' => '',        'access' => 1],
            ['disk' => 'public', 'path' => 'files',   'access' => 1],
            ['disk' => 'public', 'path' => $base,     'access' => 2],
            ['disk' => 'public', 'path' => $base.'/*','access' => 2],
        ];
    }
}
