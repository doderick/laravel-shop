<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = app(Faker\Generator::class);

        // 头像数据
        $avatars = [
            'http://shop1.test/images/avatar_1.png',
            'http://shop1.test/images/avatar_2.png',
            'http://shop1.test/images/avatar_3.png',
            'http://shop1.test/images/avatar_4.png',
            'http://shop1.test/images/avatar_5.png',
        ];

        // 生成数据集合
        $user = factory(User::class)
                        ->times(100)
                        ->make()
                        ->each(function($user, $index)
                            use($faker, $avatars)
        {
            // 获得随机头像
            $user->avatar = $faker->randomElement($avatars);
        });

        // 让隐藏字段可见，并将数据集合转换为数组
        $user_array = $user->makeVisible(['password', 'remember_token'])->toArray();

        // 插入到数据库中
        User::insert($user_array);

        // 设置 id=1 id=2 的两个用户
        $user = User::find(1);
        $user->name   = 'doderick';
        $user->email  = 'doderick@outlook.com';
        $user->avatar = config('app.url') . '/images/The_Red_Devils.png';
        $user->save();

        $user = User::find(2);
        $user->name   = 'JJ-711';
        $user->email  = 'jiajun5427@163.com';
        $user->avatar = config('app.url') . '/images/doderick-github.png';
        $user->save();
    }
}
