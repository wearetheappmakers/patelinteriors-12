<?php

namespace App\Http\Controllers\EmployeeAuth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Hesto\MultiAuth\Traits\LogsoutGuard;
use Illuminate\Http\Request;
use App\Employee;
use App\Attendance;
use Carbon\Carbon;
use App\User;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, LogsoutGuard {
        LogsoutGuard::logout insteadof AuthenticatesUsers;
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('employee.guest', ['except' => 'logout']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        return view('employee.auth.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('employee');
    }

    public function login(Request $request)
    {

        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

        return $this->sendLockoutResponse($request);
    }

    $check = Employee::where('email',$request->email)->first();

    if(isset($check) &&  $check->status == 1 && $check->branch_id)
    {
        $branch = User::where('id',$check->branch_id)->first();
        if(!(isset($branch) && $branch->status == 1))
        {
            return $this->sendFailedLoginResponseStatus($request);
        }
    }else{
        return $this->sendFailedLoginResponseStatus($request);
    }
    
    if ($this->attemptLogin($request)) {

        $data_att = Attendance::where('employee_id',$check->id)->whereDate('created_at',Carbon::today())->first();

        $leave = Attendance::where('employee_id',$check->id)->whereDate('date',date('Y-m-d'))->first();
        if(!empty($leave)){
            return $this->sendLoginResponse($request);

        }else

        if(!empty($data_att)){
            return $this->sendLoginResponse($request);
        }
        else
        {

            $param['date'] = date('Y-m-d');
            $param['time'] = date('H:i:s');
            $param['employee_id'] = $check->id;
            $param['branch_id'] = $check->branch_id;
            $param['attendance'] = 'Not Approved ';

            // $store = Attendance::create($param);
        }

        return $this->sendLoginResponse($request);
    }

    $this->incrementLoginAttempts($request);

    return $this->sendFailedLoginResponse($request);
}

protected function sendFailedLoginResponseStatus(Request $request)
{
    throw ValidationException::withMessages([
        $this->username() => ['Employee is Inactive. Please contact admin.'],
    ]);
}

protected function sendLoginResponse(Request $request)
{
    $request->session()->regenerate();

    $this->clearLoginAttempts($request);

    return $this->authenticated($request, $this->guard()->user())
    ?: redirect()->intended($this->redirectPath());
}

public function logout(Request $request)
{
    $this->guard()->logout();

    $request->session()->invalidate();

    return $this->loggedOut($request) ?: redirect('/employee/login');
}
}
