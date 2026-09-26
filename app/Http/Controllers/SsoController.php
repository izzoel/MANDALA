<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Aplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function redirect()
    {
        $query = http_build_query([
            'client_id' => env('OIDC_CLIENT_ID'),
            'redirect_uri' => env('OIDC_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
        ]);

        return redirect(env('OIDC_ISSUER') . '/protocol/openid-connect/auth?' . $query);
    }

    public function callback()
    {
        $code = request('code');

        if (! $code) {
            abort(403, 'Authorization code not found.');
        }

        $tokenResponse = Http::asForm()->post(env('OIDC_ISSUER') . '/protocol/openid-connect/token', [
            'grant_type' => 'authorization_code',
            'client_id' => env('OIDC_CLIENT_ID'),
            'client_secret' => env('OIDC_CLIENT_SECRET'),
            'redirect_uri' => env('OIDC_REDIRECT_URI'),
            'code' => $code,
        ]);

        if (! $tokenResponse->successful()) {
            abort(403, 'Failed to get access token.');
        }

        $accessToken = $tokenResponse->json('access_token');

        $userInfoResponse = Http::withToken($accessToken)
        ->get(env('OIDC_ISSUER') . '/protocol/openid-connect/userinfo');

        if (! $userInfoResponse->successful()) {
            abort(403, 'Failed to get user info.');
        }

        $ssoUser = $userInfoResponse->json();

        $user = User::updateOrCreate(
            ['email' => $ssoUser['email']],
            [
                'name' => $ssoUser['name'] ?? $ssoUser['preferred_username'] ?? $ssoUser['email'],
                'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user);

            return redirect()->intended('/dashboard');
        }

        public function ticket(string $nama)
        {
            $nama = Str::lower($nama);

            $aplikasi = Aplikasi::whereRaw('LOWER(nama) = ?', [$nama])
            ->firstOrFail();

            $user = Auth::user();

            $ticket = Str::random(80);

            Cache::put("sso_ticket:{$ticket}", [
                'user_id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'aplikasi_id' => $aplikasi->id,
                'aplikasi_nama' => $aplikasi->nama,
                'aplikasi_url' => rtrim($aplikasi->url, '/'),
                'created_at' => now()->toDateTimeString(),
            ], now()->addSeconds(60));

            return redirect()->route('sso.handoff', [
                'nama' => $nama,
                'ticket' => $ticket,
            ]);
        }

        public function handoff(string $nama, string $ticket)
        {
            $nama = Str::lower($nama);

            $payload = Cache::get("sso_ticket:{$ticket}");

            if (! $payload) {
                abort(403, 'Ticket tidak valid atau sudah expired.');
            }

            if (Str::lower($payload['aplikasi_nama']) !== $nama) {
                abort(403, 'Ticket tidak sesuai dengan aplikasi tujuan.');
            }

            $targetUrl = rtrim($payload['aplikasi_url'], '/');

            return redirect()->away($targetUrl . '/sso/receive?' . http_build_query([
                'ticket' => $ticket,
                'source' => Str::lower(env('APP_NAME')),
                'next' => '/dashboard',
            ]));
        }

        public function verify(Request $request)
        {
            $request->validate([
                'ticket' => ['required', 'string'],
            ]);

            $secretFromApp = $request->header('X-SSO-Handoff-Secret');
            $secretFromMandala = env('SSO_HANDOFF_SECRET');

            if (blank($secretFromMandala) || blank($secretFromApp)) {
                abort(403, 'Handoff secret is missing.');
            }

            if (! hash_equals((string) $secretFromMandala, (string) $secretFromApp)) {
                abort(403, 'Invalid handoff secret.');
            }

            $payload = Cache::pull("sso_ticket:{$request->ticket}");

            if (! $payload) {
                return response()->json([
                    'message' => 'Ticket tidak valid, sudah expired, atau sudah pernah dipakai.',
                ], 401);
            }

            return response()->json([
                'email' => $payload['email'],
                'name' => $payload['name'],
                'user_id' => $payload['user_id'],
                'aplikasi_id' => $payload['aplikasi_id'],
                'aplikasi_nama' => $payload['aplikasi_nama'],
            ]);
        }
    }
