<?php

namespace Tests\Feature;

use App\Models\Misc\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ContactTest extends TestBase {

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

    public function createSampleContact() {

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
    }

    /** @test
     * Create a user, login the user, get contacts
     * @return void
     */

    public function getContacts() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->getJson('/api/contacts');
        $this->logMessage($response->decodeResponseJson());
        $response
            ->assertStatus(200)
            ->assertJsonStructure(["contacts", "total_count"]);

        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, create contact, all information are provided
     * @return void
     */

    public function createContact() {
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


    /** @test
     * Create a user, login the user, create contact, Missing information
     * @return void
     */

    public function createContactMissing() {
        $this->logCategory();

        $this->createSampleAccount();


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/contacts', ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $response
            ->assertStatus(422)
            ->assertJson(["status" => "error"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, login the user, create contact, empty information
     * @return void
     */

    public function createContactEmpty() {
        $this->logCategory();

        $this->createSampleAccount();


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/contacts', ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => "", "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());


        $response
            ->assertStatus(422)
            ->assertJson(["status" => "error"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, login the user, create contact, all information are provide, birthday is invalid
     * @return void
     */

    public function createContactInvalidBirthday() {
        $this->logCategory();

        $this->createSampleAccount();


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/contacts', ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2040-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
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

    /** @test
     * Create a user, login the user, create contact, all information are provide, birthday's format is invalid
     * @return void
     */

    public function createContactInvalidBirthdayFormat() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson('/api/contacts', ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2040.01.10", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $response
            ->assertStatus(422);
        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, login the user, create contact, all information are provided, invalid reminders
     * @return void
     */

    public function createContactRemindersInvalid() {
        $this->logCategory();

        $this->createSampleAccount();

        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson("/api/contacts", ["first_name" => "Muster", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Selbst eingeben...","frequency_value": null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertEmpty($contact);

        if(empty($contact) === false)
            Storage::disk('public')->assertExists("avatars/{$contact->avatar}");

        $response
            ->assertStatus(500);
        $this->logResult($response->getStatusCode() == "500");
    }

    /** @test
     * Create a user, login the user, update contact, all information are provided
     * @return void
     */

    public function updateContact() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson("/api/contacts/{$contact->id}", ["first_name" => "MusterNew", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "MusterNew")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);
        $this->assertEquals($contact->first_name, "MusterNew");

        if(empty($contact) === false)
            Storage::disk('public')->assertExists("avatars/{$contact->avatar}");

        $response
            ->assertStatus(200)
            ->assertJson(["status" => "success"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, update contact, all information are provided, remove avatar
     * @return void
     */

    public function updateContactRemoveAvatar() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson("/api/contacts/{$contact->id}", ["first_name" => "MusterNew", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar_changed" => "true"]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "MusterNew")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);
        $this->assertEquals($contact->first_name, "MusterNew");

        if(empty($contact) === false)
            $this->assertEmpty($contact->avatar);

        $response
            ->assertStatus(200)
            ->assertJson(["status" => "success"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "200");
    }


    /** @test
     * Create a user, login the user, update contact, all information are provided, invalid birthday
     * @return void
     */

    public function updateContactBirthdayInvalid() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson("/api/contacts/{$contact->id}", ["first_name" => "MusterNew", "last_name" => "Man", "birthday" => "2010-0110", "priority" => "Familie", "reminders" => '[{"frequency":"Jeden Monat","frequency_value":null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);
        $this->assertEquals($contact->first_name, "Muster");

        if(empty($contact) === false)
            Storage::disk('public')->assertExists("avatars/{$contact->avatar}");

        $response
            ->assertStatus(422);
        $this->logResult($response->getStatusCode() == "422");
    }

    /** @test
     * Create a user, login the user, update contact, all information are provided, invalid reminders
     * @return void
     */

    public function updateContactRemindersInvalid() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->withHeader("Content-Type", "multipart/form-data")->postJson("/api/contacts/{$contact->id}", ["first_name" => "MusterNew", "last_name" => "Man", "birthday" => "2010-01-10", "priority" => "Familie", "reminders" => '[{"frequency":"Selbst eingeben...","frequency_value": null,"type":"Schreiben"}]', "avatar" => UploadedFile::fake()->image('avatar.jpg')]);
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);
        $this->assertEquals($contact->first_name, "Muster");

        if(empty($contact) === false)
            Storage::disk('public')->assertExists("avatars/{$contact->avatar}");

        $response
            ->assertStatus(500);
        $this->logResult($response->getStatusCode() == "500");
    }


    /** @test
     * Create a user, login the user, create contact, all information are provided, delete it
     * @return void
     */

    public function deleteContact() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->deleteJson("/api/contacts/{$contact->id}");
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertEmpty($contact);

        $response
            ->assertStatus(200)
            ->assertJson(["status" => "success"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "200");
    }

    /** @test
     * Create a user, login the user, create contact, all information are provided, delete different one
     * @return void
     */

    public function deleteContactInvalid() {
        $this->logCategory();

        $this->createSampleContact();

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);


        $response = $this->deleteJson("/api/contacts/43242432423");
        $this->logMessage($response->decodeResponseJson());

        $contact = Contact::where("first_name", "Muster")->where("priority", "Familie")->where("last_name", "Man")->first();
        $this->assertNotEmpty($contact);

        $response
            ->assertStatus(500)
            ->assertJson(["status" => "failure"])
            ->assertJsonStructure(["status", "message"]);
        $this->logResult($response->getStatusCode() == "500");
    }

}
