<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 管理者
        User::updateOrCreate(  
            ['email' => 'a@a.a'],
            [
                'name' => '管理者',
                'password' => Hash::make('aaaaaaaa'),
                'role' => 'admin',
            ]
        );
        
        // テスト用従業員
        $users =[
            ['name' => '太 太郎', 'email' => 'aa@a.a', 'password' => 'aaaaaaaa'],
            ['name' => '太 郎太', 'email' => 'dd@d.d', 'password' => 'dddddddd'],
            ['name' => '郎 太太', 'email' => 'ee@e.e', 'password' => 'eeeeeeee'],
            ['name' => '太太 郎', 'email' => 'cc@c.c', 'password' => 'cccccccc'],
            ['name' => '太郎 太', 'email' => 'bb@b.b', 'password' => 'bbbbbbbb'],
            ['name' => '郎太 太', 'email' => 'ff@f.f', 'password' => 'ffffffff'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make($u['password']),
                    'role' => 'employee', 
                ]
            );
        }
    }
}
