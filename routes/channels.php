<?php

use App\Helpers\Helper;
use App\Models\Account;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('private_user_{userid}',fn(Account $user,$userId)=>true);
Broadcast::channel(Helper::getStudentChannel("{student_id}")
,fn($student_id)=>['hello']
);
Broadcast::channel(Helper::getEmployeeChannel("{employee_id}")
,fn($student_id)=>true
);

