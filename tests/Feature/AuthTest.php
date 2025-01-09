<?php

namespace Tests\Feature;




use App\Models\User\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestBase {
    /**
     * Create a user, login the user
     * @return void
     */
    public function createSampleAccount() {
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example", "password" => "myPasswordAwesome"]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);

        $this->logMessage($response->decodeResponseJson());

        $response = $this->postJson('/api/user/login', ["email" => "musterman@example", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);
    }

    /** @test
     * fetch the user
     * @return void
     */
    public function fetchUser() {
        $this->logCategory();
        $this->createSampleAccount();

        $response = $this->getJson('/api/user/fetch');
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJsonStructure(["user"]);


        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * fetch the user, but the user is not authenticated
     * @return void
     */
    public function fetchUserNoAuth() {
        $this->logCategory();

        $response = $this->getJson('/api/user/fetch');
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(401);


        $this->logResult($response->getStatusCode() == "401");
    }

    /** @test
     * fetch the user, logout
     * @return void
     */
    public function logoutUser() {
        $this->logCategory();

        $user = User::create([
            'first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example", "password" => "myPasswordAwesome"
        ]);
        Auth::setUser($user);

        $response = $this->postJson('/api/user/logout');
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200);

        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * fetch the user, dont set as auth, logout
     * @return void
     */
    public function logoutUserFailure() {
        $this->logCategory();

        $response = $this->postJson('/api/user/logout');
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(401);

        $this->logResult($response->getStatusCode() == "401");
    }


    /** @test
     * update the user
     * @return void
     */
    public function updateUser() {
        $this->logCategory();

        $user = User::create([
            'first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example", "password" => "myPasswordAwesome"
        ]);
        // Set auth user so that we test the Auth functionality of Laravel
        Auth::setUser($user);

        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/user/preferences', ["avatar_changed" => "true", "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJsonStructure(["user", "message", "status"]);

        $user = User::where("email", "musterman@example")->first();
        self::assertNotEmpty($user);
        Storage::disk('public')->assertExists("avatars/{$user->avatar}");

        $this->logResult($response->getStatusCode() == "200");
    }

}
