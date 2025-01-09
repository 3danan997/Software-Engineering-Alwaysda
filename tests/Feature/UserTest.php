<?php

namespace Tests\Feature;

use App\Models\User\PasswordReset;
use App\Models\User\User;

class UserTest extends TestBase {
    /** @test
     * Create a user, mandatory information are provided
     * it's expected to return a successful response and check of the existence of the provided information
     * login the user afterwards to test the password
     * @return void
     */
    public function createUser() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, mandatory information are provided but the rule of password is invalid
     * it's expected to return a failure response
     * @return void
     */
    public function createUserPasswordInvalid() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "short"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(422);


        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, mandatory information are provided but the rule of email is invalid
     * it's expected to return a failure response
     * @return void
     */
    public function createUserEmailInvalid() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "123456678", "email" => "test.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(422);


        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, mandatory information are provided but the rule of duplicated user (via email) is invalid
     * it's expected to return a failure response
     * @return void
     */
    public function createUserAlreadyExists() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "123456678", "email" => "test@test.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "123456678", "email" => "test@test.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(422);

        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, mandatory information are partially provided
     * it's expected to return a failure response
     * @return void
     */
    public function createUserMissing() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(422);

        $this->logResult($response->getStatusCode() == "422");
    }


    /** @test
     * Create multiple users
     * The provided information are variable and not fixed.
     * @return void
     */
    public function createUserBulk() {
        $this->logCategory();
        $failure = 0;
        for ($i = 0; $i < 40; $i++) {
            $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman{$i}@example.com", "password" => "myPasswordAwesome"]);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'status' => "success",
                ]);

            if ($response->getStatusCode() != "200") $failure++;
        }


        $this->logResult($failure == 0);
    }

    /** @test
     * Login user who exists. Provided information are correct.
     * @return void
     */
    public function loginUser() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/login', ["password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Login user who exists. Provided information are incorrect.
     * @return void
     */
    public function loginUserIncorrect() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/login', ["password" => "myPasswordAwesome2", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(500);


        $this->logResult($response->getStatusCode() == "500");
    }


    /** @test
     * Login user who exists. Email is not provided.
     * @return void
     */
    public function loginUserMissingEmail() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/login', ["password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(422);


        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Login user who exists. Password is not provided.
     * @return void
     */
    public function loginUserMissingPassword() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/login', ["email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(422);


        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Login user who doesn't exist
     * @return void
     */
    public function loginUserWhoNotExist() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/login', ["password" => "myPasswordAwesome", "email" => "testfd@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(500);


        $this->logResult($response->getStatusCode() == "500");
    }

    /** @test
     * Request token of a user who exists
     * @return void
     */
    public function requestToken() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "password" => "myPasswordAwesome", "email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $response = $this->postJson('/api/user/token', ["email" => "test@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $failure = 0;

        // Check if user exists
        $user = User::where("email", "test@gmail.com")->first();
        if (empty($user)) $failure++;
        $this->assertNotEmpty($user);

        // Check if token exists
        if (empty($user) === false) {
            $passwordReset = PasswordReset::where("user_id", $user->id)->first();
            if (empty($passwordReset)) $failure++;
            $this->assertNotEmpty($passwordReset);
        }

        $this->logResult($failure == 0);
    }

    /** @test
     * Request token of a user who doesn't exist
     * @return void
     */
    public function requestTokenUserNotExist() {
        $this->logCategory();
        $response = $this->postJson('/api/user/token', ["email" => "testvcv@gmail.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $failure = 0;

        // Check if user exists
        $user = User::where("email", "testvcvgffd@gmail.com")->first();
        if (empty($user) === false) $failure++;
        $this->assertEmpty($user);

        $this->logResult($failure == 0);
    }

    /** @test
     * Request password reset of a user who already exists
     * @return void
     */
    public function resetPassword() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example.com", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $response = $this->postJson('/api/user/token', ["email" => "musterman@example.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $failure = 0;

        // Check if user exists
        $user = User::where("email", "musterman@example.com")->first();
        if (empty($user)) $failure++;
        $this->assertNotEmpty($user);

        // Check if token exists
        if (empty($user) === false) {
            $passwordReset = PasswordReset::where("user_id", $user->id)->first();
            if (empty($passwordReset)) $failure++;
            $this->assertNotEmpty($passwordReset);

            $response = $this->postJson('/api/user/password', ["email" => "musterman@example.com", "password" => "newPasswordTest", "token" => $passwordReset->token]);
            $this->logMessage($response->decodeResponseJson());
            $response->assertStatus(200);


            $response = $this->postJson('/api/user/login', ["email" => "musterman@example.com", "password" => "newPasswordTest"]);
            $this->logMessage($response->decodeResponseJson());
            $response
                ->assertStatus(200);

        }


        $this->logResult($failure == 0);
    }

    /** @test
     * Request password reset of a user who already exists with invalid password (password's rule)
     * @return void
     */
    public function resetPasswordInvalidPassword() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example.com", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $response = $this->postJson('/api/user/token', ["email" => "musterman@example.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);



        $failure = 0;

        // Check if user exists
        $user = User::where("email", "musterman@example.com")->first();
        if (empty($user)) $failure++;
        $this->assertNotEmpty($user);

        // Check if token exists
        if (empty($user) === false) {
            $passwordReset = PasswordReset::where("user_id", $user->id)->first();
            if (empty($passwordReset)) $failure++;
            $this->assertNotEmpty($passwordReset);

            $response = $this->postJson('/api/user/password', ["email" => "musterman@example.com", "password" => "newPasswordTest", "token" => $passwordReset->token]);
            $this->logMessage($response->decodeResponseJson());
            $response->assertStatus(200);


            $response = $this->postJson('/api/user/login', ["email" => "musterman@example.com", "password" => "net"]);
            $this->logMessage($response->decodeResponseJson());
            $response
                ->assertStatus(500);

        }


        $this->logResult($failure == 0);
    }


    /** @test
     * Request password reset of a user who already exists with incorrect password
     * @return void
     */
    public function resetPasswordIncorrectPassword() {
        $this->logCategory();
        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example.com", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $response = $this->postJson('/api/user/token', ["email" => "musterman@example.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);


        $failure = 0;

        // Check if user exists
        $user = User::where("email", "musterman@example.com")->first();
        if (empty($user)) $failure++;
        $this->assertNotEmpty($user);

        // Check if token exists
        if (empty($user) === false) {
            $passwordReset = PasswordReset::where("user_id", $user->id)->first();
            if (empty($passwordReset)) $failure++;
            $this->assertNotEmpty($passwordReset);

            $response = $this->postJson('/api/user/password', ["email" => "musterman@example.com", "password" => "newPasswordTest", "token" => $passwordReset->token]);
            $this->logMessage($response->decodeResponseJson());
            $response->assertStatus(200);


            $response = $this->postJson('/api/user/login', ["email" => "musterman@example.com", "password" => "newPasswordTest2"]);
            $this->logMessage($response->decodeResponseJson());
            $response
                ->assertStatus(500);

        }


        $this->logResult($failure == 0);
    }

    /** @test
     * Request password reset of a user who already exists with incorrect token
     * @return void
     */
    public function resetPasswordIncorrectToken() {
        $this->logCategory();

        $response = $this->postJson('/api/user/register', ['first_name' => 'Muster', "last_name" => "Man", "email" => "musterman@example.com", "password" => "myPasswordAwesome"]);
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);


        $response = $this->postJson('/api/user/token', ["email" => "musterman@example.com"]);
        $this->logMessage($response->decodeResponseJson());
        $response->assertStatus(200);

        $failure = 0;

        // Check if user exists
        $user = User::where("email", "musterman@example.com")->first();
        if (empty($user)) $failure++;
        $this->assertNotEmpty($user);

        // Check if token exists
        if (empty($user) === false) {
            $passwordReset = PasswordReset::where("user_id", $user->id)->first();
            if (empty($passwordReset)) $failure++;
            $this->assertNotEmpty($passwordReset);

            $response = $this->postJson('/api/user/password', ["email" => "musterman@example.com", "password" => "newPasswordTest", "token" => $passwordReset->token . "-"]);
            $this->logMessage($response->decodeResponseJson());
            $response->assertStatus(500);


            $response = $this->postJson('/api/user/login', ["email" => "musterman@example.com", "password" => "newPasswordTest"]);
            $this->logMessage($response->decodeResponseJson());
            $response
                ->assertStatus(500);

        }


        $this->logResult($failure == 0);
    }
}
