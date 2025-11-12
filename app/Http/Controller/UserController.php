<?php

namespace ilhamrhmtkbr\App\Http\Controller;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Facades\View;
use ilhamrhmtkbr\App\Helper\Components\BadgeWithCloseHelper;
use ilhamrhmtkbr\App\Helper\UrlHelper;
use ilhamrhmtkbr\App\Models\UserAdvancePersonal;
use ilhamrhmtkbr\App\Redis\Session;
use ilhamrhmtkbr\App\Repository\UserRepository;
use ilhamrhmtkbr\App\Service\UserService;

class UserController
{
    private UserService $userService;
    private UserRepository $userRepository;
    private Session $session;

    public function __construct()
    {
        $connection = Database::getConnection();
        $this->userRepository = new UserRepository($connection);
        $this->session = new Session();
        $this->userService = new UserService($this->userRepository, $this->session);
    }

    public function viewRegister(): void
    {
        View::render('Auth/Register', pageTitle: 'Register', isNeedSessionFlash: true);
    }

    public function postRegister(Request $request): void
    {
        try {
            $this->userService->register($request);
            View::redirect('/user/login');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-register');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/register');
        }
    }

    public function viewLogin(): void
    {
        View::render('Auth/Login', pageTitle: 'Login', isNeedSessionFlash: true);
    }

    public function postLogin(Request $request): void
    {
        try {
            $response = $this->userService->login($request);
            $this->session->create($response->email);
            View::redirect('/user/dashboard');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-login');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/login');
        }
    }

    public function doLogout(): void
    {
        $this->session->destroy();
        View::redirect('/user/login');
    }

    public function viewDashboard(): void
    {
        $user = $this->session->current();
        View::render('User/Dashboard', $user, 'Dashboard', data: $this->userRepository->findUserLoginData($user));
    }

    public function viewPersonal(): void
    {
        $user = $this->session->current();
        $advancePersonal = new UserAdvancePersonal();
        $advancePersonal->user_id = $user->email;

        View::render(
            'User/Personal',
            $user,
            'Personal',
            true,
            $this->userRepository->findOneAdvancedPersonal($advancePersonal)
        );
    }

    public function viewSkill(): void
    {
        $user = $this->session->current();

        View::render(
            'User/Skill',
            $user,
            'Skill',
            true,
            $this->userRepository->findAllAdvanceSkills($user->email, UrlHelper::getParamData())
        );
    }

    public function viewSocial(): void
    {
        $user = $this->session->current();

        View::render(
            'User/Social',
            $user,
            'Social',
            true,
            $this->userRepository->findAllAdvanceSocial($user->email, UrlHelper::getParamData())
        );
    }

    public function viewEducation(): void
    {
        $user = $this->session->current();

        $data = [
            'education' => $this->userRepository->findAllProfileEducation($user->email, UrlHelper::getParamData()),
            'education-degree' => $this->userRepository->findAllProfileEducationDegree()
        ];

        View::render(
            'User/Education',
            $user,
            'Education',
            true,
            $data
        );
    }

    public function viewExperience(): void
    {
        $user = $this->session->current();
        View::render(
            'User/Experience',
            $user,
            'Experience',
            true,
            $this->userRepository->findAllProfileExperience($user->email, UrlHelper::getParamData())
        );
    }

    public function viewPortfolio(): void
    {
        $user = $this->session->current();

        View::render(
            'User/Portfolio',
            $user,
            'Portfolio',
            true,
            $this->userRepository->findAllProfilePortfolio($user->email, UrlHelper::getParamData())
        );
    }

    public function postUpdateName(Request $request): void
    {
        try {
            $this->userService->updateName($request);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update name', 'success-update-name');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-name');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-name');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-name');
        }
    }

    public function postUpdateEmail(Request $request): void
    {
        try {
            $this->userService->updateEmail($request);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update email', 'success-update-email');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-email');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-email');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-email');
        }
    }

    public function postUpdatePassword(Request $request): void
    {
        try {
            $this->userService->updatePassword($request);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update password', 'success-update-password');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-password');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-password');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-password');
        }
    }

    public function postAdvancePersonal(Request $request): void
    {
        try {
            $this->userService->updateAdvancedPersonal($request);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update advance personal', 'success-update-advance-personal');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-advance-personal');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-advance-personal');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/personal#update-advance-personal');
        }
    }

    public function postAdvanceSkill(Request $request): void
    {
        try {
            $this->userService->updateOrCreateAdvanceSkill($request, $_POST['id'] != null);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update advance skill', 'success-update-advance-skill');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/skill');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-advance-skill');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/skill#form-update');
        }
    }

    public function deleteAdvanceSkill(Request $request): void
    {
        try {
            $this->userService->deleteOneSkill($request->id);
            View::redirect('/user/advance/skill');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-delete-advance-skill');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/skill');
        }
    }

    public function postAdvanceSocial(Request $request): void
    {
        try {
            $this->userService->updateOrCreateSocial($request, $_POST['id'] != null);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update advance social', 'success-update-advance-social');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/social');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-advance-social');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/social#form-update');
        }
    }

    public function deleteAdvanceSocial(Request $request): void
    {
        try {
            $this->userService->deleteOneSocial($request->id);
            View::redirect('/user/advance/social');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-delete-advance-social');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/advance/social');
        }
    }

    public function postProfileEducation(Request $request): void
    {
        try {
            $this->userService->updateOrCreateEducation($request, $_POST['id'] != null);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update profile education', 'success-update-profile-education');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/education');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-profile-education');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/education#form-update');
        }
    }

    public function deleteProfileEducation(Request $request): void
    {
        try {
            $this->userService->deleteOneEducation($request->id);
            View::redirect('/user/profile/education');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-delete-profile-education');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/education');
        }
    }

    public function postProfileExperience(Request $request): void
    {
        try {
            $this->userService->updateOrCreateExperience($request, $_POST['id'] != null);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update profile experience', 'success-update-profile-experience');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/experience');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-profile-experience');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/experience#form-update');
        }
    }

    public function deleteProfileExperience(Request $request): void
    {
        try {
            $this->userService->deleteOneExperience($request->id);
            View::redirect('/user/profile/experience');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-delete-profile-experience');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/experience');
        }
    }

    public function postProfilePortfolio(Request $request): void
    {
        try {
            $this->userService->updateOrCreatePortfolio($request, $_POST['id'] != null);
            $successData = BadgeWithCloseHelper::setBadgeData('success', 'Success update profile portfolio', 'success-update-profile-portfolio');
            $sessionFlash = ['badge' => $successData];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/portfolio');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-update-profile-portfolio');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/portfolio#form-update');
        }
    }

    public function deleteProfilePortfolio(Request $request): void
    {
        try {
            $this->userService->deleteOnePortfolio($request->id, $request->image);
            View::redirect('/user/profile/portfolio');
        } catch (ValidationException $exception) {
            $errorData = BadgeWithCloseHelper::setBadgeData('danger', $exception->getErrors()['badge'] ?? null, 'error-delete-profile-portfolio');
            $sessionFlash = ['badge' => $errorData, 'errors' => $exception->getErrors()];
            View::withSessionFlash($sessionFlash)->redirect('/user/profile/portfolio');
        }
    }
}
