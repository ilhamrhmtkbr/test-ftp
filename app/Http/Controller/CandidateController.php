<?php

namespace ilhamrhmtkbr\App\Http\Controller;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Facades\Session;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\Components\AlertWithCloseHelper;
use ilhamrhmtkbr\App\Repository\CandidateRepository;
use ilhamrhmtkbr\App\Repository\UserRepository;
use ilhamrhmtkbr\App\Service\CandidateService;

class CandidateController
{
    private CandidateService $candidateService;
    private CandidateRepository $candidateRepository;
    private Session $session;

    public function __construct()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->candidateRepository = new CandidateRepository($connection);
        $this->session = new Session();
        $this->candidateService = new CandidateService($userRepository, $this->session);
    }

    public function viewJobs(): void
    {
        $page = $_GET['page'] ?? 1;
        $orderBy = $_GET['orderBy'] ?? 'ASC';
        $keyword = $_GET['keyword'] ?? '';

        View::render(
            'Candidate/Jobs',
            $this->session->current(),
            'Jobs',
            data: $this->candidateRepository->findAllJobs($page, $orderBy, $keyword)
        );
    }

    public function viewJob(): void
    {
        if (!isset($_GET['id'])) {
            View::redirect('/candidate/jobs');
        }

        View::render(
            'Candidate/Job',
            $this->session->current(),
            'Job',
            true,
            data: $this->candidateRepository->findOneJob($_GET['id'])
        );
    }

    public function postJob(Request $request): void
    {
        try {
            $this->candidateService->createJobApply($request);
            $successData = AlertWithCloseHelper::setAlertData('success', 'Success apply job', 'success-apply-job');
            $sessionFlash = ['float' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/candidate/applied');
        } catch (ValidationException $exception) {
            $errorData = AlertWithCloseHelper::setAlertData('danger', $exception->getErrors()['float'] ?? null, 'error-apply-job');
            $sessionFlash = ['float' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/candidate/job?id=' . $_POST['id']);
        }
    }

    public function viewApplied(): void
    {
        $user = $this->session->current();
        $page = $_GET['page'] ?? 1;
        $orderBy = $_GET['orderBy'] ?? 'ASC';
        $keyword = $_GET['keyword'] ?? '';

        View::render(
            'Candidate/Applied',
            $user,
            'Applied',
            data: $this->candidateRepository->findAllJobsWasApplied($user->email, $page, $orderBy, $keyword)
        );
    }
}
