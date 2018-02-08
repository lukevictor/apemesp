<?php

namespace Apemesp\Http\Controllers\Auth;

use Apemesp\Apemesp\Models\User;
use Validator;
use View;
use Apemesp\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Apemesp\Apemesp\Repositories\Apemesp\UserRepository;
use Auth;
use Session;
use Input;
use Mail;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    
    protected $redirectTo = '/admin';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
         View::composers([
            'Apemesp\Composers\MaisVistosComposer'  => ['partials._sidebar'] 
        ]);
         View::composers([
            'Apemesp\Composers\PropagandasComposer'  => ['partials._sidebar'] 
        ]);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function postRegister(Request $request)
    {
        $user = new UserRepository;
        $result = $user->create($request);
        if ($result) {
            Session::flash('sucesso', 'Seus dados foram salvos com sucesso. Por favor verifique o seu e-mail.');
            $confirmCode = $this->generateCode();
            $user->storeCode($result, $confirmCode);
            $this->sendEmailReminder($result, $confirmCode);
        } else {
            Session::flash('cuidado', 'O e-mail informado já foi cadastrado ou é inválido, por favor tente novamente ou entre em contato');
        }
        return redirect()->back();
    }

    public function getLogin(){
        
        return ("man");
    }

    public function generateCode()
    {
        return  uniqid(rand(), true);
    }

      /**
     * Handle an authentication attempt.
     *
     * @return Response
     */
    public function authenticate()
    {
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            // Authentication passed...
            return redirect()->intended('dashboard');
        }
    }

      /**
     * Send an e-mail reminder to the user.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function sendEmailReminder($id, $confirmCode)
    {
        $user = User::findOrFail($id);

        Mail::send('emails.reminder', ['confirmCode' => $confirmCode], function ($m) use ($user) {
            $m->from('site.apemesp@gmail.com', 'APEMESP');

            $m->to($user->email, $user->name)->subject('Validação de cadastro!');
        });
    }

    public function confirm($code)
    {
        $aud = new UserRepository;
        $id = $aud->findCode($code);
        if (!empty($id)) {
            $aud->update($id);
        }
        return ($code);
    }

  
}
