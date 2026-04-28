<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>DataCollector | Login</title>
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="#" class="h1"><b>Data</b>Collector</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible p-2">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
      <form action="{{ route('login') }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-envelope"></span></div></div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>
      <div class="social-auth-links text-center mt-2 mb-3">
        <p class="mb-1">
        <a href="{{ route('password.request') }}">I forgot my password</a>
        </p>
        <p class="mb-0">
        <a href="{{ route('register.page') }}" class="text-center">Register a new membership</a>
        </p>
    </div>
    </div>
  </div>
</div>
</body>
</html>