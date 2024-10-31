<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function store(AccountRequest $request)
    {
        $account = Account::create([
          'name' => $request->name,
          'account_type' => $request->account_type,
        ]);

        return AccountResource::make($account);
      }
}
