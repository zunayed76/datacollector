<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>DataCollector | Verify OTP</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
  
  <style>
    /* Professional look for the OTP digits */
    .otp-input {
      letter-spacing: 12px;
      font-size: 24px;
      font-weight: bold;
      text-indent: 12px;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-success">
    <div class="card-header text-center">
      <a href="/" class="h1"><b>Data</b>Collector</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Account Activation</p>
      <p class="text-muted text-center small">Enter the 6-digit code sent to<br><b>{{ request('email') }}</b></p>
      {{-- Success/Error Alerts --}}
      @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
          </div>
      @endif
      @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
          </div>
      @endif

      <form action="{{ route('verify.otp') }}" method="post">
        @csrf
        <input type="hidden" name="email" value="{{ request('email') }}">

        <div class="input-group mb-3">
          <input type="text" 
                 name="otp" 
                 class="form-control text-center otp-input @error('otp') is-invalid @enderror" 
                 placeholder="000000" 
                 maxlength="6" 
                 required 
                 autofocus 
                 autocomplete="one-time-code">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-shield-alt"></span>
            </div>
          </div>
          @error('otp')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
          @enderror
        </div>

        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-success btn-block">
              <i class="fas fa-check-circle mr-2"></i> Activate Account
            </button>
          </div>
        </div>
      </form>
      <div class="mt-3 text-center">
        <p class="mb-1">Didn't receive a code?</p>
        <form action="{{ route('otp.resend') }}" method="post" id="resendForm">
            @csrf
            <input type="hidden" name="email" value="{{ request('email') }}">
            <button type="submit" id="resendBtn" class="btn btn-link p-0">Resend OTP</button>
        </form>
    </div>

      <div class="mt-3 text-center">
        <p class="mb-1">
          <a href="{{ route('login') }}">Back to Login</a>
        </p>
      </div>
    </div>
    </div>
  </div>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
<script>
    // Prevent spamming the resend button
    let seconds = 120;
    const btn = document.getElementById('resendBtn');
    const form = document.getElementById('resendForm');

    function startTimer() {
        btn.disabled = true;
        let timer = setInterval(() => {
            seconds--;
            btn.innerText = `Resend available in ${seconds}s`;
            if (seconds <= 0) {
                clearInterval(timer);
                btn.innerText = "Resend OTP";
                btn.disabled = false;
                seconds = 120;
            }
        }, 1000);
    }

    // Start timer if the page just reloaded with a success message
    @if(session('success'))
        startTimer();
    @endif
</script>
</body>
</html>