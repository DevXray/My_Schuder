<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ChatbotController;
use Illuminate\Http\Request;

class TestChatbot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:chatbot {--message= : Test message to send} {--api-only : Test only Gemini API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Chatbot Gemini API Integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Chatbot Gemini API Integration');
        $this->newLine();

        $message = $this->option('message') ?: 'halo, apa kabar?';
        $apiOnly = $this->option('api-only');

        if ($apiOnly) {
            $this->testGeminiAPIOnly($message);
        } else {
            $this->testFullIntegration($message);
        }
    }

    private function testGeminiAPIOnly($message)
    {
        $this->info('🔍 Testing Gemini API Connection Only');

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = env('GEMINI_API_URL');

        if (!$apiKey) {
            $this->error('❌ GEMINI_API_KEY not configured in .env');
            return;
        }

        $this->info("📡 API URL: {$apiUrl}");
        $this->info("🔑 API Key: " . substr($apiKey, 0, 10) . '...');

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [['text' => $message]]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1000,
            ]
        ];

        $this->info("📤 Sending request to Gemini API...");
        $this->info("💬 Message: {$message}");

        try {
            $response = Http::timeout(30)->post($apiUrl . '?key=' . $apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

                $this->newLine();
                $this->info('✅ Gemini API Response:');
                $this->line('"' . $aiResponse . '"');
            } else {
                $this->error('❌ Gemini API Error: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection Error: ' . $e->getMessage());
        }
    }

    private function testFullIntegration($message)
    {
        $this->info('🔄 Testing Full Chatbot Integration');

        // Test 1: Controller Method
        $this->info('1️⃣ Testing ChatbotController...');

        try {
            $controller = new ChatbotController();
            $request = new Request();
            $request->merge([
                'message' => $message,
                'conversation_history' => []
            ]);

            $response = $controller->chat($request);
            $data = $response->getData(true);

            if ($data['success']) {
                $this->info('✅ Controller Response:');
                $this->line('   Source: ' . $data['source']);
                $this->line('   Response: "' . substr($data['response'], 0, 100) . '..."');
                if (isset($data['fallback'])) {
                    $this->warn('   ⚠️ Using fallback: ' . ($data['fallback'] ? 'Yes' : 'No'));
                }
            } else {
                $this->error('❌ Controller Error: ' . ($data['message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->error('❌ Controller Exception: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 2: Route exists
        $this->info('2️⃣ Testing Route Configuration...');
        $routes = app('router')->getRoutes();
        $chatRouteExists = false;

        foreach ($routes as $route) {
            if ($route->uri() === 'chat' && in_array('POST', $route->methods())) {
                $chatRouteExists = true;
                break;
            }
        }

        if ($chatRouteExists) {
            $this->info('✅ Route /chat (POST) exists');
        } else {
            $this->error('❌ Route /chat (POST) not found');
        }

        $this->newLine();

        // Test 3: Environment variables
        $this->info('3️⃣ Testing Environment Configuration...');

        $apiKey = env('GEMINI_API_KEY');
        $apiUrl = env('GEMINI_API_URL');

        if ($apiKey && $apiKey !== 'your_actual_gemini_api_key_here') {
            $this->info('✅ GEMINI_API_KEY configured');
        } else {
            $this->error('❌ GEMINI_API_KEY not properly configured');
        }

        if ($apiUrl) {
            $this->info('✅ GEMINI_API_URL configured');
        } else {
            $this->error('❌ GEMINI_API_URL not configured');
        }

        $this->newLine();

        // Test 4: Middleware
        $this->info('4️⃣ Testing Middleware Registration...');
        $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
        $middlewareGroups = $kernel->getMiddlewareGroups();

        if (isset($middlewareGroups['web']) && in_array(\App\Http\Middleware\ChatbotRateLimit::class, $middlewareGroups['web'])) {
            $this->info('✅ ChatbotRateLimit middleware registered');
        } else {
            $this->error('❌ ChatbotRateLimit middleware not registered');
        }

        $this->newLine();
        $this->info('🎉 Testing completed!');
    }
}
