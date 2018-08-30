<?php

namespace App;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Events\PasswordRequested;
use App\Events\UserRegistered;
use App\Events\UserSignedIn;
use App\Events\UserVerified;
use App\Exceptions\AccountException;
use App\Exceptions\ApplicationException;
use App\Http\Filters\UserFilter;
use App\Mail\UserVerificationMail;
use App\Models\Account\Profile;
use App\Models\Account\VerificationToken;
use App\Models\Activity\UserProgram;
use App\Models\Measurement\Field;
use App\Models\Measurement\UserField;
use App\Models\Program\Program;
use App\Traits\Verifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, Verifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'type', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function register($data=array()) {
        $default_data = [
            'password' => Hash::make($data['password']),
            'type' => UserType::FREE,
            'status' => UserStatus::INITIAL,
        ];
        $user = parent::create(array_merge($data, $default_data));
        $user->generateToken('account.verify');

        $user->profile()->save(new Profile($data));

        try{
            event(new UserRegistered($user));
        }catch(ApplicationException $e){
            report($e);
        }

        return $user;
    }

    public function login() {
        return $this->loginAs(UserType::CLIENT);
    }

    public function isA(string $userType) {
        return in_array($this->type, explode('|', $userType));
    }

    public function isMyself(User $user) {
        return $this->id == $user->id && $this->type == $user->type;
    }

    public function loginAs(string $userType=UserType::CLIENT) {
        if($this->status != UserStatus::ACTIVE) {
            $e1 = new AccountException('account.user.invalid_status');
            report($e1);
            throw $e1;
        }

        if(!$this->isA($userType)){
            $e2 = new AccountException('account.user.invalid_user_type');
            report($e2);
            throw $e2;
        }

        $this->accessToken = $this->createToken(Config::get('app.name'))->accessToken;
        try{
            event(new UserSignedIn($this));
        }catch(ApplicationException $e){
            report($e);
        }

        return ['token'=>$this->accessToken, 'id'=>$this->id];
    }

    public function verify($token) {
        if(!$this->verifying($token, 'account.verify')){
            throw new AccountException('account.user.invalid_token');
        }
        $this->status = UserStatus::ACTIVE;
        $saved = $this->save();

        if($saved){
            $this->clearTokens('account.verify');
            try{
                event(new UserVerified($this));
            }catch(ApplicationException $e){
                report($e);
            }
        }

        return $saved;
    }

    public function resend($action) {
        $token = VerificationToken::where([
            ['email', $this->email],
            ['action', $action]
        ])->latest();
        if(!$token) {
            throw new AccountException('account.user.invalid_token');
        }
        $this->verifyToken = $token;
        Mail::to($this)->send(new UserVerificationMail($this));
    }

    public function forgot(){
        $this->clearTokens();
        $this->generateToken();
        try{
            event(new PasswordRequested($this));
        }catch(ApplicationException $e){
            report($e);
        }
    }

    public function reset($token, $new_password) {
        if(!$this->verifying($token)){
            throw new AccountException('account.user.invalid_token');
        }

        $encripted_password = Hash::make($new_password);
        $this->password = $encripted_password;
        $this->save();
        $this->clearTokens();
    }

    public function scopeByEmail($query, $email) {
        return $query->where('email', $email);
    }

    public function isAdmin() {
        return $this->isA(UserType::ADMIN);
    }

    public function fields() {
        return $this->belongsToMany(Field::class, 'user_fields')->using(UserField::class);
    }

    public function programs() {
        return $this->hasMany(UserProgram::class);
    }

    public function profile() {
        return $this->hasOne(Profile::class);
    }

    public static function search(UserFilter $filter) {
        $query = User::with('profile');
        $query->when(!empty($filter->keyword()), function ($query) use ($filter) {
            $query->where('email', 'LIKE', '%'.$filter->keyword().'%');
        });
        $query->when(count($filter->getTypes())>0, function ($query) use ($filter) {
            $query->whereIn('type', $filter->getTypes());
        });
        $query->when(count($filter->getStatuses())>0, function ($query) use ($filter) {
            $query->whereIn('status', $filter->getStatuses());
        });
        // get total number before adding pagination
        $total = $query->count();
        if($filter->limit()>0){
            $query->skip($filter->offset())->take($filter->limit());
        }
        foreach($filter->order() as $order) {
            list($orderField, $orderBy) = $order;
            $query->orderBy($orderField, $orderBy);
        }
//        dd(DB::getQueryLog());
        return array('total'=> $total, 'items' => $query->get());
    }

}
