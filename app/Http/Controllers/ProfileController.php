<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProfileRequest;
use App\Interfaces\UserRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{

    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $logged_in_user = Auth::user();

        return view('pages.profile.index')->with(
            [
                'my_info' => $logged_in_user
            ]
        );
    }

    public function update(StoreProfileRequest $request)
    {
        $myUserId = Auth::user()->id;
        $changePassword = $request->input('form:password') ?? false;
        
        DB::beginTransaction();
        try {

            switch ($changePassword) {
    
                case true:
                    $newPassword = Hash::make($request->password);
                    $this->userRepository->updateUser($myUserId, ['password' => $newPassword ]);
                    break;
    
                case false:
    
                    break;
    
            }
            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();
            Log::error("Failed to change password : " . $e->getMessage().' on Line '.$e->getLine());
            return Redirect::back()->withError('Failed to change password. Please try again or contact your administrator');

        }

        return Redirect::to('profile')->withSuccess('Password changed successfully');
    }
}
