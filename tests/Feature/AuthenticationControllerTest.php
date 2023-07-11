<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Dependent;
use App\Models\Manager;
use App\Models\User;
use App\Services\TokenJWT;
use Firebase\JWT\JWT;
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

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_trigger_email_to_confirm
    public function test_trigger_email_to_confirm(): void
    {
        $manager = Manager::factory()
            ->has(User::factory(1, ['email_verified_at' => null]))
            ->create()
        ;

        $this->post(
            'api/trigger-email-to-confirm',
            [],
            ['Authorization' => (new TokenJWT(new JWT))->generateByUser($manager->user)]
        )->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_trigger_email_to_confirm_is_confirmed
    public function test_trigger_email_to_confirm_is_confirmed(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/trigger-email-to-confirm',
            [],
            ['Authorization' => (new TokenJWT(new JWT))->generateByUser($manager->user)]
        )->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_trigger_email_to_confirm_without_authorization_header
    public function test_trigger_email_to_confirm_without_authorization_header(): void
    {
        $manager = Manager::factory()
            ->has(User::factory())
            ->create()
        ;

        $this->post(
            'api/trigger-email-to-confirm',
            [],
            []
        )->assertStatus(498)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_confirm_email
    public function test_confirm_email(): void
    {
        $manager = Manager::factory()
            ->has(User::factory(1, ['email_verified_at' => null, 'email_verify_code' => 'TESTE']))
            ->create()
        ;

        $this->post(
            'api/confirm-email',
            ['code' => 'TESTE'],
            ['Authorization' => (new TokenJWT(new JWT))->generateByUser($manager->user)]
        )->assertStatus(200)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_confirm_email_is_confirmed
    public function test_confirm_email_is_confirmed(): void
    {
        $manager = Manager::factory()
            ->has(User::factory(1))
            ->create()
        ;

        $this->post(
            'api/confirm-email',
            ['code' => 'TESTE'],
            ['Authorization' => (new TokenJWT(new JWT))->generateByUser($manager->user)]
        )->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_confirm_email_with_incorrect_code
    public function test_confirm_email_with_incorrect_code(): void
    {
        $manager = Manager::factory()
            ->has(User::factory(1, ['email_verify_code' => 'TESTE', 'email_verified_at' => null]))
            ->create()
        ;

        $this->post(
            'api/confirm-email',
            ['code' => 'TEST'],
            ['Authorization' => (new TokenJWT(new JWT))->generateByUser($manager->user)]
        )->assertStatus(401)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }

    # docker-compose exec us php artisan test --filter=AuthenticationControllerTest::test_confirm_email_without_authorization_header
    public function test_confirm_email_without_authorization_header(): void
    {
        $manager = Manager::factory()
            ->has(User::factory(1, ['email_verify_code' => 'TESTE', 'email_verified_at' => null]))
            ->create()
        ;

        $this->post(
            'api/confirm-email',
            ['code' => 'TEST'],
            []
        )->assertStatus(498)
            ->assertJsonStructure([
                'code',
                'message'
            ])
        ;
    }
}
