@extends('Dashboard.layout')

@section('title')
    Add Admin
@endsection

@section('content')
    <div class="form">
        @if (session('success'))
            <div class="alert alert-success" id="message">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-success" id="message">
                {{ session('error') }}
            </div>
        @endif
        <h2>Add Admin</h2>
        <hr>
        <div class="fields">
            <form method="POST" action="/add/admin">
                @csrf
                @method('POST')
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" placeholder="Enter admin name" name="name"
                        value="{{ old('name') }}">
                    @error('name')
                        <p class="text-danger" style="font-size: 15px;text-align:start">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" class="form-control" placeholder="Enter admin email" name="email"
                        value="{{ old('email') }}">
                    @error('email')
                        <p class="text-danger" style="font-size: 15px;text-align:start">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" placeholder="Enter admin password" name="password"
                        value="{{ old('password') }}">
                    @error('password')
                        <p class="text-danger" style="font-size: 15px;text-align:start">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="form-control" placeholder="Enter admin phone number" name="phone_number"
                        value="{{ old('phone_number') }}">
                    @error('phone_number')
                        <p class="text-danger" style="font-size: 15px;text-align:start">{{ $message }}</p>
                    @enderror
                </div>

                <div class="d-flex justify-content-end p-3">
                    <button type="submit" class="sign-btn text-white"
                        style="background-color: #4FACFC; border-radius:10px;font-size:23px"><i class="bi bi-plus"></i> Add
                        Admin</button>
                </div>
            </form>
        </div>
    </div>
@endsection
