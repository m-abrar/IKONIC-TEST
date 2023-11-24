<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>EMINENT | Log in</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{asset('adminlte/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{asset('adminlte/dist/css/AdminLTE.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="{{asset('adminlte/plugins/iCheck/square/blue.css')}}" rel="stylesheet" type="text/css" />
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="login-page">

  <div class="login-box">
      <div class="login-logo">
        <a href="{{url('/')}}"><b>EMINENT</b> Admin</a>
      </div>
      <!-- /.login-logo -->
      <div class="login-box-body">

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">
          
          <p class="login-box-msg">Reset Password</p>
            <div class="panel-body">
              @if (session('status'))
              <div class="alert alert-success">
                {{ session('status') }}
              </div>
              @endif
              @if (count($errors) > 0)
              <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif
              <form class="form-horizontal" role="form" method="POST" action="{{ route('password.email') }}">
              @csrf
                <div class="form-group">
                  <label class="col-md-12">E-Mail Address</label>
                  <div class="col-md-12">
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                    Send Password Reset Link
                    </button>
                  </div>
                </div>
              </form>
            </div>
          
        </div>
      </div>
    </div>

    </div>
    </div>
  </body>
</html>
