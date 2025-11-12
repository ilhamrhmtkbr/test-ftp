<?php

namespace ilhamrhmtkbr\Test {
    require_once __DIR__ . '/helper/http.php';
    require_once __DIR__ . '/../config/test.php';

    use GuzzleHttp\Client;
    use ilhamrhmtkbr\App\Config\Database;
    use ilhamrhmtkbr\App\Models\Candidates;
    use ilhamrhmtkbr\App\Models\CompanyEmployeeProjects;
    use ilhamrhmtkbr\App\Models\CompanyEmployeeRoles;
    use ilhamrhmtkbr\App\Models\CompanyOfficeDepartements;
    use ilhamrhmtkbr\App\Models\CompanyOfficeFinancialTransactions;
    use ilhamrhmtkbr\App\Models\CompanyOfficeRecruitments;
    use ilhamrhmtkbr\App\Models\EmployeeAttendanceRules;
    use ilhamrhmtkbr\App\Models\EmployeeContracts;
    use ilhamrhmtkbr\App\Models\EmployeeLeaveRequests;
    use ilhamrhmtkbr\App\Models\EmployeeOvertime;
    use ilhamrhmtkbr\App\Models\EmployeePayrolls;
    use ilhamrhmtkbr\App\Models\EmployeeProjectAssigments;
    use ilhamrhmtkbr\App\Models\Employees;
    use ilhamrhmtkbr\App\Models\User;
    use ilhamrhmtkbr\App\Helper\FormSessionHelper;
    use ilhamrhmtkbr\App\Redis\Session;
    use ilhamrhmtkbr\App\Repository\CandidateRepository;
    use ilhamrhmtkbr\App\Repository\HrRepository;
    use ilhamrhmtkbr\App\Repository\UserRepository;
    use ilhamrhmtkbr\Test\refactory\CustomClientCookies;
    use PHPUnit\Framework\TestCase;

    class RouteHrTest extends TestCase
    {
        private Client $clientWithCookies;
        private Client $clientWithNoCookies;
        private static HrRepository $hrRepository;
        private static UserRepository $userRepository;
        private static CandidateRepository $candidateRepository;
        private static Session $session;
        private static User $hrUser;
        private static User $candidateUser;
        private static User $employeeUser;
        private static Employees $employee;
        private static Candidates $candidate;

        public static function setUpBeforeClass(): void
        {
            self::$hrUser = new User();
            self::$hrUser->email = 'rani@contoh.com';

            self::$candidateUser = new User();
            self::$candidateUser->email = 'candidate@gmail.com';
            self::$candidateUser->password = 'rahasia';

            self::$employeeUser = new User();
            self::$employeeUser->email = 'employee@gmail.com';
            self::$employeeUser->password = 'rahasia';

            self::$employee = new Employees();
            self::$employee->user_id = self::$employeeUser->email;
            self::$employee->role_id = 1;
            self::$employee->department_id = 1;
            self::$employee->salary = 12000000;
            self::$employee->hire_date = date('Y-m-d');
            self::$employee->status = 'active';

            self::$candidate = new Candidates();
            self::$candidate->user_id = self::$candidateUser->email;
            self::$candidate->job_id = 1;
            self::$candidate->status = 'applied';

            $connection = Database::getConnection();

            self::$hrRepository = new HrRepository($connection);
            self::$userRepository = new UserRepository($connection);
            self::$candidateRepository = new CandidateRepository($connection);

            self::$session = new Session();

            self::$userRepository->save(self::$candidateUser);
            self::$userRepository->save(self::$employeeUser);
            self::$hrRepository->saveEmployee(self::$employee);
            self::$candidateRepository->saveJobApply(self::$candidate);
        }

        public static function tearDownAfterClass(): void
        {
            self::$userRepository->deleteOne(self::$candidateUser);
            self::$userRepository->deleteOne(self::$employeeUser);
        }

        public function setUp(): void
        {
            $session = uniqid();
            FormSessionHelper::$FILENAME = $session;

            self::$session->create(self::$hrUser->email);

            $this->clientWithNoCookies = new Client(['base_uri' => getTestConfig('base_uri')]);
            $this->clientWithCookies = CustomClientCookies::createClientWithCookieAuthMiddlewareAndFormSession(self::$hrUser->email, $session);
        }

        public function tearDown(): void
        {
            self::$session->destroy();
        }

        public function test_hr_download_candidates_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/candidates/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);

            // Jika ingin memvalidasi isi, gunakan library parser PDF (misalnya `smalot/pdfparser`)
            // Contoh:
            // $parser = new \Smalot\PdfParser\Parser();
            // $pdf = $parser->parseContent($pdfContent);
            // $this->assertStringContainsString('Expected Content', $pdf->getText());
        }

        public function test_hr_download_candidates_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/candidates/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_candidates_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/candidates');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Candidates', $body);
        }

        public function test_hr_view_candidates_failed(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/candidates');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_candidate_details_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/candidate/details', [
                'query' => [
                    'id' => self::$candidateUser->email
                ]
            ]);
            $body = (string) $response->getBody();

            $this->assertStringContainsString('Candidate belum mengatur', $body);
        }

        public function test_hr_view_candidate_details_failed_because_user_not_found(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/candidate/details', [
                'query' => [
                    'id' => 'usernotfound@gmail.com'
                ]
            ]);
            $body = (string) $response->getBody();

            $this->assertStringContainsString('Candidates', $body); // balik ke halaman candidates
        }

        public function test_hr_edit_candidate_status_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/candidate/update', [
                'form_params' => [
                    'email' => self::$candidateUser->email,
                    'status' => 'interviewed'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Candidate belum mengatur', $body);
        }

        public function test_hr_edit_candidate_status_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/candidate/update', [
                'form_params' => [
                    'email' => self::$candidateUser->email,
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_candidate_status_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/candidate/update', [
                'form_params' => [
                    'email' => self::$candidateUser->email,
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_download_employees_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employees/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employees_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employees/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employees_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employees');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Employees', $body);
        }

        public function test_hr_view_employees_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employees');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_success(): void
        {
            $candidate = new Candidates();
            $candidate->user_id = self::$candidateUser->email;
            $candidate->status = 'hired';

            self::$hrRepository->editStatusCandidate($candidate);

            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'email' => $candidate->user_id,
                    'role' => 1,
                    'department' => 1,
                    'salary' => 12000000,
                    'hire_date' => '2025-01-01',
                    'status' => 'active'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update : ' . $candidate->user_id, $body);

            self::$hrRepository->destroyEmployee($candidate->user_id);

            $candidate->status = 'interviewed';
            self::$hrRepository->editStatusCandidate($candidate);
        }

        public function test_hr_add_employee_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'email' => '',
                    'role' => '',
                    'department' => '',
                    'salary' => '',
                    'hire_date' => '',
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_failed_because_candidate_status_not_hired(): void
        {
            $candidate = new Candidates();
            $candidate->user_id = self::$candidateUser->email;

            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'email' => $candidate->user_id,
                    'role' => 1,
                    'department' => 1,
                    'salary' => 12000000,
                    'hire_date' => date('Y-m-d'),
                    'status' => 'active'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Hanya candidate yang berstatus hired yang bisa ditambahkan!', $body);
        }

        public function test_hr_edit_employee_success(): void
        {
            $candidate = new Candidates();
            $candidate->user_id = self::$candidateUser->email;
            $candidate->status = 'hired';

            self::$hrRepository->editStatusCandidate($candidate);

            $employee = new Employees();
            $employee->user_id = $candidate->user_id;
            $employee->role_id = 1;
            $employee->department_id = 1;
            $employee->salary = 12000000;
            $employee->hire_date = '2025-01-01';
            $employee->status = 'active';

            self::$hrRepository->saveEmployee($employee);

            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'email' => $candidate->user_id,
                    'role' => 1,
                    'department' => 1,
                    'salary' => 12000000,
                    'hire_date' => '2025-01-01',
                    'status' => 'active'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update : ' . $candidate->user_id, $body);

            self::$hrRepository->destroyEmployee($candidate->user_id);

            $candidate->status = 'interviewed';
            self::$hrRepository->editStatusCandidate($candidate);
        }

        public function test_hr_edit_employee_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'email' => '',
                    'role' => '',
                    'department' => '',
                    'salary' => '',
                    'hire_date' => '',
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_success(): void
        {
            $newEmployee = new Employees();
            $newEmployee->user_id = self::$candidate->user_id;
            $newEmployee->role_id = 1;
            $newEmployee->department_id = 1;
            $newEmployee->salary = 12000000;
            $newEmployee->hire_date = date('Y-m-d');
            $newEmployee->status = 'active';

            self::$hrRepository->saveEmployee($newEmployee);

            $response = $this->clientWithCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'user_id' => self::$candidateUser->email,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);

            self::$hrRepository->destroyEmployee(self::$candidate->user_id);
        }

        public function test_hr_delete_employee_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee', [
                'form_params' => [
                    'user_id' => self::$candidateUser->email,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_details_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee', [
                'query_params' => [
                    'id' => self::$candidateUser->email
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('User', $body);
        }

        public function test_hr_download_employee_attendance_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/attendance/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_attendance_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/attendance/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_attendance_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/attendance');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Attendance', $body);
        }

        public function test_hr_view_employee_attendance_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/attendance');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_attendance_rules_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/attendance-rules/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_attendance_rules_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/attendance-rules/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_attendance_rules_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/attendance-rules');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Attendance Rules', $body);
        }

        public function test_hr_view_employee_attendance_rules_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/attendance-rules');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_attendance_rule_success(): void
        {
            $ruleName = 'Shift siang';
            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'rule_name' => $ruleName,
                    'start_time' => '13:00:00',
                    'end_time' => '19:00:00',
                    'late_threshold' => '00:15:00',
                    'penalty_for_late' => 5000
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save attendance-rule', $body);

            self::$hrRepository->forTestEmployeeAttendanceRuleDestroy($ruleName);
        }

        public function test_hr_add_employee_attendance_rule_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/attendance-rule');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_attendance_rule_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'rule_name' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'late_threshold' => '',
                    'penalty_for_late' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_attendance_rule_failed_because_data_has_been_there(): void
        {
            $employeeAttendanceRule = new EmployeeAttendanceRules();
            $employeeAttendanceRule->rule_name = 'Shift siang';
            $employeeAttendanceRule->start_time = '13:00:00';
            $employeeAttendanceRule->end_time = '19:00:00';
            $employeeAttendanceRule->late_threshold = '00:15:00';
            $employeeAttendanceRule->penalty_for_late = 5000;

            self::$hrRepository->saveEmployeeAttendanceRule($employeeAttendanceRule);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'rule_name' => 'Shift siang',
                    'start_time' => '13:00:00',
                    'end_time' => '19:00:00',
                    'late_threshold' => '00:15:00',
                    'penalty_for_late' => 5000
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Attendance Rule udah ada!', $body);

            self::$hrRepository->forTestEmployeeAttendanceRuleDestroy($employeeAttendanceRule->rule_name);
        }

        public function test_hr_edit_employee_attendance_rule_success(): void
        {
            $employeeAttendanceRule = new EmployeeAttendanceRules();
            $employeeAttendanceRule->rule_name = 'Shift siang';
            $employeeAttendanceRule->start_time = '13:00:00';
            $employeeAttendanceRule->end_time = '19:00:00';
            $employeeAttendanceRule->late_threshold = '00:15:00';
            $employeeAttendanceRule->penalty_for_late = 5000;

            self::$hrRepository->saveEmployeeAttendanceRule($employeeAttendanceRule);

            $id = self::$hrRepository->forTestEmployeeAttendanceRuleGetId($employeeAttendanceRule->rule_name);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'id' => $id,
                    'rule_name' => $employeeAttendanceRule->rule_name,
                    'start_time' => $employeeAttendanceRule->start_time,
                    'end_time' => $employeeAttendanceRule->end_time,
                    'late_threshold' => '00:07:00',
                    'penalty_for_late' => $employeeAttendanceRule->penalty_for_late
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update attendance-rule', $body);

            self::$hrRepository->forTestEmployeeAttendanceRuleDestroy($employeeAttendanceRule->rule_name);
        }

        public function test_hr_edit_employee_attendance_rule_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/attendance-rule');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_attendance_rule_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'rule_name' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'late_threshold' => '',
                    'penalty_for_late' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_attendance_rule_success(): void
        {
            $employeeAttendanceRule = new EmployeeAttendanceRules();
            $employeeAttendanceRule->rule_name = 'Shift siang';
            $employeeAttendanceRule->start_time = '13:00:00';
            $employeeAttendanceRule->end_time = '19:00:00';
            $employeeAttendanceRule->late_threshold = '00:15:00';
            $employeeAttendanceRule->penalty_for_late = 5000;

            self::$hrRepository->saveEmployeeAttendanceRule($employeeAttendanceRule);

            $id = self::$hrRepository->forTestEmployeeAttendanceRuleGetId($employeeAttendanceRule->rule_name);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_attendance_rule_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/attendance-rule', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_contracts_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/contracts/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_contracts_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/contracts/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_contracts_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/contracts');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Contracts', $body);
        }

        public function test_hr_view_employee_contracts_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/contracts');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_contract_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'contract_start_date' => date('Y-m-d'),
                    'contract_end_date' => '2026-12-01',
                    'salary' => 1200000,
                    'contract_terms' => 'Amanah'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save contract : ' . self::$employee->user_id, $body);

            self::$hrRepository->forTestEmployeeContractDestroy(self::$employee->user_id);
        }

        public function test_hr_add_employee_contract_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/contract');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_contract_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'employee_id' => '',
                    'contract_start_date' => '',
                    'contract_end_date' => '',
                    'salary' => '',
                    'contract_terms' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_contract_failed_because_data_has_been_there(): void
        {
            $employeeContract = new EmployeeContracts();
            $employeeContract->employee_id = self::$employee->user_id;
            $employeeContract->contract_start_date = date('Y-m-d');
            $employeeContract->contract_end_date = '2026-12-01';
            $employeeContract->salary = 1200000;
            $employeeContract->contract_terms = 'Amanah';

            self::$hrRepository->saveEmployeeContract($employeeContract);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'contract_start_date' => date('Y-m-d'),
                    'contract_end_date' => '2026-12-01',
                    'salary' => 1200000,
                    'contract_terms' => 'Amanah'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Contract udah ada!', $body);

            self::$hrRepository->forTestEmployeeContractDestroy($employeeContract->employee_id);
        }

        public function test_hr_edit_employee_contract_success(): void
        {
            $employeeContract = new EmployeeContracts();
            $employeeContract->employee_id = self::$employee->user_id;
            $employeeContract->contract_start_date = date('Y-m-d');
            $employeeContract->contract_end_date = '2026-12-01';
            $employeeContract->salary = 1200000;
            $employeeContract->contract_terms = 'Amanah';

            self::$hrRepository->saveEmployeeContract($employeeContract);

            $id = self::$hrRepository->forTestEmployeeContractGetId($employeeContract);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'id' => $id,
                    'employee_id' => self::$employee->user_id,
                    'contract_start_date' => date('Y-m-d'),
                    'contract_end_date' => '2025-12-01',
                    'salary' => 1200000,
                    'contract_terms' => 'Amanah'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update contract : ' . $employeeContract->employee_id, $body);

            self::$hrRepository->forTestEmployeeContractDestroy($employeeContract->employee_id);
        }

        public function test_hr_edit_employee_contract_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/contract');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_contract_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'employee_id' => '',
                    'contract_start_date' => '',
                    'contract_end_date' => '',
                    'salary' => '',
                    'contract_terms' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_contract_success(): void
        {
            $employeeContract = new EmployeeContracts();
            $employeeContract->employee_id = self::$employee->user_id;
            $employeeContract->contract_start_date = date('Y-m-d');
            $employeeContract->contract_end_date = '2026-12-01';
            $employeeContract->salary = 1200000;
            $employeeContract->contract_terms = 'Amanah';

            self::$hrRepository->saveEmployeeContract($employeeContract);

            $id = self::$hrRepository->forTestEmployeeContractGetId($employeeContract);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_contract_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/contract', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_leave_requests_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/leave-requests/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_leave_requests_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/leave-requests/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_leave_requests_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/leave-requests');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Leave Requests', $body);
        }

        public function test_hr_view_employee_leave_requests_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/leave-requests');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_leave_request_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'leave_type' => 'Vacation',
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'status' => 'Approved',
                    'remarks' => 'Belajar Mandiri'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save leave request : ' . self::$employee->user_id, $body);

            self::$hrRepository->forTestEmployeeLeaveRequestDestroy(self::$employee->user_id);
        }

        public function test_hr_add_employee_leave_request_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/leave-request');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_leave_request_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'employee_id' => '',
                    'leave_type' => '',
                    'start_date' => '',
                    'end_date' => '',
                    'status' => '',
                    'remarks' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_leave_request_failed_because_data_has_been_there(): void
        {
            $employeeLeaveRequest = new EmployeeLeaveRequests();
            $employeeLeaveRequest->employee_id = self::$employee->user_id;
            $employeeLeaveRequest->leave_type = 'Vacation';
            $employeeLeaveRequest->start_date = '2024-01-01';
            $employeeLeaveRequest->end_date = '2024-12-31';
            $employeeLeaveRequest->status = 'Approved';
            $employeeLeaveRequest->remarks = 'Belajar Mandiri';

            self::$hrRepository->saveEmployeeLeaveRequest($employeeLeaveRequest);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'leave_type' => 'Vacation',
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'status' => 'Approved',
                    'remarks' => 'Belajar Mandiri'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Request udah ada!', $body);

            self::$hrRepository->forTestEmployeeLeaveRequestDestroy($employeeLeaveRequest->employee_id);
        }

        public function test_hr_edit_employee_leave_request_success(): void
        {
            $employeeLeaveRequest = new EmployeeLeaveRequests();
            $employeeLeaveRequest->employee_id = self::$employee->user_id;
            $employeeLeaveRequest->leave_type = 'Vacation';
            $employeeLeaveRequest->start_date = '2024-01-01';
            $employeeLeaveRequest->end_date = '2024-12-31';
            $employeeLeaveRequest->status = 'Approved';
            $employeeLeaveRequest->remarks = 'Belajar Mandiri';

            self::$hrRepository->saveEmployeeLeaveRequest($employeeLeaveRequest);

            $id = self::$hrRepository->forTestEmployeeLeaveRequestGetId($employeeLeaveRequest);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'id' => $id,
                    'employee_id' => self::$employee->user_id,
                    'leave_type' => 'Vacation',
                    'start_date' => '2024-01-01',
                    'end_date' => '2024-12-31',
                    'status' => 'Approved',
                    'remarks' => 'Pengen sukses'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update leave request : ' . $employeeLeaveRequest->employee_id, $body);

            self::$hrRepository->forTestEmployeeContractDestroy($employeeLeaveRequest->employee_id);
        }

        public function test_hr_edit_employee_leave_request_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/leave-request');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_leave_request_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'employee_id' => '',
                    'leave_type' => '',
                    'start_date' => '',
                    'end_date' => '',
                    'status' => '',
                    'remarks' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_leave_request_success(): void
        {
            $employeeLeaveRequest = new EmployeeLeaveRequests();
            $employeeLeaveRequest->employee_id = self::$employee->user_id;
            $employeeLeaveRequest->leave_type = 'Vacation';
            $employeeLeaveRequest->start_date = '2024-01-01';
            $employeeLeaveRequest->end_date = '2024-12-31';
            $employeeLeaveRequest->status = 'Approved';
            $employeeLeaveRequest->remarks = 'Belajar Mandiri';

            self::$hrRepository->saveEmployeeLeaveRequest($employeeLeaveRequest);

            $id = self::$hrRepository->forTestEmployeeLeaveRequestGetId($employeeLeaveRequest);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_leave_request_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/leave-request', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_overtime_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/overtime/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/overtime/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_overtime_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Overtime', $body);
        }

        public function test_hr_view_employee_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_overtime_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'overtime_date' => '2024-01-01',
                    'start_time' => '17:00:00',
                    'end_time' => '19:00:00',
                    'overtime_rate' => 50000,
                    'remarks' => 'Add feature'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save overtime : ' . self::$employee->user_id, $body);

            self::$hrRepository->forTestEmployeeOvertimeDestroy(self::$employee->user_id);
        }

        public function test_hr_add_employee_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_overtime_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'employee_id' => '',
                    'overtime_date' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'overtime_rate' => '',
                    'remarks' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_overtime_failed_because_data_has_been_there(): void
        {
            $employeeOvertime = new EmployeeOvertime();
            $employeeOvertime->employee_id = self::$employee->user_id;
            $employeeOvertime->overtime_date = '2024-01-01';
            $employeeOvertime->start_time = '17:00:00';
            $employeeOvertime->end_time = '19:00:00';
            $employeeOvertime->overtime_rate = 50000;
            $employeeOvertime->remarks = 'Add feature';

            self::$hrRepository->saveEmployeeOvertime($employeeOvertime);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'employee_id' => self::$employee->user_id,
                    'overtime_date' => '2024-01-01',
                    'start_time' => '17:00:00',
                    'end_time' => '19:00:00',
                    'overtime_rate' => 50000,
                    'remarks' => 'Add feature'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Overtime udah ada!', $body);

            self::$hrRepository->forTestEmployeeOvertimeDestroy($employeeOvertime->employee_id);
        }

        public function test_hr_edit_employee_overtime_success(): void
        {
            $employeeOvertime = new EmployeeOvertime();
            $employeeOvertime->employee_id = self::$employee->user_id;
            $employeeOvertime->overtime_date = '2024-01-01';
            $employeeOvertime->start_time = '17:00:00';
            $employeeOvertime->end_time = '19:00:00';
            $employeeOvertime->overtime_rate = 50000;
            $employeeOvertime->remarks = 'Add feature';

            self::$hrRepository->saveEmployeeOvertime($employeeOvertime);

            $id = self::$hrRepository->forTestEmployeeOvertimeGetId($employeeOvertime);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'id' => $id,
                    'employee_id' => self::$employee->user_id,
                    'overtime_date' => '2024-01-01',
                    'start_time' => '17:00:00',
                    'end_time' => '19:00:00',
                    'overtime_rate' => 50000,
                    'remarks' => 'Add feature'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update overtime : ' . $employeeOvertime->employee_id, $body);

            self::$hrRepository->forTestEmployeeOvertimeDestroy($employeeOvertime->employee_id);
        }

        public function test_hr_edit_employee_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/overtime');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_overtime_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'employee_id' => '',
                    'overtime_date' => '',
                    'start_time' => '',
                    'end_time' => '',
                    'overtime_rate' => '',
                    'remarks' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_overtime_success(): void
        {
            $employeeOvertime = new EmployeeOvertime();
            $employeeOvertime->employee_id = self::$employee->user_id;
            $employeeOvertime->overtime_date = '2024-01-01';
            $employeeOvertime->start_time = '17:00:00';
            $employeeOvertime->end_time = '19:00:00';
            $employeeOvertime->overtime_rate = 50000;
            $employeeOvertime->remarks = 'Add feature';

            self::$hrRepository->saveEmployeeOvertime($employeeOvertime);

            $id = self::$hrRepository->forTestEmployeeOvertimeGetId($employeeOvertime);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_overtime_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/overtime', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_payrolls_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/payrolls/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_payrolls_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/payrolls/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_payrolls_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/payrolls');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Payrolls', $body);
        }

        public function test_hr_view_employee_payrolls_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/payrolls');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_payroll_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'email' => self::$employee->user_id,
                    'payroll_month' => '2024-01',
                    'base_salary' => 50000,
                    'status' => 'Pending',
                    'payment_date' => '2024-01-03',
                    'remarks' => 'Gajian',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save payroll : ' . self::$employee->user_id, $body);

            self::$hrRepository->forTestEmployeePayrollDestroy(self::$employee->user_id);
        }

        public function test_hr_add_employee_payroll_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/payroll');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_payroll_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'email' => '',
                    'payroll_month' => '',
                    'base_salary' => '',
                    'status' => '',
                    'payment_date' => '',
                    'remarks' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_payroll_failed_because_data_has_been_there(): void
        {
            $employeePayroll = new EmployeePayrolls();
            $employeePayroll->employee_id = self::$employee->user_id;
            $employeePayroll->payroll_month = '2024-01-01';
            $employeePayroll->base_salary = 50000;

            $totalOvertime = self::$hrRepository->findEmployeeTotalOvertime($employeePayroll);
            $employeePayroll->total_overtime = $totalOvertime;

            $latePenalties = self::$hrRepository->findEmployeeLatePenalties($employeePayroll);
            $employeePayroll->late_penalties = $latePenalties;

            $employeePayroll->status = 'Pending';
            $employeePayroll->payment_date = '2024-01-03';
            $employeePayroll->remarks = 'Gajian';

            self::$hrRepository->saveEmployeePayroll($employeePayroll);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'email' => self::$employee->user_id,
                    'payroll_month' => '2024-01',
                    'base_salary' => 50000,
                    'status' => 'Pending',
                    'payment_date' => '2024-01-03',
                    'remarks' => 'Gajian',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Payroll sudah dibuat', $body);

            self::$hrRepository->forTestEmployeePayrollDestroy($employeePayroll->employee_id);
        }

        public function test_hr_edit_employee_payroll_success(): void
        {
            $employeePayroll = new EmployeePayrolls();
            $employeePayroll->employee_id = self::$employee->user_id;
            $employeePayroll->payroll_month = '2024-01-01';
            $employeePayroll->base_salary = 50000;

            $totalOvertime = self::$hrRepository->findEmployeeTotalOvertime($employeePayroll);
            $employeePayroll->total_overtime = $totalOvertime;

            $latePenalties = self::$hrRepository->findEmployeeLatePenalties($employeePayroll);
            $employeePayroll->late_penalties = $latePenalties;

            $employeePayroll->status = 'Pending';
            $employeePayroll->payment_date = '2024-01-03';
            $employeePayroll->remarks = 'Gajian';

            self::$hrRepository->saveEmployeePayroll($employeePayroll);

            $id = self::$hrRepository->forTestEmployeePayrollGetId($employeePayroll);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'id' => $id,
                    'email' => self::$employee->user_id,
                    'payroll_month' => '2024-01',
                    'base_salary' => 50000,
                    'status' => 'Pending',
                    'payment_date' => '2024-01-03',
                    'remarks' => 'Gajian',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update payroll : ' . $employeePayroll->employee_id, $body);

            self::$hrRepository->forTestEmployeePayrollDestroy($employeePayroll->employee_id);
        }

        public function test_hr_edit_employee_payroll_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/payroll');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_payroll_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'email' => '',
                    'payroll_month' => '',
                    'base_salary' => '',
                    'status' => '',
                    'payment_date' => '',
                    'remarks' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_payroll_success(): void
        {
            $employeePayroll = new EmployeePayrolls();
            $employeePayroll->employee_id = self::$employee->user_id;
            $employeePayroll->payroll_month = '2024-01-01';
            $employeePayroll->base_salary = 50000;

            $totalOvertime = self::$hrRepository->findEmployeeTotalOvertime($employeePayroll);
            $employeePayroll->total_overtime = $totalOvertime;

            $latePenalties = self::$hrRepository->findEmployeeLatePenalties($employeePayroll);
            $employeePayroll->late_penalties = $latePenalties;

            $employeePayroll->status = 'Pending';
            $employeePayroll->payment_date = '2024-01-03';
            $employeePayroll->remarks = 'Gajian';

            self::$hrRepository->saveEmployeePayroll($employeePayroll);

            $id = self::$hrRepository->forTestEmployeePayrollGetId($employeePayroll);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_payroll_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/payroll', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_employee_project_assignments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/project-assignments/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_employee_project_assignments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/project-assignments/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_employee_project_assignments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/employee/project-assignments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Project Assignments', $body);
        }

        public function test_hr_view_employee_project_assignments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/employee/project-assignments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_project_assignment_success(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'email' => self::$employee->user_id,
                    'project_id' => 1,
                    'role_in_project' => 'Leader',
                    'assigned_date' => '2024-01-03',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save project assignment : ' . self::$employee->user_id, $body);

            self::$hrRepository->forTestEmployeePayrollDestroy(self::$employee->user_id);
        }

        public function test_hr_add_employee_project_assignment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/project-assignment');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_employee_project_assignment_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'email' => '',
                    'project_id' => '',
                    'role_in_project' => '',
                    'assigned_date' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_employee_project_assignment_failed_because_data_has_been_there(): void
        {
            $employeProjectAssignment = new EmployeeProjectAssigments();
            $employeProjectAssignment->employee_id = self::$employee->user_id;
            $employeProjectAssignment->project_id = 1;
            $employeProjectAssignment->role_in_project = 'Leader';
            $employeProjectAssignment->assigned_date = '2024-01-03';

            self::$hrRepository->saveEmployeeProjectAssignment($employeProjectAssignment);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'email' => self::$employee->user_id,
                    'project_id' => 1,
                    'role_in_project' => 'Leader',
                    'assigned_date' => '2024-01-03',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Project Assignments udah ada!', $body);

            self::$hrRepository->forTestEmployeeProjectAssignmentDestroy($employeProjectAssignment->employee_id);
        }

        public function test_hr_edit_employee_project_assignment_success(): void
        {
            $employeProjectAssignment = new EmployeeProjectAssigments();
            $employeProjectAssignment->employee_id = self::$employee->user_id;
            $employeProjectAssignment->project_id = 1;
            $employeProjectAssignment->role_in_project = 'Leader';
            $employeProjectAssignment->assigned_date = '2024-01-03';

            self::$hrRepository->saveEmployeeProjectAssignment($employeProjectAssignment);

            $id = self::$hrRepository->forTestEmployeeProjectAssignmentGetId($employeProjectAssignment);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'id' => $id,
                    'email' => self::$employee->user_id,
                    'project_id' => 1,
                    'role_in_project' => 'Leader',
                    'assigned_date' => '2024-01-03',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update project assignment : ' . $employeProjectAssignment->employee_id, $body);

            self::$hrRepository->forTestEmployeeProjectAssignmentDestroy($employeProjectAssignment->employee_id);
        }

        public function test_hr_edit_employee_project_assignment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/project-assignment');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_employee_project_assignment_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'email' => '',
                    'project_id' => '',
                    'role_in_project' => '',
                    'assigned_date' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_employee_project_assignment_success(): void
        {
            $employeProjectAssignment = new EmployeeProjectAssigments();
            $employeProjectAssignment->employee_id = self::$employee->user_id;
            $employeProjectAssignment->project_id = 1;
            $employeProjectAssignment->role_in_project = 'Leader';
            $employeProjectAssignment->assigned_date = '2024-01-03';

            self::$hrRepository->saveEmployeeProjectAssignment($employeProjectAssignment);

            $id = self::$hrRepository->forTestEmployeeProjectAssignmentGetId($employeProjectAssignment);

            $response = $this->clientWithCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete : ' . $id, $body);
        }

        public function test_hr_delete_employee_project_assignment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/employee/project-assignment', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Company', $body);
        }

        public function test_hr_view_company_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_company_employee_projects_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/employee/projects/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_company_employee_projects_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/employee/projects/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_employee_projects_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/employee/projects');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Employee Projects', $body);
        }

        public function test_hr_view_company_employee_projects_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/employee/projects');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_employee_project_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'name' => $name,
                    'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!',
                    'start_date' => '2024-01-03',
                    'end_date' => '2024-02-03',
                    'status' => 'ongoing'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save company employee project : ' . $name, $body);

            self::$hrRepository->forTestCompanyEmployeeProjectDestroy($name);
        }

        public function test_hr_add_company_employee_project_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/project');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_employee_project_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'email' => '',
                    'project_id' => '',
                    'role_in_project' => '',
                    'assigned_date' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_company_employee_project_failed_because_data_has_been_there(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeProject = new CompanyEmployeeProjects();
            $companyEmployeeProject->name = $name;
            $companyEmployeeProject->description = 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!';
            $companyEmployeeProject->start_date = '2024-01-03';
            $companyEmployeeProject->end_date = '2024-02-03';
            $companyEmployeeProject->status = 'ongoing';

            self::$hrRepository->saveCompanyEmployeeProject($companyEmployeeProject);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'name' => $name,
                    'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!',
                    'start_date' => '2024-01-03',
                    'end_date' => '2024-02-03',
                    'status' => 'ongoing'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Project udah ada!', $body);

            self::$hrRepository->forTestCompanyEmployeeProjectDestroy($companyEmployeeProject->name);
        }

        public function test_hr_edit_company_employee_project_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeProject = new CompanyEmployeeProjects();
            $companyEmployeeProject->name = $name;
            $companyEmployeeProject->description = 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!';
            $companyEmployeeProject->start_date = '2024-01-03';
            $companyEmployeeProject->end_date = '2024-02-03';
            $companyEmployeeProject->status = 'ongoing';

            self::$hrRepository->saveCompanyEmployeeProject($companyEmployeeProject);

            $id = self::$hrRepository->forTestCompanyEmployeeProjectGetId($companyEmployeeProject);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'id' => $id,
                    'name' => $name,
                    'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!',
                    'start_date' => '2024-01-03',
                    'end_date' => '2024-04-03',
                    'status' => 'ongoing'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update company employee project : ' . $companyEmployeeProject->name, $body);

            self::$hrRepository->forTestCompanyEmployeeProjectDestroy($companyEmployeeProject->name);
        }

        public function test_hr_edit_company_employee_project_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/project');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_company_employee_project_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'id' => 1,
                    'email' => '',
                    'project_id' => '',
                    'role_in_project' => '',
                    'assigned_date' => '',
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_company_employee_project_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeProject = new CompanyEmployeeProjects();
            $companyEmployeeProject->name = $name;
            $companyEmployeeProject->description = 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Fugiat, libero!';
            $companyEmployeeProject->start_date = '2024-01-03';
            $companyEmployeeProject->end_date = '2024-02-03';
            $companyEmployeeProject->status = 'ongoing';

            self::$hrRepository->saveCompanyEmployeeProject($companyEmployeeProject);

            $id = self::$hrRepository->forTestCompanyEmployeeProjectGetId($companyEmployeeProject);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);
        }

        public function test_hr_delete_company_employee_project_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/project', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_company_employee_roles_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/employee/roles/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_company_employee_roles_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/employee/roles/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_employee_roles_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/employee/roles');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Employee Roles', $body);
        }

        public function test_hr_view_company_employee_roles_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/employee/roles');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_employee_role_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'name' => $name,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save company employee role : ' . $name, $body);

            self::$hrRepository->forTestCompanyEmployeeRoleDestroy($name);
        }

        public function test_hr_add_company_employee_role_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/role');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_employee_role_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'name' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_company_employee_role_failed_because_data_has_been_there(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeRole = new CompanyEmployeeRoles();
            $companyEmployeeRole->name = $name;

            self::$hrRepository->saveCompanyEmployeeRole($companyEmployeeRole);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'name' => $name,
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Role sudah ada', $body);

            self::$hrRepository->forTestCompanyEmployeeRoleDestroy($companyEmployeeRole->name);
        }

        public function test_hr_edit_company_employee_role_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeRole = new CompanyEmployeeRoles();
            $companyEmployeeRole->name = $name;

            self::$hrRepository->saveCompanyEmployeeRole($companyEmployeeRole);

            $id = self::$hrRepository->forTestCompanyEmployeeRoleGetId($companyEmployeeRole);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'id' => $id,
                    'name' => $name
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update company employee role : ' . $companyEmployeeRole->name, $body);

            self::$hrRepository->forTestCompanyEmployeeRoleDestroy($companyEmployeeRole->name);
        }

        public function test_hr_edit_company_employee_role_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/role');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_company_employee_role_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'id' => 1,
                    'name' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_company_employee_role_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyEmployeeRole = new CompanyEmployeeRoles();
            $companyEmployeeRole->name = $name;

            self::$hrRepository->saveCompanyEmployeeRole($companyEmployeeRole);

            $id = self::$hrRepository->forTestCompanyEmployeeRoleGetId($companyEmployeeRole);

            $response = $this->clientWithCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);
        }

        public function test_hr_delete_company_employee_role_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/employee/role', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_company_office_departments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/departments/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_company_office_departments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/departments/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_office_departments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/departments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Office Departments', $body);
        }

        public function test_hr_view_company_office_departments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/departments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_department_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'name' => $name,
                    'description' => $name . $name . $name
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save company office department : ' . $name, $body);

            self::$hrRepository->forTestCompanyOfficeDepartmentDestroy($name);
        }

        public function test_hr_add_company_office_department_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/department');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_department_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'name' => '',
                    'description' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_company_office_department_failed_because_data_has_been_there(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyOfficeDepartment = new CompanyOfficeDepartements();
            $companyOfficeDepartment->name = $name;
            $companyOfficeDepartment->description = $name . $name . $name;

            self::$hrRepository->saveCompanyOfficeDepartment($companyOfficeDepartment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'name' => $name,
                    'description' => $name . $name . $name
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Department udah ada!', $body);

            self::$hrRepository->forTestCompanyOfficeDepartmentDestroy($companyOfficeDepartment->name);
        }

        public function test_hr_edit_company_office_department_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyOfficeDepartment = new CompanyOfficeDepartements();
            $companyOfficeDepartment->name = $name;
            $companyOfficeDepartment->description = $name . $name . $name;

            self::$hrRepository->saveCompanyOfficeDepartment($companyOfficeDepartment);

            $id = self::$hrRepository->forTestCompanyOfficeDepartmentGetId($companyOfficeDepartment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'id' => $id,
                    'name' => $name,
                    'description' => $name . $name . $name
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update company office department : ' . $companyOfficeDepartment->name, $body);

            self::$hrRepository->forTestCompanyOfficeDepartmentDestroy($companyOfficeDepartment->name);
        }

        public function test_hr_edit_company_office_department_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/department');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_company_office_department_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'id' => 1,
                    'name' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_company_office_department_success(): void
        {
            $name = 'Lorem ipsum dolor sit.';

            $companyOfficeDepartment = new CompanyOfficeDepartements();
            $companyOfficeDepartment->name = $name;
            $companyOfficeDepartment->description = $name . $name . $name;

            self::$hrRepository->saveCompanyOfficeDepartment($companyOfficeDepartment);

            $id = self::$hrRepository->forTestCompanyOfficeDepartmentGetId($companyOfficeDepartment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);
        }

        public function test_hr_delete_company_office_department_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/department', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_company_office_financial_transactions_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/financial-transactions/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_company_office_financial_transactions_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/financial-transactions/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_office_financial_transactions_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/financial-transactions');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Financial Transactions', $body);
        }

        public function test_hr_view_company_office_financial_transactions_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/financial-transactions');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_financial_transaction_success(): void
        {
            $type = 'income';

            $companyOfficeFinancialTransaction = new CompanyOfficeFinancialTransactions();
            $companyOfficeFinancialTransaction->type = $type;
            $companyOfficeFinancialTransaction->amount = 123123;
            $companyOfficeFinancialTransaction->transaction_date = '2025-01-01';
            $companyOfficeFinancialTransaction->description = $type . $type . $type;

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'type' => $type,
                    'amount' => $companyOfficeFinancialTransaction->amount,
                    'transaction_date' => $companyOfficeFinancialTransaction->transaction_date,
                    'description' => $companyOfficeFinancialTransaction->description
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save company employee office financial transaction : ' . $type, $body);

            self::$hrRepository->forTestCompanyOfficeFinancialTransactionDestroy($companyOfficeFinancialTransaction);
        }

        public function test_hr_add_company_office_financial_transaction_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/financial-transaction');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_financial_transaction_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'type' => '',
                    'amount' => '',
                    'transaction_date' => '',
                    'description' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_company_office_financial_transaction_failed_because_data_has_been_there(): void
        {
            $type = 'income';

            $companyOfficeFinancialTransaction = new CompanyOfficeFinancialTransactions();
            $companyOfficeFinancialTransaction->type = $type;
            $companyOfficeFinancialTransaction->amount = 123123;
            $companyOfficeFinancialTransaction->transaction_date = '2025-01-01';
            $companyOfficeFinancialTransaction->description = $type . $type . $type;

            self::$hrRepository->saveCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'type' => $companyOfficeFinancialTransaction->type,
                    'amount' => $companyOfficeFinancialTransaction->amount,
                    'transaction_date' => $companyOfficeFinancialTransaction->transaction_date,
                    'description' => $companyOfficeFinancialTransaction->description
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Financial Transaction udah ada!', $body);

            self::$hrRepository->forTestCompanyOfficeFinancialTransactionDestroy($companyOfficeFinancialTransaction);
        }

        public function test_hr_edit_company_office_financial_transaction_success(): void
        {
            $type = 'income';

            $companyOfficeFinancialTransaction = new CompanyOfficeFinancialTransactions();
            $companyOfficeFinancialTransaction->type = $type;
            $companyOfficeFinancialTransaction->amount = 123123;
            $companyOfficeFinancialTransaction->transaction_date = '2025-01-01';
            $companyOfficeFinancialTransaction->description = $type . $type . $type;

            self::$hrRepository->saveCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction);

            $id = self::$hrRepository->forTestCompanyOfficeFinancialTransactionGetId($companyOfficeFinancialTransaction);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'id' => $id,
                    'type' => $type,
                    'amount' => 123123,
                    'transaction_date' => '2025-01-01',
                    'description' => $type . $type . $type
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update company employee office financial transaction : ' . $companyOfficeFinancialTransaction->type, $body);

            self::$hrRepository->forTestCompanyOfficeFinancialTransactionDestroy($companyOfficeFinancialTransaction);
        }

        public function test_hr_edit_company_office_financial_transaction_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/financial-transaction');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_company_office_financial_transaction_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'id' => 1,
                    'type' => '',
                    'amount' => '',
                    'transaction_date' => '',
                    'description' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_company_office_financial_transaction_success(): void
        {
            $type = 'income';

            $companyOfficeFinancialTransaction = new CompanyOfficeFinancialTransactions();
            $companyOfficeFinancialTransaction->type = $type;
            $companyOfficeFinancialTransaction->amount = 20000;
            $companyOfficeFinancialTransaction->transaction_date = '2025-01-01';
            $companyOfficeFinancialTransaction->description = $type . $type . $type;

            self::$hrRepository->saveCompanyOfficeFinancialTransaction($companyOfficeFinancialTransaction);

            $id = self::$hrRepository->forTestCompanyOfficeFinancialTransactionGetId($companyOfficeFinancialTransaction);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);
        }

        public function test_hr_delete_company_office_financial_transaction_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/financial-transaction', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_download_company_office_recruitments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/recruitments/download');

            $this->assertEquals(200, $response->getStatusCode());

            $this->assertTrue($response->hasHeader('Content-Type'));
            $this->assertEquals('application/pdf', $response->getHeaderLine('Content-Type'));

            $pdfContent = $response->getBody()->getContents();
            $this->assertNotEmpty($pdfContent);
        }

        public function test_hr_download_company_office_recruitments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/recruitments/download');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_view_company_office_recruitments_success(): void
        {
            $response = $this->clientWithCookies->request('GET', '/hr/company/office/recruitments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Recruitments', $body);
        }

        public function test_hr_view_company_office_recruitments_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('GET', '/hr/company/office/recruitments');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_recruitment_success(): void
        {
            $jobTitle = 'Job 123456';

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'job_title' => $jobTitle,
                    'department' => 1,
                    'job_description' => 'Description',
                    'status' => 'open'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success save company employee office recruitment : ' . $jobTitle, $body);

            self::$hrRepository->forTestCompanyOfficeRecruitmentDestroy($jobTitle);
        }

        public function test_hr_add_company_office_recruitment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/recruitment');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_add_company_office_recruitment_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'job_title' => '',
                    'department' => '',
                    'job_description' => '',
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_add_company_office_recruitment_failed_because_data_has_been_there(): void
        {
            $jobTitle = 'Job 123456';

            $companyOfficeRecruitment = new CompanyOfficeRecruitments();
            $companyOfficeRecruitment->job_title = $jobTitle;
            $companyOfficeRecruitment->department_id = 1;
            $companyOfficeRecruitment->job_description = 'Description';
            $companyOfficeRecruitment->status = 'open';

            self::$hrRepository->saveCompanyOfficeRecruitment($companyOfficeRecruitment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'job_title' => $jobTitle,
                    'department' => 1,
                    'job_description' => 'Description',
                    'status' => 'open'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Recruitment udah ada!', $body);

            self::$hrRepository->forTestCompanyOfficeRecruitmentDestroy($companyOfficeRecruitment->job_title);
        }

        public function test_hr_edit_company_office_recruitment_success(): void
        {
            $jobTitle = 'Job 123456';

            $companyOfficeRecruitment = new CompanyOfficeRecruitments();
            $companyOfficeRecruitment->job_title = $jobTitle;
            $companyOfficeRecruitment->department_id = 1;
            $companyOfficeRecruitment->job_description = 'Description';
            $companyOfficeRecruitment->status = 'open';

            self::$hrRepository->saveCompanyOfficeRecruitment($companyOfficeRecruitment);

            $id = self::$hrRepository->forTestCompanyOfficeRecruitmentGetId($companyOfficeRecruitment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'id' => $id,
                    'job_title' => $jobTitle,
                    'department' => 1,
                    'job_description' => 'Description',
                    'status' => 'closed'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success update company employee office recruitment : ' . $companyOfficeRecruitment->job_title, $body);

            self::$hrRepository->forTestCompanyOfficeRecruitmentDestroy($companyOfficeRecruitment->job_title);
        }

        public function test_hr_edit_company_office_recruitment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/recruitment');

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }

        public function test_hr_edit_company_office_recruitment_failed_because_validation(): void
        {
            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'id' => 1,
                    'job_title' => '',
                    'department' => '',
                    'job_description' => '',
                    'status' => ''
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('tidak boleh kosong', $body);
        }

        public function test_hr_delete_company_office_recruitment_success(): void
        {
            $jobTitle = 'Job 123456';

            $companyOfficeRecruitment = new CompanyOfficeRecruitments();
            $companyOfficeRecruitment->job_title = $jobTitle;
            $companyOfficeRecruitment->department_id = 1;
            $companyOfficeRecruitment->job_description = 'Description';
            $companyOfficeRecruitment->status = 'open';

            self::$hrRepository->saveCompanyOfficeRecruitment($companyOfficeRecruitment);

            $id = self::$hrRepository->forTestCompanyOfficeRecruitmentGetId($companyOfficeRecruitment);

            $response = $this->clientWithCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'id' => $id,
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Success delete', $body);
        }

        public function test_hr_delete_company_office_recruitment_failed_because_auth_middleware(): void
        {
            $response = $this->clientWithNoCookies->request('POST', '/hr/company/office/recruitment', [
                'form_params' => [
                    'id' => 'failed',
                    '_method' => 'DELETE'
                ]
            ]);

            $body = (string) $response->getBody();

            $this->assertStringContainsString('Login', $body);
        }
    }
}
