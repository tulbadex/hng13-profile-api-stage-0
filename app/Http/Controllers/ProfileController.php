<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function me()
    {
        Log::info('Profile endpoint accessed');
        
        try {
            $response = Http::timeout(5)->get('https://catfact.ninja/fact');
            $fact = $response->successful() ? $response->json()['fact'] : 'Cats are amazing creatures!';
            Log::info('Cat fact fetched successfully');
        } catch (\Exception $e) {
            Log::warning('Cat fact API failed', ['error' => $e->getMessage()]);
            $fact = 'Cats are amazing creatures!';
        }

        return response()->json([
            'status' => 'success',
            'user' => [
                'email' => 'tulbadex@gmail.com',
                'name' => 'Ibrahim Adedayo',
                'stack' => 'Laravel/PHP'
            ],
            'timestamp' => Carbon::now()->utc()->toISOString(),
            'fact' => $fact
        ]);
    }
}