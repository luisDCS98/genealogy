<?php

namespace App\Tables\Builders;

use Illuminate\Database\Eloquent\Builder;
use LaravelEnso\Tables\Contracts\Table;
use App\Models\User;

class UserTable implements Table
{
    protected const TemplatePath = __DIR__.'/../Templates/users.json';

    protected $query;

    public function query(): Builder
    {
        return User::with('avatar:id,user_id')->selectRaw('
            users.id, user_groups.name as "group", people.name, people.appellative,
            people.phone, users.email, roles.name as role, users.is_active,
            users.created_at, users.person_id
        ')->join('people', 'users.person_id', '=', 'people.id')
            ->join('user_groups', 'users.group_id', '=', 'user_groups.id')
            ->join('roles', 'users.role_id', '=', 'roles.id');
    }

    public function templatePath(): string
    {
        return static::TemplatePath;
    }
}
