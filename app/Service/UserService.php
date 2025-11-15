<?php

namespace ilhamrhmtkbr\App\Service;

use ilhamrhmtkbr\App\Config\Database;
use ilhamrhmtkbr\App\Exceptions\ValidationException;
use ilhamrhmtkbr\App\Facades\Request;
use ilhamrhmtkbr\App\Facades\Session;
use ilhamrhmtkbr\App\Facades\Validation;
use ilhamrhmtkbr\App\Helper\Components\ImageHelper;
use ilhamrhmtkbr\App\Models\User;
use ilhamrhmtkbr\App\Models\UserAdvancePersonal;
use ilhamrhmtkbr\App\Models\UserAdvanceSkills;
use ilhamrhmtkbr\App\Models\UserAdvanceSocial;
use ilhamrhmtkbr\App\Models\UserProfileEducation;
use ilhamrhmtkbr\App\Models\UserProfileExperience;
use ilhamrhmtkbr\App\Models\UserProfilePortfolio;
use ilhamrhmtkbr\App\Repository\UserRepository;

class UserService
{

    private UserRepository $userRepository;
    private Validation $validation;
    private User $user;
    private Session $session;

    public function __construct(UserRepository $userRepository, Session $session)
    {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->validation = new Validation();
        $this->user = new User();
    }

    public function register(Request $request): User
    {
        $this->validateUserRegistrationRequest($request);

        try {
            Database::beginTransaction();

            $this->user->email = $request->email;

            $result = $this->userRepository->findOne($this->user);

            if ($result != null) {
                throw new ValidationException(['badge' => 'User udah ada']);
            }

            $this->user->password = password_hash($request->password, PASSWORD_BCRYPT);

            $this->userRepository->save($this->user);

            Database::commitTransaction();

            return $this->user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserRegistrationRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'required|mustString|mustBeEmail'],
            'password' => [$request->password, 'required|mustString|mustPasswordCombination'],
            'passwordConfirm' => [$request->passwordConfirm, "required|mustString|mustPasswordCombination|mustPasswordConfirm:$request->password"]
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function login(Request $request): User
    {
        $this->validateLoginRequest($request);

        $this->user->email = $request->email;

        $result = $this->userRepository->findOne($this->user);

        if ($result == null) {
            throw new ValidationException(['badge' => 'Email atau Password salah']);
        }

        if (password_verify($request->password, $result->password)) {
            return $result;
        } else {
            throw new ValidationException(['badge' => 'Email atau Password salah']);
        }
    }

    private function validateLoginRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'required|mustString|mustBeEmail'],
            'password' => [$request->password, 'required|mustString|mustPasswordCombination'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function updateName(Request $request): User
    {
        $this->validateNameUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findOne($this->session->current());

            if ($user == null) {
                throw new ValidationException(['badge' => 'User is not found']);
            }

            $user->name = $request->name;
            $this->userRepository->updateName($user);

            Database::commitTransaction();

            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateNameUpdateRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'name' => [$request->name, 'required|mustString'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function updateEmail(Request $request): User
    {
        $this->validateEmailUpdateRequest($request);

        try {
            Database::beginTransaction();

            $user = $this->userRepository->findOne($this->session->current());

            $this->userRepository->updateEmail($user, $request->email);

            Database::commitTransaction();

            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateEmailUpdateRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'email' => [$request->email, 'required|mustString|mustBeEmail'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function updatePassword(Request $request): User
    {
        $this->validatePasswordUpdateRequest($request);

        try {
            Database::beginTransaction();

            $currentUser = $this->session->current();
            $user = $this->userRepository->findOne($currentUser);

            if (!password_verify($request->oldPassword, $user->password)) {
                throw new ValidationException(['badge' => 'Old password is wrong']);
            }

            $user->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->userRepository->updatePassword($user);

            Database::commitTransaction();

            return $user;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validatePasswordUpdateRequest(Request $request): void
    {
        $errors = $this->validation->make([
            'oldPassword' => [$request->oldPassword, 'required|mustString|mustPasswordCombination'],
            'newPassword' => [$request->newPassword, 'required|mustString|mustPasswordCombination'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function updateAdvancedPersonal(Request $request): UserAdvancePersonal
    {
        $result = $this->validateUpdateAdvanced($request);

        try {
            Database::beginTransaction();

            if ($result['check-condition'] == null) {
                $this->userRepository->saveAdvancedPersonal($result['advance-personal']);
            } else {
                $this->userRepository->updateAdvancedPersonal($result['advance-personal']);
            }

            Database::commitTransaction();

            return $result['advance-personal'];
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateAdvanced(Request $request): ?array
    {
        $errors = $this->validation->make([
            'phone' => [$request->phone, 'required|mustNumeric'],
            'headline' => [$request->headline, 'required|mustString'],
            'location' => [$request->location, 'required|mustString'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $currentUser = $this->session->current();

        $advancePersonal = new UserAdvancePersonal();
        $advancePersonal->user_id = $currentUser->email;

        $isThereAny = $this->userRepository->findOneAdvancedPersonal($advancePersonal);

        $advancePersonal->phone = $request->phone;
        $advancePersonal->headline = $request->headline;
        $advancePersonal->location =  $request->location;

        if ($isThereAny == null) {
            $errors = $this->validation->make([
                'image' => [$request->image, "isValidFileImage"],
            ]);

            if (!empty($errors)) {
                throw new ValidationException($errors);
            }

            $advancePersonal->image = ImageHelper::uploadCompressedImage($request->image, 'User');
        } else {
            if ($request->image['error'] == UPLOAD_ERR_NO_FILE) {
                $advancePersonal->image = $isThereAny->image;
            } else {
                $oldImage = $advancePersonal->image;
                $advancePersonal->image = ImageHelper::uploadCompressedImage($request->image, 'User', oldImagePath: $oldImage);
            }
        }

        $result = [
            'advance-personal' => $advancePersonal,
            'check-condition' => $isThereAny
        ];

        return $result;
    }

    public function updateOrCreateAdvanceSkill(Request $request, bool $isUpdate): ?UserAdvanceSkills
    {
        $this->validateUpdateSkill($request);

        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $advanceSkill = new UserAdvanceSkills();
            $advanceSkill->id = $request->id;
            $advanceSkill->user_id = $currentUser->email;
            $advanceSkill->name = $request->name;
            $advanceSkill->rating = $request->rating;
            $advanceSkill->description = $request->description;

            $isThereAny = $this->userRepository->isThereAnyAdvanceSkills($advanceSkill);

            if ($isUpdate) {
                $this->userRepository->updateAdvanceSkills($advanceSkill);
            } else {
                if ($isThereAny) {
                    $this->userRepository->saveAdvanceSkills($advanceSkill);
                } else {
                    throw new ValidationException(['badge' => 'Skill udah ada']);
                }
            }

            Database::commitTransaction();

            return $advanceSkill;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateSkill(Request $request): void
    {
        $errors = $this->validation->make([
            'name' => [$request->name, 'required|mustString'],
            'rating' => [$request->rating, 'required|mustEnum:1.2.3.4.5'],
            'description' => [$request->description, 'required|mustString'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteOneSkill(int $id): void
    {
        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $advanceSkill = new UserAdvanceSkills();
            $advanceSkill->id = $id;
            $advanceSkill->user_id = $currentUser->email;
            $this->userRepository->destroyOneAdvanceSkills($advanceSkill);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function updateOrCreateSocial(Request $request, bool $isUpdate): UserAdvanceSocial
    {
        $this->validateUpdateSocial($request);

        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $advanceSocial = new UserAdvanceSocial();
            $advanceSocial->id = $request->id;
            $advanceSocial->user_id = $currentUser->email;
            $advanceSocial->app_name = $request->app_name;
            $advanceSocial->url_link = $request->url_link;

            $isThereAny = $this->userRepository->isThereAnyAdvanceSocial($advanceSocial);

            if ($isUpdate) {
                $this->userRepository->updateAdvanceSocial($advanceSocial);
            } else {
                if ($isThereAny) {
                    $this->userRepository->saveAdvanceSocial($advanceSocial);
                } else {
                    throw new ValidationException(['badge' => 'Social media udah ada']);
                }
            }

            Database::commitTransaction();

            return $advanceSocial;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateSocial(Request $request): void
    {
        $errors = $this->validation->make([
            'app_name' => [$request->app_name, 'required|mustString'],
            'url_link' => [$request->url_link, 'required|mustString|mustBeSocialMediaLink'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteOneSocial(int $id): void
    {
        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $advanceSocial = new UserAdvanceSocial();
            $advanceSocial->id = $id;
            $advanceSocial->user_id = $currentUser->email;

            $this->userRepository->destroyOneAdvanceSocial($advanceSocial);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function updateOrCreateEducation(Request $request, bool $isUpdate): UserProfileEducation
    {
        $this->validateUpdateEducation($request);

        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $profileEducation = new UserProfileEducation();
            $profileEducation->id = $request->id;
            $profileEducation->user_id = $currentUser->email;
            $profileEducation->degree_id = $request->degree_id;
            $profileEducation->institution = $request->institution;
            $profileEducation->field = $request->field;
            $profileEducation->graduation_year = $request->graduation_year;

            $isThereAny = $this->userRepository->isThereAnyProfileEducation($profileEducation);

            if ($isUpdate) {
                $this->userRepository->updateProfileEducation($profileEducation);
            } else {
                if ($isThereAny != null) {
                    $this->userRepository->saveProfileEducation($profileEducation);
                } else {
                    throw new ValidationException(['badge' => 'Education udah ada']);
                }
            }

            Database::commitTransaction();

            return $profileEducation;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateEducation(Request $request): void
    {
        $graduationEnum = implode('.', range(1990, 2024));

        $options = [];
        foreach ($this->userRepository->findAllProfileEducationDegree() as $educationDegree) {
            $options[] = $educationDegree->id;
        }
        $optionsDegree = implode('.', $options);

        $errors = $this->validation->make([
            'institution' => [$request->institution, 'required|mustString'],
            'degree_id' => [$request->degree_id, "required|mustEnum:$optionsDegree"],
            'field' => [$request->field, 'required|mustString'],
            'graduation_year' => [$request->graduation_year, "required|mustEnum:$graduationEnum"],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteOneEducation(int $id): void
    {
        try {
            Database::beginTransaction();

            $currentUser = $this->session->current();

            $profileEducation = new UserProfileEducation();
            $profileEducation->id = $id;
            $profileEducation->user_id = $currentUser->email;
            $this->userRepository->destroyOneProfileEducation($profileEducation);

            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function updateOrCreateExperience(Request $request, bool $isUpdate): UserProfileExperience
    {
        $this->validateUpdateExperience($request);

        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $profileExperience = new UserProfileExperience();
            $profileExperience->id = $request->id;
            $profileExperience->user_id = $currentUser->email;
            $profileExperience->job_title = $request->job_title;
            $profileExperience->job_description = $request->job_description;
            $profileExperience->company_name = $request->company_name;
            $profileExperience->work_duration = $request->work_duration;

            $isThereAny = $this->userRepository->isThereAnyProfileExperience($profileExperience);

            if ($isUpdate) {
                $this->userRepository->updateProfileExperience($profileExperience);
            } else {
                if ($isThereAny != null) {
                    $this->userRepository->saveProfileExperience($profileExperience);
                } else {
                    throw new ValidationException(['badge' => 'Experience udah ada']);
                }
            }

            Database::commitTransaction();

            return $profileExperience;
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUpdateExperience(Request $request): void
    {
        $errors = $this->validation->make([
            'job_title' => [$request->job_title, 'required|mustString'],
            'job_description' => [$request->job_description, 'required|mustString'],
            'company_name' => [$request->company_name, 'required|mustString'],
            'work_duration' => [$request->work_duration, 'required|mustEnum:1.2.3.4.5.6.7.8.9.10'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    public function deleteOneExperience(int $id): void
    {
        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $profileExperience = new UserProfileExperience();
            $profileExperience->id = $id;
            $profileExperience->user_id = $currentUser->email;

            $this->userRepository->destroyOneProfileExperience($profileExperience);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function updateOrCreatePortfolio(Request $request, bool $isUpdate): UserProfilePortfolio
    {
        $result = $this->validateUpdatePortfolio($request);

        try {
            Database::beginTransaction();

            if ($isUpdate) {
                $this->userRepository->updateProfilePortfolio($result['portfolio']);
            } else {
                if (!$result['check-condition']) {
                    $this->userRepository->saveProfilePortfolio($result['portfolio']);
                } else {
                    throw new ValidationException(['badge' => 'Portfolio udah ada']);
                }
            }

            Database::commitTransaction();
            return $result['portfolio'];
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    public function validateUpdatePortfolio(Request $request): array
    {
        $errors = $this->validation->make([
            'title' => [$request->title, 'required|mustString'],
            'description' => [$request->description, 'required|mustString'],
            'link' => [$request->link, 'required|mustString'],
        ]);

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $currentUser = $this->session->current();

        $portfolio = new UserProfilePortfolio();
        $portfolio->id = $request->id;
        $portfolio->user_id = $currentUser->email;
        $portfolio->title = $request->title;
        $portfolio->description = $request->description;
        $portfolio->link = $request->link;

        $isThereAny = $this->userRepository->isThereAnyProfilePortfolio($portfolio);

        if (is_bool($isThereAny)) {
            $errors = $this->validation->make([
                'picture' => [$request->picture, "isValidFileImage"],
            ]);

            if (!empty($errors)) {
                throw new ValidationException($errors);
            }

            $portfolio->picture = ImageHelper::uploadCompressedImage($request->picture, 'User');
        } else {
            if ($request->picture['error'] == UPLOAD_ERR_NO_FILE) {
                $portfolio->picture = $isThereAny->picture;
            } else {
                $oldImage = $portfolio->picture;
                $portfolio->picture = ImageHelper::uploadCompressedImage($request->picture, 'User', oldImagePath: $oldImage);
            }
        }

        return [
            'portfolio' => $portfolio,
            'check-condition' => $isThereAny
        ];
    }

    public function deleteOnePortfolio(int $id, string $image): void
    {
        try {
            Database::beginTransaction();
            $currentUser = $this->session->current();

            $userPortfolio = new UserProfilePortfolio();
            $userPortfolio->id = $id;
            $userPortfolio->user_id = $currentUser->email;

            ImageHelper::delete($image);

            $this->userRepository->destroyOneProfilePortfolio($userPortfolio);
            Database::commitTransaction();
        } catch (\Exception $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }
}
