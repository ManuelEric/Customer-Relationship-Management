<?php

namespace App\Http\Controllers;

use App\Interfaces\ClientRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RecycleClientController extends Controller
{
    protected ClientRepositoryInterface $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }
    
    public function index(Request $request)
    {
        $target = $request->route('target');
        switch ($target) {

            case "students":
                $model = $this->clientRepository->getDeletedStudents(true);
                $view = 'pages.recycle.client.student';
                break;


            case "parents":
                $model = $this->clientRepository->getDeletedParents(true);
                $view = 'pages.recycle.client.parent';
                break;

            
            case "teacher-counselor":
                $model = $this->clientRepository->getDeletedTeachers(true);
                $view = 'pages.recycle.client.teacher';
                break;

        }

        if ($request->ajax()) 
            return $this->clientRepository->getDataTables($model);

        return view($view);
    }


    public function restore(Request $request)
    {
        $target = $request->route('target'); # not used
        $clientId = $request->route('client');

        if (!$this->clientRepository->findDeletedClientById($clientId))
            abort(404);

        DB::beginTransaction();
        try {

            $this->clientRepository->restoreClient($clientId);
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollBack();
            Log::error('Failed to restore '.$target.' : ' . $e->getMessage().' on line '.$e->getLine());
            return Redirect::back()->withError('Failed to restore client');

        }

        return Redirect::to('recycle/client/'.$target)->withSuccess('Client has been restored');
    }   
}
