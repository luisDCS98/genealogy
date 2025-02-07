<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use LaravelEnso\Core\Notifications\ResetPassword;
use LaravelEnso\Forms\TestTraits\DestroyForm;
use LaravelEnso\Forms\TestTraits\EditForm;
use LaravelEnso\Tables\Traits\Tests\Datatable;
use App\Models\User;
use Tests\TestCase;

class UserRestrictTest extends TestCase
{
    use Datatable, DestroyForm, EditForm, RefreshDatabase;

    private $permissionGroup = 'administration.userrestrict';
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed()
            ->actingAs(User::first());

        $this->testModel = User::factory()->make();
    }

    /** @test */
    public function can_view_create_form()
    {
        $this->get(route($this->permissionGroup.'.create', [$this->testModel->id], false))
            ->assertStatus(200)
            ->assertJsonStructure(['form']);
    }

    /** @test */
    public function can_store_user()
    {
        Notification::fake();

        $response = $this->post(
            route('administration.userrestrict.store', [], false),
            $this->testModel->toArray()
        );

        $user = App::make(User::class)->whereEmail($this->testModel->email)
            ->first();

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJsonFragment([
                'redirect' => 'administration.userrestrict.edit',
                'param' => ['user' => $user->id],
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function can_update_user()
    {
        $this->testModel->save();

        $this->testModel->is_active = ! $this->testModel->is_active;

        $this->patch(
            route('administration.userrestrict.update', $this->testModel->id, false),
            $this->testModel->toArray()
        )->assertStatus(200)
            ->assertJsonStructure(['message']);

        $this->assertEquals($this->testModel->is_active, $this->testModel->fresh()->is_active);
    }

    /** @test */
    public function get_option_list()
    {
        $this->testModel->is_active = true;
        $this->testModel->save();

        $this->get(route('administration.userrestrict.options', [
            'query' => $this->testModel->email,
            'limit' => 10,
        ], false))
            ->assertStatus(200)
            ->assertJsonFragment(['email' => $this->testModel->email]);
    }
}
