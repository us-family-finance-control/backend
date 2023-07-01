<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Dependent;
use App\Models\Manager;
use App\Models\User;
use Tests\TestCase;

# docker-compose exec us php artisan test --filter=AuthenticationControllerTest
class AuthenticationControllerTest extends TestCase
{
    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_register_with_no_parameters
    public function test_register_with_no_parameters(): void
    {
        $this->post(
            'api/register',
            []
        )->assertStatus(403)
            ->assertJsonStructure([
                'code',
                'message',
                'errors'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_register_with_email_exists
    public function test_register_with_email_exists(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/register',
            [
                'type' => 'manager',
                'name' => 'Kabutiassauro Rex',
                'email' => $manager->user->email,
                'phone' => '9999999999999',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]
        )->assertStatus(403)
            ->assertJsonStructure([
                'code',
                'message',
                'errors'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_register_admin
    public function test_register_admin(): void
    {
        $this->post(
            'api/register',
            [
                'type' => 'admin',
                'name' => 'Batatassauro Rex',
                'email' => 'batatassauro@rex.com',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]
        )->assertStatus(201)
            ->assertJsonStructure([
                'code',
                'message',
                'data'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_register_manager
    public function test_register_manager(): void
    {
        $this->post(
            'api/register',
            [
                'type' => 'manager',
                'name' => 'Kabutiassauro Rex',
                'email' => 'kabutiassauro@rex.com',
                'phone' => '9999999999999',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]
        )->assertStatus(201)
            ->assertJsonStructure([
                'code',
                'message',
                'data'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_register_dependent
    public function test_register_dependent(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/register',
            [
                'type' => 'dependent',
                'manager_id' => $manager->id,
                'name' => 'Amendoassauro Rex',
                'username' => 'amendoassauro',
                'kinship' => 'wife',
                'phone' => '9999999999999',
                'password' => '123456',
                'password_confirmation' => '123456'
            ]
        )->assertStatus(201)
            ->assertJsonStructure([
                'code',
                'message',
                'data'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_admin
    public function test_login_admin(): void
    {
        $admin = Admin::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/login',
            [
                'emailOrUsername' => $admin->user->email,
                'password' => 'password',
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_manager
    public function test_login_manager(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/login',
            [
                'emailOrUsername' => $manager->user->email,
                'password' => 'password',
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_dependent
    public function test_login_dependent(): void
    {
        $dependent = Dependent::factory()
            ->for(Manager::factory())
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/login',
            [
                'emailOrUsername' => $dependent->user->username,
                'password' => 'password',
            ]
        )->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_manager_without_params
    public function test_login_manager_without_params(): void
    {
        $this->post(
            'api/login',
            []
        )->assertStatus(403)
            ->assertJsonStructure([
                'code',
                'message',
                'errors'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_manager_not_found
    public function test_login_manager_not_found(): void
    {
        $this->post(
            'api/login',
            [
                'emailOrUsername' => 'managerssauro@rex.com',
                'password' => 'password',
            ]
        )->assertStatus(404)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_login_manager_password_incorrect
    public function test_login_manager_password_incorrect(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;


        $this->post(
            'api/login',
            [
                'emailOrUsername' => $manager->user->email,
                'password' => '123456',
            ]
        )->assertStatus(404)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }
}
