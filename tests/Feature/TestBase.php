<?php

namespace Tests\Feature;


use App\Console\Kernel;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase;
use PHPUnitColors\Display;

class TestBase extends TestCase {
    use InteractsWithSession;
    // Roll-back changes after testing
    use DatabaseTransactions;

    public function createApplication() {
        $app = require __DIR__ . '/../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }

    public function logCategory() {
        $category = debug_backtrace()[1]['function'];
        if(empty($category) === true) $category = "Nicht-definiert";

        echo "================\n";
        echo "[{$category}]:\n";
    }

    public function logMessage($obj) {
        $message = "Es gibt keine Rückgabe.";
        if(empty($obj) === false) {
            if(isset($obj["message"])) {
                $message = $obj["message"];
            } else if(isset($obj["total_count"]) === false && isset($obj["status"]) === false && isset($obj["user"]) === false){
                $message = json_encode($obj);
            }
        }
        echo "  - $message";
        echo "\n";
    }

    public function logResult($success) {
        echo "  - Ergebnis: ";
        if($success) {
            echo Display::OK("Success");
        } else {
            echo Display::warning("Failure");
        }
    }
}
