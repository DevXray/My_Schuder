<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TestHttpEndpoint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:http-endpoint {--message= : Test message to send} {--with-csrf : Include CSRF token}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Chatbot HTTP Endpoint with proper Laravel setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌐 Testing Chatbot HTTP Endpoint');
        $this->newLine();

        $message = $this->option('message') ?: 'halo, apa kabar?';
        $withCsrf = $this->option('with-csrf');

        // Test 1: Basic endpoint accessibility
        $this->info('1️⃣ Testing Basic Endpoint Access...');
        try {
            $response = Http::get('http://127.0.0.1:8000/');
            if ($response->successful()) {
                $this->info('✅ Homepage accessible (HTTP ' . $response->status() . ')');
            } else {
                $this->error('❌ Homepage not accessible (HTTP ' . $response->status() . ')');
                return;
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            return;
        }

        $this->newLine();

        // Test 2: Chat endpoint without CSRF (should fail)
        $this->info('2️⃣ Testing Chat Endpoint without CSRF...');
        try {
            $response = Http::post('http://127.0.0.1:8000/chat', [
                'message' => $message,
                'conversation_history' => []
            ]);

            if ($response->status() === 419) {
                $this->warn('⚠️ CSRF token required (expected)');
            } elseif ($response->successful()) {
                $this->error('❌ Unexpected success without CSRF token');
            } else {
                $this->error('❌ Unexpected error: HTTP ' . $response->status());
            }
        } catch (\Exception $e) {
            $this->error('❌ Request failed: ' . $e->getMessage());
        }

        $this->newLine();

        // Test 3: Chat endpoint with CSRF (if requested)
        if ($withCsrf) {
            $this->info('3️⃣ Testing Chat Endpoint with CSRF Token...');

            // First get CSRF token from homepage
            try {
                $response = Http::get('http://127.0.0.1:8000/');
                $html = $response->body();

                // Extract CSRF token from meta tag
                preg_match('/<meta name="csrf-token" content="([^"]+)">/', $html, $matches);
                $csrfToken = $matches[1] ?? null;

                if (!$csrfToken) {
                    $this->error('❌ CSRF token not found in HTML');
                    return;
                }

                $this->info('🔑 CSRF Token: ' . substr($csrfToken, 0, 20) . '...');

                // Now test chat endpoint with CSRF
                $response = Http::withHeaders([
                    'X-CSRF-TOKEN' => $csrfToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])->post('http://127.0.0.1:8000/chat', [
                    'message' => $message,
                    'conversation_history' => []
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->info('✅ Chat endpoint successful!');
                    $this->line('   Response: ' . substr($data['response'] ?? 'No response', 0, 100) . '...');
                    $this->line('   Source: ' . ($data['source'] ?? 'Unknown'));
                    $this->line('   Fallback: ' . (($data['fallback'] ?? false) ? 'Yes' : 'No'));
                } else {
                    $this->error('❌ Chat endpoint failed: HTTP ' . $response->status());
                    $this->error('   Response: ' . $response->body());
                }

            } catch (\Exception $e) {
                $this->error('❌ CSRF test failed: ' . $e->getMessage());
            }
        } else {
            $this->warn('⚠️ Skipping CSRF test (use --with-csrf to enable)');
        }

        $this->newLine();
        $this->info('🎉 HTTP Endpoint testing completed!');
    }
}
