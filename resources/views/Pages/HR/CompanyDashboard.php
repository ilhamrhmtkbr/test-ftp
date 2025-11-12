<div class="container-fluid">
    <?= \ilhamrhmtkbr\App\Helper\Components\GenerateBreadcrumbHelper::getComponent() ?>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Explore</h6>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-body">
                    <h5>Employee</h5>
                    <div class="d-flex" style="gap: .5rem">
                        <a href="/hr/company/employee/projects" class="btn btn-sm btn-primary">
                            Projects
                        </a>
                        <a href="/hr/company/employee/roles" class="btn btn-sm btn-primary">
                            Roles
                        </a>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5>Office</h5>
                    <div class="d-flex" style="gap: .5rem">
                        <a href="/hr/company/office/departments" class="btn btn-sm btn-primary">
                            Departments
                        </a>
                        <a href="/hr/company/office/financial-transactions" class="btn btn-sm btn-primary">
                            Financial Transactions
                        </a>
                        <a href="/hr/company/office/recruitments" class="btn btn-sm btn-primary">
                            Recruitments
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>