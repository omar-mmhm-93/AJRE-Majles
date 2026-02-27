@extends(backpack_view('blank'))

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4>LDAP Login</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ backpack_url('ldap') }}">
                @csrf

                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">
                    Import Users and Departments from LDAP
                </button>
            </form>
        </div>
    </div>

</div>
@endsection