<?php

use ilhamrhmtkbr\App\Helper\NumberHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="list-group">
                        <a href="/employee/attendance" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Attendance</h5>
                            <p class="mb-1">Lihat riwayat kehadiran, absen, dan lainnya</p>
                        </a>
                        <a href="/employee/contracts" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Contracts</h5>
                            <p class="mb-1">Lama kerja di perusahaan. Status kerja</p>
                        </a>
                        <a href="/employee/leave-requests" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Leave Requests</h5>
                            <p class="mb-1">Pengajuan cuti untuk keperluan mendesak</p>
                        </a>
                        <a href="/employee/overtime" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Overtime</h5>
                            <p class="mb-1">Hitungan lemburan dan perolehan tambahan uang</p>
                        </a>
                        <a href="/employee/payrolls" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Payrolls</h5>
                            <p class="mb-1">Penghasilan per bulan</p>
                        </a>
                        <a href="/employee/project-assignments" class="list-group-item list-group-item-action">
                            <h5 class="mb-1">Project Assignments</h5>
                            <p class="mb-1">Proposal proyek yang diajukan</p>
                        </a>
                    </div>
                </div>
                <div class="col-md-8">
                    <h5 class="card-title">Employee</h5>
                    <form>
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" value="<?= $data['name'] ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" value="<?= $data['email'] ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" class="form-control" value="<?= $data['role'] ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" value="<?= $data['department'] ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="text" class="form-control" value="<?= NumberHelper::convertNumberToRupiah($data['salary']) ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Hire Date</label>
                            <input type="text" class="form-control" value="<?= TimeHelper::getTime($data['hire_date']) ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <input type="text" class="form-control" value="<?= $data['status'] ?>" readonly/>
                        </div>
                        <div class="form-group">
                            <label>Created At</label>
                            <input type="text" class="form-control" value="<?= TimeHelper::getTime($data['created_at']) ?>" readonly/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>