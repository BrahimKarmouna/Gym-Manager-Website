<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Http\Resources\UserResource;
use App\Models\Account;
use App\Enums\AccountType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{

    public function index(){
       $account = Account::all();

       return UserResource::collection($account);

    }
    public function store(AccountRequest $request)
    {
        // Create a new account
        $account = Account::create([
            'name' => $request->name,
            'balance' => $request->balance,
            'account_type' => $request->account_type,
            'rib' => $request->rib,
            'user_id' => auth()->id()
        ]);

        return UserResource::make($account);
      }
}
