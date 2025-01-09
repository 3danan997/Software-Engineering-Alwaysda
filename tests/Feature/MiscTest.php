<?php

namespace Tests\Feature;

use App\Models\Misc\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MiscTest extends TestBase {

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
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ]);
        $this->logMessage($response->decodeResponseJson());
    }

    /** @test
     * Create a user, login the user, get stats
     * @return void
     */
    public function getStats() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->getJson('/api/stats');
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ])->assertJsonStructure(["status", "stats"]);

        $this->logMessage($response->decodeResponseJson());
        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, get reminders
     * @return void
     */
    public function getReminders() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->getJson('/api/reminders');
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ])->assertJsonStructure(["status", "reminders"]);

        $this->logMessage($response->decodeResponseJson());
        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, get today reminders
     * @return void
     */
    public function getRemindersToday() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->getJson('/api/reminders');
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => "success",
            ])->assertJsonStructure(["status", "reminders"]);

        $this->logMessage($response->decodeResponseJson());
        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, create contact, all information are provided, validate reminder is today
     * @return void
     */

    public function createTodayReminder() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/contacts', ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);

        if(empty($contact) === false)
            Storage::disk('public')->assertExists("avatars/{$contact->avatar}");

        $response
            ->assertStatus(200)
            ->assertJson(["status" => "success"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "200");
    }

}
