<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once app_path('Services/Admin/HospitalInfo.php');
require_once app_path('Services/Admin/Department.php');
require_once app_path('Services/Admin/ServiceFee.php');
require_once app_path('Services/Admin/UserManager.php');
require_once app_path('Services/Admin/RolePermission.php');
require_once app_path('Services/Admin/AuditLog.php');
require_once app_path('Services/Admin/BackupManager.php');

class AdminController extends Controller
{
    public function dashboard()
    {
        $hospitalModel = new \HospitalInfo();
        $deptModel = new \Department();
        $feeModel = new \ServiceFee();
        $userModel = new \UserManager();
        $roleModel = new \RolePermission();
        $auditModel = new \AuditLog();
        $backupModel = new \BackupManager();

        $hospitalInfo = $hospitalModel->get();
        $departments = $deptModel->getAll();
        $serviceFees = $feeModel->getAll();
        $users = $userModel->getAll();
        $roles = $roleModel->getRoles();
        $recentLogs = $auditModel->getActivityLogs(20);
        $backups = $backupModel->getBackupHistory();
        $currentUser = \Session::getCurrentUser();

        return view('admin.dashboard', compact(
            'hospitalInfo',
            'departments',
            'serviceFees',
            'users',
            'roles',
            'recentLogs',
            'backups',
            'currentUser'
        ));
    }
}
