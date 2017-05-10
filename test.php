<?php
/**
 * Created by PhpStorm.
 * User: lukaskammerling
 * Date: 10.05.17
 * Time: 14:19
 */

require_once __DIR__.'/app/bootstrap.php';
use LoisBoard\Models\Accounts\Account;
$all_accounts = Account::all();
dump($all_accounts);
echo "<h3>Konversationen</h3>";
$all_accounts->map(function(Account $account) {
  echo "Account: ".$account->username."<br /> Anzahl Konversationen:".$account->conversations->count()."<br /> SQL: ".$account->conversations()->toSql()."<hr>";
});