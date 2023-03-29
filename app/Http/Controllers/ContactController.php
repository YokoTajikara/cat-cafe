<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;
use App\Mail\ContactAdminMail;

class ContactController extends Controller
{
    public function index()
	{
		return view('contact.index');
	}

	public function sendMail(ContactRequest $request) {
		$validated = $request->validated();
		
		// これ以降の行は入力エラーがなかった場合のみ実行されます
		// 登録処理(実際はメール送信などを行う)
		//Log::debug($validated['name']. 'さんよりお問い合わせがありました');
		Mail::to('blu.iroizariuo62@gmail.com')->send(new ContactAdminMail($validated));
		return to_route('contact.complete');
	}

	public function complete()
	{
		return view('contact.complete');
	}
}
