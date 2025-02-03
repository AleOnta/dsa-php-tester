<?php

namespace Backend\Services;

use Backend\Models\Job;
use Backend\Models\UploadStatus;
use Backend\Repositories\JobRepository;
use RuntimeException;

class JobService
{
    private JobRepository $jobRepository;

    public function __construct(JobRepository $jobRepo)
    {
        $this->jobRepository = $jobRepo;
    }

    public function createFileUploadJob(string $file,)
    {
        # create an instance of the job
        $job = new Job(
            [
                'file' => $file,
                'status' => 'initialized',
                'progress' => 0,
                'message' => 'file received by the server',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        );
        # store the job in the db
        $jobId = $this->jobRepository->create($job);
        # check if the create has failed
        if (!$jobId) {
            throw new RuntimeException('Error while creating the file upload Job');
        }
        # return the entity id
        return $jobId;
    }

    public function updateJob(int $id, array $data)
    {
        # update the job
        $update = $this->jobRepository->update($id, $data);
        # check for update failure
        if (!$update) {
            throw new RuntimeException("Error while updating the job with id {$id}");
        }
        # return res
        return true;
    }

    public function updateJobStatus(int $id, string $status)
    {
        # check attempted status set
        if (!in_array($status, UploadStatus::getAvailable())) {
            throw new RuntimeException("Job status ({$status}) doesn't exists");
        }
        # update the job status
        $update = $this->jobRepository->update($id, ['status' => $status]);
        # check for update failure
        if (!$update) {
            throw new RuntimeException("Error while updating the job with id {$id}");
        }
        # return res
        return true;
    }

    public function updateJobProgress(int $id, int $progress)
    {
        # update the job status
        $update = $this->jobRepository->update($id, ['progress' => $progress]);
        # check for update failure
        if (!$update) {
            throw new RuntimeException("Error while updating the job with id {$id}");
        }
        # return res
        return true;
    }
}
