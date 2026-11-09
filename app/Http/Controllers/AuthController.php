<?php

namespace App\Http\Controllers;

use App\Models\CandidateProfile;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::transliterate(Str::lower($credentials['email']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()->withErrors([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ])->onlyInput('email');
        }

        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);

            return back()->withErrors(['email' => 'The provided credentials could not be verified.'])
                ->onlyInput('email');
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended($this->dashboardUrl($request->user()));
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->mixedCase()->numbers()],
            'role' => ['required', 'in:employee,employer'],
            'company_name' => ['required_if:role,employer', 'nullable', 'string', 'max:180'],
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'is_active' => true,
            ]);

            if ($data['role'] === 'employer') {
                $name = $data['company_name'];
                Company::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.$user->id,
                ]);
            } else {
                CandidateProfile::create(['user_id' => $user->id]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->role.'.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function forgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($request->only('email'));

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'status' : 'error',
            __($status)
        );
    }

    public function resetPasswordForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->mixedCase()->numbers()],
        ]);

        $status = Password::reset($data, function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password)])->save();
            $user->setRememberToken(Str::random(60));
            $user->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    private function dashboardUrl(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'employer' => route('employer.dashboard'),
            default => route('candidate.dashboard'),
        };
    }
}
