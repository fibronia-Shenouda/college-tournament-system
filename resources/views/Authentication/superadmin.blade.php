@extends('Authentication.layout')

@section('title')
    Superadmin Login
@endsection

@section('content')
<div class="form">
  <form action="/login/superadmin/authentication" method="POST">
    @csrf
    @method('POST')
    <div class="form-group">
      <label>Name</label>
      <input type="text" class="form-control" name="name" placeholder="Enter your name" value="{{old('name')}}">
      @error('name')
          <p class="text-danger" style="font-size: 13px">{{$message}}</p>
      @enderror
    </div>
    <div class="form-group">
      <label>Email address</label>
      <input type="text" class="form-control" name="email" placeholder="Enter your email" value="{{old('email')}}">
      @error('email')
          <p class="text-danger" style="font-size: 13px">{{$message}}</p>
      @enderror
    </div>

    <div class="form-group">
      <label>Phone Number</label>
      <input type="text" class="form-control" name="phone_number" placeholder="Enter your phone number" value="{{old('phone_number')}}">
      @error('phone_number')
          <p class="text-danger" style="font-size: 13px">{{$message}}</p>
      @enderror
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" class="form-control" name="password" placeholder="Password" value="{{old('password')}}">
      @error('password')
          <p class="text-danger" style="font-size: 13px">{{$message}}</p>
      @enderror
    </div>
    <div class="btn-container" style="justify-content: end">
      <button type="submit" class="form-btn"><i class="bi bi-box-arrow-in-right"></i>  Sign In</button>
    </div>
  </form>
</div>
@endsection