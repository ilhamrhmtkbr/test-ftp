<?php

use ilhamrhmtkbr\App\Helper\NumberHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

?>
<section>
    <h2>Employee</h2>
    <div class="card-wrapper">
        <p class="employee__data__title">Personal</p>
        <div class="employee__data__box">
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Name</div>
                <div class="employee__data__item__value"><?= $data['name'] ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Email</div>
                <div class="employee__data__item__value"><?= $data['email'] ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Role</div>
                <div class="employee__data__item__value"><?= $data['role'] ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Department</div>
                <div class="employee__data__item__value"><?= $data['department'] ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Salary</div>
                <div class="employee__data__item__value"><?= NumberHelper::convertNumberToRupiah($data['salary']) ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Hire Date</div>
                <div class="employee__data__item__value"><?= TimeHelper::getTime($data['hire_date']) ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Status</div>
                <div class="employee__data__item__value"><?= $data['status'] ?> </div>
            </div>
            <div class="employee__data__wrapper">
                <div class="employee__data__item__key">Created At</div>
                <div class="employee__data__item__value"><?= TimeHelper::getTime($data['created_at']) ?> </div>
            </div>
        </div>
    </div>

    <div class="card-wrapper">
        <h3>Employee</h3>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: var(--m);">
            <a href="/employee/attendance" class="button btn-primary">
                Attendance
            </a>
            <a href="/employee/contracts" class="button btn-primary">
                Contracts
            </a>
            <a href="/employee/leave-requests" class="button btn-primary">
                Leave Requests
            </a>
            <a href="/employee/overtime" class="button btn-primary">
                Overtime
            </a>
            <a href="/employee/payrolls" class="button btn-primary">
                Payrolls
            </a>
            <a href="/employee/project-assignments" class="button btn-primary">
                Project Assignments
            </a>
        </div>
    </div>
</section>