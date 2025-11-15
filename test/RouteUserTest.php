<?php

namespace ilhamrhmtkbr\Test {
    require_once __DIR__ . '/helper/http.php'; // untuk mengatas phpunit error : Cannot modify header information - headers already ...
    require_once __DIR__ . '/helper/folder.php'; // untuk mengatas phpunit error : Cannot modify header information - headers already ...
    require_once __DIR__ . '/../config/test.php';

    use GuzzleHttp\Client;
    use ilhamrhmtkbr\App\Config\Database;
    use ilhamrhmtkbr\App\Facades\Session;
    use ilhamrhmtkbr\App\Helper\FormSessionHelper;
    use ilhamrhmtkbr\App\Models\User;
    use ilhamrhmtkbr\App\Models\UserAdvancePersonal;
    use ilhamrhmtkbr\App\Models\UserAdvanceSkills;
    use ilhamrhmtkbr\App\Models\UserAdvanceSocial;
    use ilhamrhmtkbr\App\Models\UserProfileEducation;
    use ilhamrhmtkbr\App\Models\UserProfileExperience;
    use ilhamrhmtkbr\App\Repository\UserRepository;
    use ilhamrhmtkbr\Test\refactory\CustomClientCookies;
    use PHPUnit\Framework\TestCase;

    class RouteUserTest extends TestCase
    {
        private Client $clientWithNoCookies;
        private Client $clientWithCookieAuthMiddleware;
        private Client $clientWithCookieFormSession;
        private Client $clientWithCookieAuthMiddlewareAndFormSession;
        private static User $user;
        private static UserRepository $userRepository;
        private static Session $session;
        private static string $password;

        public static function setUpBeforeClass(): void
        {
            self::initializeUser();
            self::$session = new Session();
        }

        public static function tearDownAfterClass(): void
        {
            self::$userRepository->deleteOne(self::$user);
        }

        public function setUp(): void
        {
            $session = uniqid();
            FormSessionHelper::$FILENAME = $session;

            if ($this->isNeedCookieAuthMiddlewareAndFormSession()) {
                $this->clientWithCookieAuthMiddlewareAndFormSession = CustomClientCookies::createClientWithCookieAuthMiddlewareAndFormSession(self::$user->email, $session);
            }

            if ($this->isNeedCookieFormSession()) {
                $this->clientWithCookieFormSession = CustomClientCookies::createClientWithCookieFormSession($session);
            }

            if ($this->isNeedSessionServiceCreate()) {
                self::$session->create(self::$user->email);
            }

            if ($this->isNeedCookieAuthMiddleware()) {
                $this->clientWithCookieAuthMiddleware = CustomClientCookies::createClientWithCookieAuthMiddleware(self::$user->email);
            }

            if ($this->isNoNeedCookie()) {
                $this->clientWithNoCookies = new Client(['base_uri' => getTestConfig('base_uri')]);
            }
        }

        public function tearDown(): void
        {
            if ($this->isNeedSessionServiceDestroy()) {
                self::$session?->destroy();
//                if (self::self::$session != null) {
//                    self::self::$session->destroy();
//                }
            }
        }

        private static function initializeUser(): void
        {
            self::$user = new User();
            self::$user->email = 'ilham25@gmail.com';
            self::$password = 'Ilham123!';
            self::$user->password = password_hash(self::$password, PASSWORD_BCRYPT);

            $connection = Database::getConnection();
            self::$userRepository = new UserRepository($connection);
            self::$userRepository->save(self::$user);
        }

        private function isNoNeedCookie(): bool
        {
            return in_array($this->getName(), [
                'test_user_view_register_success',
                'test_user_register_success',
                'test_user_view_login_success',
            ]);
        }

        private function isNeedCookieAuthMiddleware(): bool
        {
            return in_array($this->getName(), [
                'test_user_view_register_failed_because_auth_middleware',
                'test_user_view_login_failed_because_auth_middleware',
                'test_user_login_success',
                'test_user_logout',
                'test_user_view_dashboard',
                'test_user_edit_name_success',
                'test_user_edit_email_success',
                'test_user_edit_password_success',
                'test_user_view_advance_personal',
                'test_user_view_advance_skill',
                'test_user_advance_add_skill_success',
                'test_user_advance_edit_skill_success',
                'test_user_advance_deleteskill_success',
                'test_user_view_advance_social',
                'test_user_advance_add_social_success',
                'test_user_advance_edit_social_success',
                'test_user_advance_delete_social_success',
                'test_user_view_profile_education',
                'test_user_profile_add_education_success',
                'test_user_profile_edit_education_success',
                'test_user_profile_delete_education_success',
                'test_user_view_profile_experience',
                'test_user_profile_add_experience_success',
                'test_user_profile_edit_experience_success',
                'test_user_profile_delete_experience_success',
                'test_user_view_profile_portfolio'
            ]);
        }

        private function isNeedCookieAuthMiddlewareAndFormSession(): bool
        {
            return in_array($this->getName(), [
                'test_user_edit_name_failed_because_validation',
                'test_user_edit_email_failed_because_validation',
                'test_user_edit_password_failed_because_validation',
                'test_user_edit_password_failed_because_wrong_old_password',
                'test_user_advance_add_personal_success',
                'test_user_advance_add_personal_error_because_validation',
                'test_user_advance_add_skill_failed_because_validation',
                'test_user_advance_add_skill_failed_because_data_has_been_there',
                'test_user_advance_edit_skill_failed_because_validation',
                'test_user_advance_add_social_failed_because_validation',
                'test_user_advance_add_social_failed_because_data_has_been_there',
                'test_user_advance_edit_social_failed_because_validation',
                'test_user_profile_add_education_failed_because_validation',
                'test_user_profile_add_education_failed_because_data_has_been_there',
                'test_user_profile_edit_education_failed_because_validation',
                'test_user_profile_add_experience_failed_because_validation',
                'test_user_profile_add_experience_failed_because_data_has_been_there',
                'test_user_profile_edit_experience_failed_because_validation',
                'test_user_profile_add_portfolio_success',
                'test_user_profile_add_portfolio_error_because_validation',
            ]);
        }

        private function isNeedCookieFormSession(): bool
        {
            return in_array($this->getName(), [
                'test_user_register_failed_because_data_has_been_there',
                'test_user_register_failed_because_validation',
                'test_user_login_failed_because_wrong_password_or_no_data',
                'test_user_login_failed_because_validation',
            ]);
        }

        private function isNeedSessionServiceCreate(): bool
        {
            return !in_array($this->getName(), [
                'test_user_view_register_success',
                'test_user_register_success',
                'test_user_register_failed_because_data_has_been_there',
                'test_user_register_failed_because_validation',
                'test_user_view_login_success',
                'test_user_login_success',
                'test_user_login_failed_because_wrong_password_or_no_data',
                'test_user_login_failed_because_validation',
            ]);
        }

        private function isNeedSessionServiceDestroy(): bool
        {
            return !in_array($this->getName(), [
                'test_user_view_register_success',
                'test_user_register_success',
                'test_user_register_failed_because_data_has_been_there',
                'test_user_register_failed_because_validation',
                'test_user_view_login_success',
                'test_user_login_success',
                'test_user_login_failed_because_wrong_password_or_no_data',
                'test_user_login_failed_because_validation',
                'test_user_logout',
            ]);
        }

        public function test_user_view_register_success(): void
        {
            $response = $this->clientWithNoCookies->get('/user/register');
            $body = (string) $response->getBody();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Register', $body);
        }

        public function test_user_view_register_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/register');

            $body = (string) $response->getBody();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Dashboard', $body);
        }

        public function test_user_register_success(): void
        {
            $user = new User();
            $user->email = 'rahmat@gmail.com';

            $response = $this->clientWithNoCookies->post('/user/register', [
                'form_params' => [
                    'email' => $user->email,
                    'password' => 'Ilham123!',
                    'passwordConfirm' => 'Ilham123!',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);

            self::$userRepository->deleteOne($user);
        }

        public function test_user_register_failed_because_data_has_been_there(): void
        {
            $response = $this->clientWithCookieFormSession->post('/user/register', [
                'form_params' => [
                    'email' => self::$user->email,
                    'password' => 'Ilham123!',
                    'passwordConfirm' => 'Ilham123!',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('User udah ada', $body);
        }

        public function test_user_register_failed_because_validation(): void
        {
            $response = $this->clientWithCookieFormSession->post('/user/register', [
                'form_params' => [
                    'email' => '',
                    'password' => '',
                    'passwordConfirm' => '',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_view_login_success(): void
        {
            $response = $this->clientWithNoCookies->get('/user/login');
            $body = (string) $response->getBody();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Login', $body);
        }

        public function test_user_view_login_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/login');

            $body = (string) $response->getBody();

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Dashboard', $body);
        }

        public function test_user_login_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/login', [
                'form_params' => [
                    'email' => self::$user->email,
                    'password' => self::$password,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Dashboard', $body);
        }

        public function test_user_login_failed_because_wrong_password_or_no_data(): void
        {
            $response = $this->clientWithCookieFormSession->post('/user/login', [
                'form_params' => [
                    'email' => self::$user->email,
                    'password' => 'Salah123!',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Email atau Password salah', $body);
        }

        public function test_user_login_failed_because_validation(): void
        {
            $response = $this->clientWithCookieFormSession->post('/user/login', [
                'form_params' => [
                    'email' => '',
                    'password' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_logout(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/logout');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_user_view_dashboard(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/dashboard');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Dashboard', $body);
        }

        public function test_user_edit_name_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/update-name', [
                'form_params' => [
                    'name' => 'User 123',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update name', $body);
        }

        public function test_user_edit_name_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/update-name', [
                'form_params' => [
                    'name' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_edit_email_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/update-email', [
                'form_params' => [
                    'email' => self::$user->email,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update email', $body);
        }

        public function test_user_edit_email_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/update-email', [
                'form_params' => [
                    'email' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_edit_password_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/update-password', [
                'form_params' => [
                    'oldPassword' => self::$password,
                    'newPassword' => self::$password,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update password', $body);
        }

        public function test_user_edit_password_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/update-password', [
                'form_params' => [
                    'oldPassword' => '',
                    'newPassword' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_edit_password_failed_because_wrong_old_password(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/update-password', [
                'form_params' => [
                    'oldPassword' => 'Salah123!',
                    'newPassword' => 'Salah123!'
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Old password is wrong', $body);
        }

        public function test_user_view_advance_personal(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/advance/personal');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Personal', $body);
        }

        public function test_user_advance_add_personal_success(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/personal', [
                'multipart' => [
                    [
                        'name'     => 'image',
                        'contents' => fopen(__DIR__ . '/assets/test.jpeg', 'r'), // Path gambar : 'r' itu artinya read ham
                        'filename' => 'test.jpeg'
                    ],
                    [
                        'name'     => 'phone',
                        'contents' => '0812123123'
                    ],
                    [
                        'name'     => 'headline',
                        'contents' => 'Programmer'
                    ],
                    [
                        'name'     => 'location',
                        'contents' => 'Jakarta'
                    ]
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update advance personal', $body);

            $userAdvancePersonal = new UserAdvancePersonal();
            $userAdvancePersonal->user_id = self::$user->email;

            deleteFolder(__DIR__ . '/../app/public');

            self::$userRepository->destroyOneAdvancePersonal(self::$user->email);
        }

        public function test_user_advance_add_personal_error_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/personal', [
                'multipart' => [
                    [
                        'name'     => 'phone',
                        'contents' => '0812123123'
                    ],
                    [
                        'name'     => 'headline',
                        'contents' => 'Programmer'
                    ],
                    [
                        'name'     => 'location',
                        'contents' => 'Jakarta'
                    ]
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Image bukan tipe file yang diizinkan.', $body);
        }

        public function test_user_view_advance_skill(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/advance/skill');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Skill', $body);
        }

        public function test_user_advance_add_skill_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/skill', [
                'form_params' => [
                    'name' => 'Javascript',
                    'rating' => '5',
                    'description' => 'React Js, Vue Js'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update advance skill', $body);
        }

        public function test_user_advance_add_skill_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/skill', [
                'form_params' => [
                    'name' => '',
                    'rating' => '',
                    'description' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_advance_add_skill_failed_because_data_has_been_there(): void
        {
            $skill = new UserAdvanceSkills();
            $skill->id = 212;
            $skill->user_id = self::$user->email;
            $skill->name = 'Javascript';
            $skill->rating = '5';
            $skill->description = 'React Js, Vue Js';

            self::$userRepository->saveAdvanceSkills($skill);

            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/skill', [
                'form_params' => [
                    'name' => 'Javascript',
                    'rating' => '5',
                    'description' => 'React Js, Vue Js'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Skill udah ada', $body);

            self::$userRepository->destroyOneAdvanceSkills($skill);
        }


        public function test_user_advance_edit_skill_success(): void
        {
            $skill = new UserAdvanceSkills();
            $skill->id = 212;
            $skill->user_id = self::$user->email;
            $skill->name = 'HTML, Css, Js';
            $skill->rating = 5;
            $skill->description = 'Membuat Landing Page';

            self::$userRepository->saveAdvanceSkills($skill);

            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/skill', [
                'form_params' => [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'rating' => $skill->rating,
                    'description' => 'Membuat Landing Page Pro+'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update advance skill', $body);
        }

        public function test_user_advance_edit_skill_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/skill', [
                'form_params' => [
                    'id' => '',
                    'name' => '',
                    'rating' => '',
                    'description' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_advance_deleteskill_success(): void
        {
            $skill = new UserAdvanceSkills();
            $skill->id = 212;
            $skill->user_id = self::$user->email;
            $skill->name = 'HTML, Css, Js';
            $skill->rating = 5;
            $skill->description = 'Membuat Landing Page';

            self::$userRepository->saveAdvanceSkills($skill);

            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/skill', [
                'form_params' => [
                    'id' => $skill->id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Skill', $body);
        }

        public function test_user_view_advance_social(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/advance/social');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Social', $body);
        }

        public function test_user_advance_add_social_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/social', [
                'form_params' => [
                    'app_name' => 'instagram',
                    'url_link' => 'https://instagram.com/juggernaut',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update advance social', $body);
        }

        public function test_user_advance_add_social_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/social', [
                'form_params' => [
                    'app_name' => '',
                    'url_link' => '',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_advance_add_social_failed_because_data_has_been_there(): void
        {
            $socialMedia = new UserAdvanceSocial();
            $socialMedia->id = 212;
            $socialMedia->user_id = self::$user->email;
            $socialMedia->app_name = 'instagram';
            $socialMedia->url_link = 'https://www.instagram.com/ilhamrhmtkbr';

            self::$userRepository->saveAdvanceSocial($socialMedia);

            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/social', [
                'form_params' => [
                    'app_name' => 'instagram',
                    'url_link' => 'https://instagram.com/juggernaut',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Social media udah ada', $body);

            self::$userRepository->destroyOneAdvanceSocial($socialMedia);
        }

        public function test_user_advance_edit_social_success(): void
        {
            $social = new UserAdvanceSocial();
            $social->id = 212;
            $social->user_id = self::$user->email;
            $social->app_name = 'instagram';
            $social->url_link = 'https://www.instagram.com/test';

            self::$userRepository->saveAdvanceSocial($social);

            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/social', [
                'form_params' => [
                    'id' => $social->id,
                    'app_name' => $social->app_name,
                    'url_link' => 'https://www.instagram.com/aws',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update advance social', $body);
        }

        public function test_user_advance_edit_social_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/advance/social', [
                'form_params' => [
                    'id' => 212,
                    'app_name' => 'instagram',
                    'url_link' => 'https://instagram/testing'
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Url Link harus berupa link media sosial yang valid', $body);
        }

        public function test_user_advance_delete_social_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/advance/social', [
                'form_params' => [
                    'id' => 212,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Social', $body);
        }

        public function test_user_view_profile_education(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/profile/education');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Education', $body);
        }

        public function test_user_profile_add_education_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/education', [
                'form_params' => [
                    'degree_id' => 1,
                    'institution' => 'Universitas Bina Sarana Informatika',
                    'field' => 'Sistem Informasi',
                    'graduation_year' => '2022',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update profile education', $body);
        }

        public function test_user_profile_add_education_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/education', [
                'form_params' => [
                    'degree_id' => '',
                    'institution' => '',
                    'field' => '',
                    'graduation_year' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_profile_add_education_failed_because_data_has_been_there(): void
        {
            $education = new UserProfileEducation();
            $education->id = 212;
            $education->user_id = self::$user->email;
            $education->degree_id = 1;
            $education->institution = 'Universitas Bina Sarana Informatika';
            $education->field = 'Sistem Informasi';
            $education->graduation_year = '2022';

            self::$userRepository->saveProfileEducation($education);

            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/education', [
                'form_params' => [
                    'degree_id' => 1,
                    'institution' => 'Universitas Bina Sarana Informatika',
                    'field' => 'Sistem Informasi',
                    'graduation_year' => '2022',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Education udah ada', $body);

            self::$userRepository->destroyOneProfileEducation($education);
        }

        public function test_user_profile_edit_education_success(): void
        {
            $education = new UserProfileEducation();
            $education->id = 212;
            $education->user_id = self::$user->email;
            $education->degree_id = 1;
            $education->institution = 'Universitas Bina Sarana Informatika';
            $education->field = 'Sistem Informasi';
            $education->graduation_year = '2022';

            self::$userRepository->saveProfileEducation($education);

            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/education', [
                'form_params' => [
                    'id' => $education->id,
                    'degree_id' => 1,
                    'institution' => 'Universitas Indonesia',
                    'field' => 'Sistem Informasi',
                    'graduation_year' => '2022',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update profile education', $body);
        }

        public function test_user_profile_edit_education_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/education', [
                'form_params' => [
                    'id' => 212,
                    'degree_id' => 1,
                    'institution' => 'Universitas Indonesia',
                    'field' => 'Sistem Informasi',
                    'graduation_year' => '',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_profile_delete_education_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/education', [
                'form_params' => [
                    'id' => 212,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Education', $body);
        }

        public function test_user_view_profile_experience(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/profile/experience');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Experience', $body);
        }

        public function test_user_profile_add_experience_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/experience', [
                'form_params' => [
                    'job_title' => 'Web Developer',
                    'job_description' => 'Mengembangkan sistem yang sudah ada',
                    'company_name' => 'Shopee',
                    'work_duration' => 3,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update profile experience', $body);
        }

        public function test_user_profile_add_experience_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/experience', [
                'form_params' => [
                    'id' => 1,
                    'job_title' => '',
                    'job_description' => '',
                    'company_name' => '',
                    'work_duration' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_profile_add_experience_failed_because_data_has_been_there(): void
        {
            $experience = new UserProfileExperience();
            $experience->id = 212;
            $experience->user_id = self::$user->email;
            $experience->job_title = 'Web Developer';
            $experience->job_description = 'Mengembangkan sistem yang sudah ada';
            $experience->company_name = 'Shopee';
            $experience->work_duration = 3;

            self::$userRepository->saveProfileExperience($experience);

            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/experience', [
                'form_params' => [
                    'job_title' => 'Web Developer',
                    'job_description' => 'Mengembangkan sistem yang sudah ada',
                    'company_name' => 'Shopee',
                    'work_duration' => 3,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Experience udah ada', $body);

            self::$userRepository->destroyOneProfileexperience($experience);
        }

        public function test_user_profile_edit_experience_success(): void
        {
            $experience = new UserProfileExperience();
            $experience->id = 212;
            $experience->user_id = self::$user->email;
            $experience->job_title = 'Web Developer';
            $experience->job_description = 'Mengembangkan sistem yang sudah ada';
            $experience->company_name = 'Shopee';
            $experience->work_duration = 3;

            self::$userRepository->saveProfileexperience($experience);

            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/experience', [
                'form_params' => [
                    'id' => $experience->id,
                    'job_title' => 'Web Developer',
                    'job_description' => 'Mengembangkan sistem yang sudah ada',
                    'company_name' => 'Shopee',
                    'work_duration' => 4,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update profile experience', $body);
        }

        public function test_user_profile_edit_experience_failed_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/experience', [
                'form_params' => [
                    'id' => 1,
                    'job_title' => '',
                    'job_description' => '',
                    'company_name' => '',
                    'work_duration' => '',
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_user_profile_delete_experience_success(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('POST', '/user/profile/experience', [
                'form_params' => [
                    'id' => 212,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Experience', $body);
        }

        public function test_user_view_profile_portfolio(): void
        {
            $response = $this->clientWithCookieAuthMiddleware->request('GET', '/user/profile/portfolio');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Portfolio', $body);
        }

        public function test_user_profile_add_portfolio_success(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/portfolio', [
                'multipart' => [
                    [
                        'name'     => 'picture',
                        'contents' => fopen(__DIR__ . '/assets/test.jpeg', 'r'), // Path gambar : 'r' itu artinya read ham
                        'filename' => 'test.jpeg'
                    ],
                    [
                        'name'     => 'title',
                        'contents' => 'portfolio title'
                    ],
                    [
                        'name'     => 'description',
                        'contents' => 'portfolio description'
                    ],
                    [
                        'name'     => 'link',
                        'contents' => 'portfolio link'
                    ]
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update profile portfolio', $body);

            deleteFolder(__DIR__ . '/../app/public');
        }

        public function test_user_profile_add_portfolio_error_because_validation(): void
        {
            $response = $this->clientWithCookieAuthMiddlewareAndFormSession->post('/user/profile/portfolio', [
                'form_params' => [
                    'title' => '',
                    'description' => '',
                    'link' => ''
                ],
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }
    }
}
