<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Access Control Toggle
    |--------------------------------------------------------------------------
    */
    'enabled' => env('ACCESS_CONTROL_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'superadmin' => 'Full system access (Dictator)',
        'administrator' => 'Handles administration and security related works.',
        'user' => 'Default user role, can create and participate in events.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    | Using grouped structure with action.target format and hyphens for multi-words.
    | Each permission key serves as both identifier and description.
    */
    'permissions' => [
        'admin' => [
            'view.panel' => 'View Admin Panel',
            'manage.settings' => 'Manage System Settings',
            'access.telescope' => 'Access Telescope Debugging',
            'access.horizon' => 'Access Horizon Queue Monitor',
            'impersonate.users' => 'Impersonate Other Users',
        ],

        'users' => [
            'view.list' => 'View User List',
            'view.profile' => 'View User Profile',
            'create.account' => 'Create User Account',
            'update.account' => 'Update User Account',
            'delete.account' => 'Delete User Account',
        ],

        'roles' => [
            'view.list' => 'View Role List',
            'create.role' => 'Create New Role',
            'update.role' => 'Update Role Permissions',
            'delete.role' => 'Delete Role',
        ],

        'security' => [
            'manage.access-control' => 'Manage Access Control',
            'view.audit-logs' => 'View Audit Logs',
        ],

        'media' => [
            'access.management' => 'Access Media Management',
            'upload.files' => 'Upload Media Files',
            'delete.files' => 'Delete Media Files',
        ],

        'profile' => [
            'view.own' => 'View Own Profile',
            'update.own' => 'Update Own Profile',
        ],

        'notifications' => [
            'view.own' => 'View Own Notifications',
            'update.own' => 'Update Own Notification Settings',
            'mark.read' => 'Mark Notifications as Read',
        ],

        'dashboard' => [
            'access.user' => 'Access User Dashboard',
            'access.admin' => 'Access Admin Dashboard',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Scopes
    |--------------------------------------------------------------------------
    | Define what each role can access using the new permission structure.
    | Format: module.action.target
    */
    'role_scope' => [
        'superadmin' => '*', // All permissions

        'administrator' => [
            // Admin Panel Access
            'admin.view.panel',
            'admin.manage.settings',
            'admin.access.telescope',
            'admin.access.horizon',
            'admin.impersonate.users',

            // User Management
            'users.view.list',
            'users.view.profile',
            'users.create.account',
            'users.update.account',
            'users.delete.account',

            // Role Management
            'roles.view.list',
            'roles.create.role',
            'roles.update.role',
            'roles.delete.role',

            // Security
            'security.manage.access-control',
            'security.view.audit-logs',

            // Media Management
            'media.access.management',
            'media.upload.files',
            'media.delete.files',

            // Dashboard
            'dashboard.access.admin',
        ],

        'user' => [
            'dashboard.access.user',
            'profile.view.own',
            'profile.update.own',
            'notifications.view.own',
            'notifications.update.own',
            'notifications.mark.read',
        ],
    ],
];
