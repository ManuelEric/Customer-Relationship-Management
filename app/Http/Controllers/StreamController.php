<?php

namespace App\Http\Controllers;

use App\Actions\Streams\CreateStreamAction;
use App\Actions\Streams\DeleteStreamAction;
use App\Actions\Streams\UpdateStreamAction;
use App\Enum\LogModule;
use App\Http\Requests\StoreStreamRequest;
use App\Http\Traits\LoggingTrait;
use App\Interfaces\StreamRepositoryInterface;
use App\Services\Log\LogService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class StreamController extends Controller
{
    use LoggingTrait;

    private StreamRepositoryInterface $streamRepository;

    public function __construct(StreamRepositoryInterface $streamRepository)
    {
        $this->streamRepository = $streamRepository;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) 
            return $this->streamRepository->rnGetDataTables();
        

        return view('pages.master.stream.index');
    }

    public function store(StoreStreamRequest $request, CreateStreamAction $createStreamAction, LogService $log_service)
    {
        $new_stream_details = $request->safe()->only([
            'stream_name',
        ]);

        DB::beginTransaction();
        try {

            $stream_created = $createStreamAction->execute($new_stream_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::STORE_STREAM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_stream_details);

            return Redirect::to('master/stream')->withError('Failed to create a new stream');
        }
        
        # store Success
        # create log success
        $log_service->createSuccessLog(LogModule::STORE_STREAM, 'New stream has been added', $stream_created->toArray());

        return Redirect::to('master/stream')->withSuccess('Stream successfully created');
    }

    public function show(Request $request, LogService $log_service)
    {
        $stream_id = $request->route('stream');

        try {
            # retrieve stream
            $stream = $this->streamRepository->rnGetStreamById($stream_id);
        } catch (Exception $e) {
            
            $log_service->createErrorLog(LogModule::SHOW_STREAM, $e->getMessage(), $e->getLine(), $e->getFile());
            return response()->json(['error' => $e->getMessage()], 500);
        }

        return response()->json(['stream' => $stream]);

    }

    public function update(StoreStreamRequest $request, UpdateStreamAction $updateStreamAction, LogService $log_service)
    {
        $new_stream_details = $request->safe()->only([
            'stream_name',
        ]);

        # retrieve vendor id from url
        $stream_id = $request->route('stream');

        DB::beginTransaction();
        try {

            $updated_stream = $updateStreamAction->execute($stream_id, $new_stream_details);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::UPDATE_STREAM, $e->getMessage(), $e->getLine(), $e->getFile(), $new_stream_details);

            return Redirect::to('master/stream')->withError('Failed to update a stream');
        }

        # Update success
        # create log success
        $log_service->createSuccessLog(LogModule::UPDATE_STREAM, 'New stream has been added', $updated_stream->toArray());

        return Redirect::to('master/stream')->withSuccess('Stream successfully updated');
    }

    public function destroy(Request $request, DeleteStreamAction $deleteStreamAction, LogService $log_service)
    {
        $stream_id = $request->route('stream');
        $stream = $this->streamRepository->rnGetStreamById($stream_id);

        DB::beginTransaction();
        try {

            $deleteStreamAction->execute($stream_id);
            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            $log_service->createErrorLog(LogModule::DELETE_STREAM, $e->getMessage(), $e->getLine(), $e->getFile(), $stream->toArray());

            return Redirect::to('master/stream')->withError('Failed to delete a stream');
        }

        # Delete success
        # create log success
        $log_service->createSuccessLog(LogModule::DELETE_STREAM, 'Stream has been deleted', $stream->toArray());

        return Redirect::to('master/stream')->withSuccess('Stream successfully deleted');
    }
}
