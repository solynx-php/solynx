@layout('auth')
@block('title')
Registration page
@endblock

@block('content')
<form method="post">
    <div class="mb-3">
        <label for="exampleInputUsername" class="form-label">Username</label>
        <input type="text" class="form-control {{$user->hasError('username') ? ' is-invalid' : ''}}" name="username" value="{{ $user->username ?? '' }}">
        <div class="invalid-feedback">
            {{ $user->getError('username') }}
        </div>
    </div>

    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email address</label>
        <input type="email" class="form-control {{$user->hasError('email') ? ' is-invalid' : ''}}" id="exampleInputEmail1" name="email" value="{{ $user->email ?? '' }}">
        <div class="invalid-feedback">
            {{ $user->getError('email') }}
        </div>
    </div>

    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" class="form-control {{$user->hasError('password') ? ' is-invalid' : ''}}" id="exampleInputPassword1" name="password">
        <div class="invalid-feedback">
            {{ $user->getError('password') }}
        </div>
    </div>

    <div class="mb-3">
        <label for="exampleInputConfirmPassword1" class="form-label">Confirm Password</label>
        <input type="password" class="form-control {{$user->hasError('confirmpassword') ? ' is-invalid' : ''}}" id="exampleInputConfirmPassword1" name="confirmpassword">
        <div class="invalid-feedback">
            {{ $user->getError('confirmpassword') }}
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>
@endblock