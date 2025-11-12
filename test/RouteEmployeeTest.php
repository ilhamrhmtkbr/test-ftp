<?php

namespace ilhamrhmtkbr\Test {

    require_once __DIR__ . '/helper/http.php';
    require_once __DIR__ . '/../config/test.php';

    use GuzzleHttp\Client;
    use ilhamrhmtkbr\App\Models\User;
    use ilhamrhmtkbr\App\Redis\Session;
    use ilhamrhmtkbr\Test\refactory\CustomClientCookies;
    use PHPUnit\Framework\TestCase;

    class RouteEmployeeTest extends TestCase
    {
        private Client $clientWithCookies;
        private Client $clientWithNoCookies;
        private static User $employeeUser;
        private static Session $session;

        public static function setUpBeforeClass(): void
        {
            self::$employeeUser = new User();
            self::$employeeUser->email = 'ilhamrhmtkbr@gmail.com';

            self::$session = new Session();
        }


        public function setUp(): void
        {
            $this->clientWithCookies = CustomClientCookies::createClientWithCookieAuthMiddleware(self::$employeeUser->email);
            $this->clientWithNoCookies = new Client(['base_uri' => getTestConfig('base_uri')]);
            self::$session->create(self::$employeeUser->email);
        }

        public function tearDown(): void
        {
            self::$session->destroy();
        }

        public function test_employee_view_employee_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Ilham Rahmat Akbar', $body);
        }

        public function test_employee_view_employee_failed_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_attendance_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/attendance/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_attendance_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/attendance/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_attendance_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/attendance');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Attendance', $body);
        }

        public function test_employee_view_attendance_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/attendance');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_contracts_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/contracts/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_contracts_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/contracts/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_contracts_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/contracts');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Contracts', $body);
        }

        public function test_employee_view_contracts_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/contracts');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_leave_requests_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/leave-requests/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_leave_requests_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/leave-requests/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_leave_requests_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/leave-requests');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Leave Request', $body);
        }

        public function test_employee_view_leave_requests_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/leave-requests');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_overtime_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/overtime/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/overtime/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_overtime_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Overtime', $body);
        }

        public function test_employee_view_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_payrolls_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/payrolls/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_payrolls_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/payrolls/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_payrolls_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/payrolls');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Overtime', $body);
        }

        public function test_employee_view_payrolls_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/payrolls');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_download_project_assignments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/project-assignments/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_employee_download_project_assignments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/project-assignments/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_employee_view_project_assignments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/employee/project-assignments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Project Assignments', $body);
        }

        public function test_employee_view_project_assignments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/employee/project-assignments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }
    }
}
