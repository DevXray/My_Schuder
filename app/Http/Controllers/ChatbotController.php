<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    private $geminiApiKey;
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->geminiApiKey = env('GEMINI_API_KEY');
    }

    public function sendMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'conversation_history' => 'nullable|array'
            ]);

            $userMessage = $validated['message'];
            $conversationHistory = $validated['conversation_history'] ?? [];

            if (!$this->geminiApiKey || empty($this->geminiApiKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key tidak dikonfigurasi',
                    'source' => 'error'
                ], 500);
            }

            $contents = [];

            if (!empty($conversationHistory)) {
                $historySlice = array_slice($conversationHistory, -10);
                foreach ($historySlice as $msg) {
                    $contents[] = [
                        'role' => $msg['role'] === 'user' ? 'user' : 'model',
                        'parts' => [
                            ['text' => $msg['text']]
                        ]
                    ];
                }
            }

            $contents[] = [
                'role' => 'user',
                'parts' => [
                    ['text' => $userMessage]
                ]
            ];

            $systemPrompt = 'Kamu adalah asisten virtual untuk platform pembelajaran "My Schuder". 
Kamu membantu siswa dengan informasi tentang:
- Materi pembelajaran (24 materi tersedia)
- Tugas dan deadline
- Jadwal kelas
- Nilai dan progress belajar
- Tips belajar dan motivasi

Berikan jawaban yang ramah, membantu, dan dalam bahasa Indonesia.
Gunakan emoji yang sesuai untuk membuat percakapan lebih menyenangkan.';

            $response = Http::timeout(30)
                ->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                    'contents' => $contents,
                    'systemInstruction' => [
                        'parts' => [
                            ['text' => $systemPrompt]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.9,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ]
                    ]
                ]);

            if ($response->failed()) {
                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? 'Unknown API Error';
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gemini API Error: ' . $errorMessage,
                    'source' => 'error'
                ], $response->status());
            }

            $data = $response->json();

            if (isset($data['candidates']) && !empty($data['candidates'])) {
                $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($aiResponse) {
                    return response()->json([
                        'success' => true,
                        'response' => $aiResponse,
                        'source' => 'gemini',
                        'fallback' => false
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid response format from Gemini API',
                'source' => 'error'
            ], 500);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Chat API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
                'source' => 'error'
            ], 500);
        }
    }

    public function testGeminiAPI()
    {
        try {
            if (!$this->geminiApiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key tidak dikonfigurasi'
                ], 500);
            }

            $response = Http::timeout(10)
                ->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'test']
                            ]
                        ]
                    ]
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Gemini API tersedia'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gemini API Error: ' . $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ], 500);
        }
    }
}