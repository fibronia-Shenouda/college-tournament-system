<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use App\Rules\UserExists;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Rules\Between10And40Words;
use App\Rules\UniqueEmail;
use App\Rules\UniquePhoneNumber;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
  // GET PRIVILEDGES
  public function show()
  {
    return view('Authentication.whoareyou');
  }

  // SHOW FORMS
  public function showForms(Request $request)
  {
    if ($request->priviledge == 'student') {
      return view('Authentication.studentRegister');
    }
    if ($request->priviledge == 'admin') {
      return view('Authentication.admin');
    }
    if ($request->priviledge == 'superadmin') {
      return view('Authentication.superadmin');
    }
  }

  // REGISTING PROCCESS
  public function regist(Request $request)
  {
    $formFields = $request->validate([
      'name' => 'required|min:3|regex:/[a-zA-Z]+/',
      'email' => ['required', 'email', Rule::unique('users', 'email')],
      'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/', Rule::unique('users', 'phone_number')],
      'password' => ['required', 'confirmed', 'min:6'],
    ]);

    // Hash Password
    $formFields['password'] = bcrypt($formFields['password']);

    // Priviledge
    $formFields['priviledge'] = $request->priviledge;

    // Store User
    $user = User::create($formFields);
    auth()->login($user);

    return redirect('/')->with('success', 'You have joined us now!');
  }

  // LOGIN FORM
  public function login()
  {
    return view('Authentication.studentLogin');
  }

  // LOGIN PROCCESS
  public function studentAuthentication(Request $request)
  {
    $formFields = $request->validate([
      'name' => 'required|min:3|regex:/[a-zA-Z]+/',
      'email' => ['required', 'email'],
      'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/'],
      'password' => ['required', 'min:6'],
    ]);

    // Check if the user exists
    $user = User::where('email', $formFields['email'])->first();

    if (!$user) {
      return back()->withErrors(['email' => 'User does not exist'])->onlyInput('email');
    }

    // check if exists
    if (auth()->attempt($formFields)) {
      $request->session()->regenerate();

      return redirect('/')->with('success', 'You Are Now Logged in');
    }

    return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
  }

  // LOGIN ADMIN
  public function adminAuthentication(Request $request)
  {
    $formFields = $request->validate([
      'name' => 'required|min:3|regex:/[a-zA-Z]+/',
      'email' => ['required', 'email'],
      'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/'],
      'password' => ['required', 'min:6'],
    ]);

    // Check if exists
    if (auth()->attempt($formFields)) {
      $user = auth()->user();
      // Check if admin
      if ($user->priviledge === 'admin') {
        return redirect('/')->with('success', 'You Are Now Logged in');
      } else {
        // If not admin
        auth()->logout();
        return back()->withErrors(['email' => 'This email is not admin'])->onlyInput('email');
      }
    }

    // Authentication failed
    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
  }

  // LOGIN SUPERADMIN
  public function superadminAuthentication(Request $request)
  {
    $formFields = $request->validate([
      'name' => 'required|min:3|regex:/[a-zA-Z]+/',
      'email' => ['required', 'email'],
      'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/'],
      'password' => ['required', 'min:6'],
    ]);


    // Check if exists
    if (auth()->attempt($formFields)) {
      $user = auth()->user();
      // Check if admin
      if ($user->priviledge === 'superadmin') {
        return redirect('/')->with('success', 'You Are Now Logged in');
      } else {
        // If not admin
        auth()->logout();
        return back()->withErrors(['email' => 'This email is not superadmin'])->onlyInput('email');
      }
    }

    // Authentication failed
    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
  }

  // LOGOUT
  public function logout(Request $request)
  {
    auth()->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('success', 'You have been Logged out!');
  }

  // Show Add Admin Form
  public function showAddAdminFrom()
  {
    return view('Dashboard.addAdmin');
  }

  // Store Admin Data
  public function storeAdmin(Request $request)
  {
    $formFields = $request->validate([
      'name' => ['required', 'regex:/[a-zA-Z]+/', 'min:3'],
      'email' => ['required', 'email', Rule::unique('users', 'email')],
      'password' => ['required', 'min:6'],
      'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/', Rule::unique('users', 'phone_number')],
    ], [
      'name.regex' => 'Name must containe letters',
    ]);

    $formFields['priviledge'] = "admin";

    User::create($formFields);

    return back()->with('success', 'Admin added successfully.');
  }

  public function allCompetitions()
  {
    return view('Dashboard.competitions', [
      'competitions' => Competition::with('events')->latest()->filter(request(['search', 'category']))->get(),
    ]);
  }

  // Get user profile
  public function getProfile($id)
  {
    if (auth()->user()->id != $id) {
      return back()->with('error', 'This is not you and you have no permission to look at this profile.');
    } else {
      $user = User::findOrFail($id)->email;
      return view('Profile.profile',);
    }
  }

  // Get profile setting
  public function getSetting($id)
  {
    if (auth()->user()->id != $id) {
      return back()->with('error', 'This is not you and you have no permission to look at this profile.');
    }
    else {
      return view('Profile.editProfile');
    }
  }

  // Edit the profile
  public function editProfile(Request $request, $id)
  {
    // dd($request->file('photo'));
    if($request->name && $request->description){
      $formProfileFields = $request->validate([
        'name' => ['required', 'regex:/[a-zA-Z]+/', 'min:3', 'max:50'],
        'description' => ['nullable', new Between10And40Words],
      ]);
  
      // Get the specific user
      $user = User::findOrFail($id);
  
      // The current phone number
      $currentPhoneNumber = $request->input('phone_number');
  
      // Check if the current phone number is different from the previous one
      if ($user->phone_number != $currentPhoneNumber) {
        $formProfileFields = $request->validate([
          'name' => ['required', 'regex:/[a-zA-Z]+/', 'min:3', 'max:50'],
          'description' => ['nullable', new Between10And40Words],
          'phone_number' => ['required', 'regex:/^(010|011|012|015)\d{8}$/', new UniquePhoneNumber($id)],
        ]);
      }
  
      // Set the image
      if ($request->hasFile('photo')) {
        $formProfileFields['photo'] = $request->file('photo')->store('users', 'public');
      }
  
      // Update the user
      $user->update($formProfileFields);
  
      return back()->with('success', 'Profile updated successfully.');
    }else{
      $formProfileFields = $request->validate([]);
  
      // Get the specific user
      $user = User::findOrFail($id);
  
      // Set the image
      if ($request->hasFile('photo')) {
        $formProfileFields['photo'] = $request->file('photo')->store('users', 'public');
      }
  
      // Update the user
      $user->update($formProfileFields);
  
      return back()->with('success', 'Profile updated successfully.');
    }
  }
}
