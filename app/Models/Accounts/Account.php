<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.17
 * Time: 14:06
 */

namespace LoisBoard\Models\Accounts;


use Illuminate\Database\Eloquent\Model;
use LoisBoard\Models\Conversations\Conversation;

/**
 * Class Account
 * @package LoisBoard\Models\Accounts
 */
class Account extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users', 'user', 'conversation');
    }
}