<?php
namespace App\Http\Controllers\Web;
use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Order;
use App\Models\Password_reset_tokens;
use Illuminate\Foundation\Validation\ValidatesRequests;
use DB;
use Artisan;
use Carbon\Carbon;


class UsersController extends Controller{
    use ValidatesRequests;

    public function showCreateCustomer()
{
    if (!auth()->user()->hasPermissionTo('create_users')) {
        abort(403, 'Unauthorized action.');
    }

    return view('users.create_customer');
}

    public function createCustomerByAdmin(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('Employee');
        return redirect('/');
}

    public function list(Request $request) {
        if(!auth()->user()->hasPermissionTo('show_users'))abort(401);
            if (auth()->user()->hasRole('admin')){
                $query = User::select('*');
                $query->when($request->keywords,
                fn($q)=> $q->where("name", "like", "%$request->keywords%"));
                $users = $query->get();
                return view('users.list', compact('users'));
            }else {
                $query = User::role('client')->select('*');
                $query->when($request->keywords,
                fn($q)=> $q->where("name", "like", "%$request->keywords%"));
                $users = $query->get();
                return view('users.list', compact('users'));
            }
    }


public function showRegister(Request $request){
    return view('users.register');
}

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('client');

        $title = "Verification Link";
        $token = Crypt::encryptString(json_encode(['id' => $user->id, 'email' => $user->email]));
        $link = route("verify", ['token' => $token]);
        Mail::to($user->email)->send(new VerificationEmail($link, $user->name));
        return redirect('/');
    }

    public function sendResetLinkEmail(Request $request){

        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withInput($request->input())->withErrors(['email' => 'We can\'t find a user with that email address.']);
        }

        $token = Str::random(60);

        Password_reset_tokens::create([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        $link = route('password.reset', ['token' => $token, 'email' => $user->email]);

        Mail::to($user->email)->send(new VerificationEmail($link, $user->name));

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetForm(Request $request, $token = null ){
        $email = $request->email;
        return view('users.reset_password', compact('token', 'email'));
}

    public function reset(Request $request){
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $passwordReset = Password_reset_tokens::where('email', $request->email)
                            ->where('token', $request->token)
                            ->first();

        if (!$passwordReset || now()->subMinutes(60) > $passwordReset->created_at) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect('/login')->with('status', 'Your password has been reset!');
    }
    // Show Login Page
    public function showLogin()
    {
        return view('users.login');
    }

    // Login Logic
    public function login(Request $request)
    {
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password]))
            return redirect()->back()->withInput($request->input())->withErrors('Invalid login information.');

        $user = User::where('email', $request->email)->first();
        Auth::setUser($user);

        if(!$user->email_verified_at)
            return redirect()->back()->withInput($request->input())->withErrors('Your email is not verified.');

        return redirect('/');
    }

    public function verify(Request $request) {

        $decryptedData = json_decode(Crypt::decryptString($request->token), true);
        $user = User::find($decryptedData['id']);
        if(!$user) abort(401);
        $user->email_verified_at = Carbon::now();
        $user->save();

        return view('users.verified', compact('user'));
}

    // Logout Logic
    public function logout()
    {
        Auth::logout();
        return redirect('login');
    }

    public function profile(Request $request, User $user = null)
    {
        $user = Auth::user();

        $user = $user??auth()->user();
        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }
        $permissions = [];
        foreach($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach($user->roles as $role) {
            foreach($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }
        $orders = $user->orders()->with('product')->get();

        return view('users.profile', compact('user', 'permissions','orders'));
    }

    public function edit(Request $request, User $user = null) {
        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }

        $roles = [];
        foreach(Role::all() as $role) {
            $role->taken = ($user->hasRole($role->name));
            $roles[] = $role;
        }

        $permissions = [];
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach(Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }
    public function save(Request $request, User $user) {
        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if(auth()->user()->hasPermissionTo('admin_users')) {

            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            Artisan::call('cache:clear');
        }

        //$user->syncRoles([1]);
        //Artisan::call('cache:clear');

        return redirect(route('profile', ['user'=>$user->id]));
}
    public function delete(Request $request, User $user) {

        if(!auth()->user()->hasPermissionTo('delete_users')) abort(401);

        //$user->delete();

        return redirect()->route('users');
}
    public function editPassword(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }

        return view('users.edit_password', compact('user'));
}

    public function savePassword(Request $request, User $user) {

        if(auth()->id()==$user?->id) {

            $this->validate($request, [
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);

            if(!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {

                Auth::logout();
                return redirect('/');
            }
        }
        else if(!auth()->user()->hasPermissionTo('edit_users')) {

            abort(401);
        }

        $user->password = bcrypt($request->password); //Secure
        $user->save();

        return redirect(route('profile', ['user'=>$user->id]));
        }
        public function showBalance(User $user){
        return view('users.update_balance',compact('user'));
    }

    public function updateBalance(Request $request, User $user){

        if (!auth()->user()->hasPermissionTo('update_balance')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'balance' => 'required|numeric|min:0',
        ]);

        $user->balance = $request->input('balance');
        $user->save();

        return redirect()->route('users');

    }

    public function add_Gift(Request $request, User $user){
    $currentUser = auth()->user();

    if (!$currentUser->hasPermissionTo('manage_sales')) {
        abort(403, 'Unauthorized action.');
    }

    // if ($currentUser->last_gift && $currentUser->last_gift = now() < 30) {
    //     return redirect()->route('users') ;
    // }

    $user->balance += 10000;
    $user->save();

    $currentUser->last_gift = now();
    $currentUser->save();

    return redirect()->route('users');
}
    public function redirectToGoogle()
        {
            return Socialite::driver('google')->redirect();
        }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if the user already exists
            $user = User::where('email', $googleUser->getEmail())->first();

            // If the user doesn't exist, create a new user
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'email_verified_at' => now(), // Automatically verify email
                    'password' => bcrypt(uniqid()), // Random password
                ]);
                $user->assignRole('customer'); // Assign the 'customer' role
            }

            // Log in the user
            Auth::login($user);
            return redirect('/');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors('Unable to login using Google.');
        }
    }
}
