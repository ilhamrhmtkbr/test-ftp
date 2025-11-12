<?php

namespace ilhamrhmtkbr\App\Service;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Models\Candidates;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Redis\Session;
use ilhamrhmtkbr\App\Repository\CandidateRepository;
use ilhamrhmtkbr\App\Repository\UserRepository;

class CandidateService
{
    private CandidateRepository $candidateRepository;
    private UserRepository $userRepository;
    private Session $session;

    public function __construct(UserRepository $userRepository, Session $session)
    {
        $connection = Database::getConnection();
        $this->candidateRepository = new CandidateRepository($connection);
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    public function createJobApply(Request $request): void
    {
        $user = $this->session->current();

        $isCompleteProfile = true;
        $userProfileData = $this->userRepository->findUserLoginData($user);
        foreach ($userProfileData as $data) {
            if (empty($data)) {
                $isCompleteProfile = false;
            }
        }

        if ($isCompleteProfile) {
            $candidate = new Candidates();
            $candidate->user_id = $user->email;
            $candidate->job_id = $request->id;

            $isThereAnyJobApply = $this->candidateRepository->isThereAnyJobApplied($candidate);
            if (!$isThereAnyJobApply) {
                $this->candidateRepository->saveJobApply($candidate);
            } else {
                throw new ValidationException(['float' => 'Job udah di apply']);
            }
        } else {
            throw new ValidationException(['float' => 'Mohon lengkapi profile']);
        }
    }
}
