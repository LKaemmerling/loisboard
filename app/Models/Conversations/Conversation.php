<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.17
 * Time: 14:07
 */

namespace LoisBoard\Models\Conversations;


use Illuminate\Database\Eloquent\Model;
use LoisBoard\Models\Accounts\Account;

/**
 * Class Conversation
 * @package LoisBoard\Models\Conversations
 */
class Conversation extends Model
{

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(Account::class, 'conversation_users', 'user');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(){
        return $this->hasMany(ConversationMessage::class,'conversation');
    }
}