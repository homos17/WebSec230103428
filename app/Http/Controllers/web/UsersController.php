<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;


class UsersController extends Controller{
    use ValidatesRequests;


public function showRegister(Request $request){
    return view('auth.register');
}

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect('/');
    }

    // Show Login Page
    public function showLogin()
    {
        return view('auth.login');
    }

    // Login Logic
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect('/');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Logout Logic
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }
}
