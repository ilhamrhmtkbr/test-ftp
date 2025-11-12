<?php

namespace ilhamrhmtkbr\Test {

    require __DIR__ . '/helper/http.php';
    require_once __DIR__ . '/../config/test.php';

    use GuzzleHttp\Client;
    use ilhamrhmtkbr\App\Config\Database;
    use ilhamrhmtkbr\App\Models\User;
    use ilhamrhmtkbr\App\Helper\FormSessionHelper;
    use ilhamrhmtkbr\App\Redis\Session;
    use ilhamrhmtkbr\App\Repository\UserRepository;
    use ilhamrhmtkbr\Test\refactory\CustomClientCookies;
    use PHPUnit\Framework\TestCase;

    class RouteCandidateTest extends TestCase
    {
        private static \PDO $connection;
        private static User $candidateUser;
        private static UserRepository $userRepository;
        private Client $clientWithCookies;
        private Client $clientWithNoCookies;
        private static Session $session;

        public static function setUpBeforeClass(): void
        {
            self::$candidateUser = new User();
            self::$candidateUser->email = 'candidate@gmail.com';
            self::$candidateUser->password = password_hash('Candidate123!', PASSWORD_BCRYPT);

            self::$connection = Database::getConnection();
            self::$userRepository = new UserRepository(self::$connection);
            self::$userRepository->save(self::$candidateUser);
            self::$session = new Session();
        }

        public static function tearDownAfterClass(): void
        {
            self::$userRepository->deleteOne(self::$candidateUser);
        }

        public function setUp(): void
        {
            $session = uniqid();
            FormSessionHelper::$FILENAME = $session;

            $this->clientWithNoCookies = new Client(['base_uri' => getTestConfig('base_uri')]);

            if ($this->getName() == 'test_candidate_add_job_failed_because_job_has_been_applied') {
                $this->clientWithCookies = CustomClientCookies::createClientWithCookieAuthMiddlewareAndFormSession('budi@gmail.com', $session);
                self::$session->create('budi@gmail.com');
            } else {
                $this->clientWithCookies = CustomClientCookies::createClientWithCookieAuthMiddlewareAndFormSession(self::$candidateUser->email, $session);
                self::$session->create(self::$candidateUser->email);
            }
        }

        public function tearDown(): void
        {
            self::$session->destroy();
        }

        public function test_candidate_view_jobs_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/candidate/jobs');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Jobs', $body);
        }

        public function test_candidate_view_jobs_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/candidate/jobs');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_candidate_view_job_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/candidate/job', [
                'query' => [
                    'id' => 1
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Job', $body);
        }

        public function test_candidate_view_job_failed_because_no_param(): void
        {
            $response = $this->clientWithCookies->request('GET', '/candidate/job');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Jobs', $body);
        }

        public function test_candidate_view_job_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/candidate/job');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_candidate_add_job_failed_because_job_has_been_applied(): void
        {
            $response = $this->clientWithCookies->request('POST', '/candidate/job', [
                'form_params' => [
                    'id' => 2
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Job udah di apply', $body);
        }

        public function test_candidate_add_job_failed_because_profile_candidate_not_completed(): void
        {
            $response = $this->clientWithCookies->request('POST', '/candidate/job', [
                'form_params' => [
                    'id' => 1
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Mohon lengkapi profile', $body);
        }

        public function test_candidate_view_apploed_failed_because_no_param(): void
        {
            $response = $this->clientWithCookies->request('GET', '/candidate/applied');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Applied', $body);
        }

        public function test_candidate_view_apploed_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/candidate/applied');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }
    }
}
