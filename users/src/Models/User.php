<?php

namespace App;

use App\Notifications\MailResetPasswordToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function role()
	{
		return $this->belongsTo('App\Role');
    }


     /**
     * Set User data
     *
     * @param Request $request
     * @return void
     */
    public function setData(Request $request)
    {
        $this->name = $request->name ?? $this->name;
        $this->email = $request->email ?? $this->email;
        $this->password = $request->password ? bcrypt($request->password) : $this->password;
        $this->role_id = 1;
    }

    /**
     * Update password
     *
     * @param Request $request
     * @return void
     */
    public function updatePassword(Request $request) 
    {
        $this->password = $request->password ? bcrypt($request->password) : $this->password;
    }

    public function generateToken()
    {
        $this->api_token = str_random(60);
        $this->save();

        return $this->api_token;
    }

    /**
     * Route notifications for the SMS.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return $this->notify_phone_mobile;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        return $this->notify_slack_webhook_url;
    }

    /**
     * Route notifications for the Telegram channel.
     *
     * @return int
     */
    public function routeNotificationForTelegram()
    {
        return $this->notify_telegram_id;
    }

    /**
     * Send a password reset email to the user
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token));
    }
}
