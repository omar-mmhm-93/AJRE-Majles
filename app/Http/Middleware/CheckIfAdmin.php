<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfAdmin
{
    public function handle($request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return redirect()->guest(backpack_url('login'));
        }

        $user = backpack_user();

        if (! $user->can('login_to_admin_panel')) {

            // logout
            backpack_auth()->logout();

            // invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->guest(backpack_url('login'))
                ->withErrors([
                    'email' => 'you dont have "login_to_admin_panel" permission'
                ]);
        }

        return $next($request);
    }

}
