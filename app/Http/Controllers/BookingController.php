<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

      /**
     * Get All Jobs For Users and Admins Method
     */
    public function index(Request $request)
    {
        if($user_id = $request->get('user_id')) {
            return response()->json(['data' => $this->repository->getUsersJobs($user_id)], 200);
        }
        elseif ($request->has('__authenticatedUser')
        && in_array($request->__authenticatedUser->user_type, [
            config('utils.admin'),
            config('utils.super_admin')
        ])) {
            return response()->json(['data' => $this->repository->getAll($request)], 200);
        }
        // Return a response showing no data if neither of the conditions is true.
        return response()->json(['message' => 'You do not have access to this data!'], 400);
    }

      /**
     * Show Job Method
     */
    public function show($id)
    {
        // Use try-catch block to handle exceptions
        try {
            $job = $this->repository->with('translatorJobRel.user')->findOrFail($id);
            return response()->json(['data' => $job], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

      /**
     * Store Job Method
     */
    public function store(Request $request)
    {
        try{
        $response = $this->repository->store($request->__authenticatedUser, $request->all());
        return response()->json(['data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }

    }

     /**
     * Update Job Method
     */
    public function update($id, Request $request)
    {
        $requestData = $request->except(['_token', 'submit']);
        try {
            $response = $this->repository->updateJob($id, $requestData, $request->__authenticatedUser);
            return response()->json(['message' => 'Job updated successfully.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

      /**
     * Immediate Job Email Method
     */
    public function immediateJobEmail(Request $request)
    {
        try {
        $response = $this->repository->storeJobEmail($request->all());
          return response()->json(['message' => 'Job Email Stored successfully.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

      /**
     * Get Job History Method
     */
    public function getHistory(Request $request)
    {
        if (isset($request->user_id)) {
            try {
                $response = $this->repository->getUsersJobsHistory($request->get('user_id'), $request);
                return response()->json(['data' => $response], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred.'], 500);
            }
        }
        return response()->json([], 204);
    }

      /**
     * Accept Job Method
     */
    public function acceptJob(Request $request)
    {
        try{
            $response = $this->repository->acceptJob($request->all(), $request->__authenticatedUser);
            return response()->json(['message' => 'Job Accepted successfully.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }
  /**
     * This will accept Job With ID
     */
    public function acceptJobWithId(Request $request)
    {
        if(isset($request->job_id)){
            try{
                $response = $this->repository->acceptJobWithId($request->get('job_id'), $request->__authenticatedUser);
                return response()->json(['message' => 'Job Accepted successfully.', 'data' => $response], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred.'], 500);
            }
        }
        return response()->json(['error' => 'Job ID is required!.'], 400);
    }

     /**
     * Cancel Job Method
     */
    public function cancelJob(Request $request)
    {
            try{
                $response = $this->repository->cancelJobAjax($request->all(), $request->__authenticatedUser);
                return response()->json(['message' => 'Job Cancelled successfully.', 'data' => $response], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An error occurred.'], 500);
            }
    }

    /**
     * End Job Method
     */
    public function endJob(Request $request)
    {

        try{
            $response = $this->repository->endJob($request->all());
            return response()->json(['message' => 'Job Ended Successfully.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Customer Not Call Method
     */
    public function customerNotCall(Request $request)
    {
        try{
            $response = $this->repository->customerNotCall($request->all());
            return response()->json(['message' => 'Customer Not Called.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }

    }

    /**
     * Getting Potential Jobs
     */
    public function getPotentialJobs(Request $request)
    {
      
        try{
            $response = $this->repository->getPotentialJobs($request->__authenticatedUser);
            return response()->json(['message' => 'Potential Jobs.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }

    }

    /**
     * Distance Filter Method
     */
    public function distanceFeed(Request $request)
    {
        $data = $request->all();
    
        $jobid = $data['jobid'] ?? null;
        $distance = $data['distance'] ?? null;
        $time = $data['time'] ?? null;
        $session = $data['session_time'] ?? null;
        $flagged = $data['flagged'] == 'true' ? 'yes' : 'no';
        $manually_handled = $data['manually_handled'] == 'true' ? 'yes' : 'no';
        $by_admin = $data['by_admin'] == 'true' ? 'yes' : 'no';
        $admincomment = $data['admincomment'] ?? null;
    
        Distance::updateOrCreate(['job_id' => $jobid], ['distance' => $distance, 'time' => $time]);
    
        Job::where('id', $jobid)->update([
            'admin_comments' => $admincomment,
            'flagged' => $flagged,
            'session_time' => $session,
            'manually_handled' => $manually_handled,
            'by_admin' => $by_admin,
        ]);
    
        return response()->json(['message' => 'Record updated!', 'data' => []], 200);
    }

    /**
     * Reopen Method
     */

    public function reopen(Request $request)
    {
        try{
            $response = $this->repository->reopen($request->all());
            return response()->json(['message' => 'Reopen.', 'data' => $response], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred.'], 500);
        }
    }

    /**
     * Resent Notification
     */
    public function resendNotifications(Request $request)
{
    $data = $request->validate([
        'jobid' => 'required|numeric',
    ]);

    try {
        $job = $this->repository->findOrFail($data['jobid']);
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');
        return response()->json(['success' => 'Push sent'], 200);
    } catch (\Exception $e) {
        // Handle any other unexpected errors
        return response()->json(['error' => 'An error occurred.'], 500);
    }
}

    /**
     * Sends SMS to Translator
     */
    public function resendSMSNotifications(Request $request)
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response()->json(['success' => 'SMS sent'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
