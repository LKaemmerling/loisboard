<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.17
 * Time: 14:14
 */

namespace LoisBoard\Models\Conversations;


use Illuminate\Database\Eloquent\Model;
use LoisBoard\Models\Accounts\Account;

/**
 * Class ConversationMessage
 * @package LoisBoard\Models\Conversations
 */
class ConversationMessage extends Model
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author(){
        return $this->belongsTo(Account::class,'user');
    }
}