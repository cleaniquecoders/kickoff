<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/port/admin/-index', 'admin/Index')->name('port.admin.index'); // or Route::get(..., [AdminController::class, 'index'])
Route::inertia('/port/admin/settings/-index', 'admin/settings/Index')->name('port.admin.settingsIndex'); // or Route::get(..., [AdminController::class, 'settingsIndex'])
Route::inertia('/port/admin/mail-history/-index', 'admin/mailHistory/Index')->name('port.admin.mailHistoryIndex'); // or Route::get(..., [AdminController::class, 'mailHistoryIndex'])
Route::inertia('/port/admin/roles/-index', 'admin/roles/Index')->name('port.admin.rolesIndex'); // or Route::get(..., [AdminController::class, 'rolesIndex'])
Route::inertia('/port/admin/roles/-role-form', 'admin/roles/RoleForm')->name('port.admin.roleForm'); // or Route::get(..., [AdminController::class, 'roleForm'])
Route::inertia('/port/admin/roles/-show', 'admin/roles/Show')->name('port.admin.rolesShow'); // or Route::get(..., [AdminController::class, 'rolesShow'])
Route::inertia('/port/admin/settings/-authentication', 'admin/settings/Authentication')->name('port.admin.authentication'); // or Route::get(..., [AdminController::class, 'authentication'])
Route::inertia('/port/admin/settings/-g8desk', 'admin/settings/G8desk')->name('port.admin.g8desk'); // or Route::get(..., [AdminController::class, 'g8desk'])
Route::inertia('/port/admin/settings/-seo', 'admin/settings/Seo')->name('port.admin.seo'); // or Route::get(..., [AdminController::class, 'seo'])
Route::inertia('/port/admin/settings/-show', 'admin/settings/Show')->name('port.admin.settingsShow'); // or Route::get(..., [AdminController::class, 'settingsShow'])

Route::inertia('/port/auth/-confirm-password', 'auth/ConfirmPassword')->name('port.auth.confirmPassword'); // or Route::get(..., [AuthController::class, 'confirmPassword'])
Route::inertia('/port/auth/-forgot-password', 'auth/ForgotPassword')->name('port.auth.forgotPassword'); // or Route::get(..., [AuthController::class, 'forgotPassword'])
Route::inertia('/port/auth/-login', 'auth/Login')->name('port.auth.login'); // or Route::get(..., [AuthController::class, 'login'])
Route::inertia('/port/auth/-register', 'auth/Register')->name('port.auth.register'); // or Route::get(..., [AuthController::class, 'register'])
Route::inertia('/port/auth/-reset-password', 'auth/ResetPassword')->name('port.auth.resetPassword'); // or Route::get(..., [AuthController::class, 'resetPassword'])
Route::inertia('/port/auth/-two-factor-challenge', 'auth/TwoFactorChallenge')->name('port.auth.twoFactorChallenge'); // or Route::get(..., [AuthController::class, 'twoFactorChallenge'])
Route::inertia('/port/auth/-verify-email', 'auth/VerifyEmail')->name('port.auth.verifyEmail'); // or Route::get(..., [AuthController::class, 'verifyEmail'])

Route::inertia('/port/dashboard', 'Dashboard')->name('port.dashboard.dashboard'); // or Route::get(..., [DashboardController::class, 'dashboard'])

Route::inertia('/port/notifications/-bell', 'notifications/Bell')->name('port.notifications.bell'); // or Route::get(..., [NotificationsController::class, 'bell'])
Route::inertia('/port/notifications/-index', 'notifications/Index')->name('port.notifications.index'); // or Route::get(..., [NotificationsController::class, 'index'])

Route::inertia('/port/security/audit-trail/-index', 'security/auditTrail/Index')->name('port.security.auditTrailIndex'); // or Route::get(..., [SecurityController::class, 'auditTrailIndex'])
Route::inertia('/port/security/-role-permissions', 'security/RolePermissions')->name('port.security.rolePermissions'); // or Route::get(..., [SecurityController::class, 'rolePermissions'])
Route::inertia('/port/security/users/-index', 'security/users/Index')->name('port.security.usersIndex'); // or Route::get(..., [SecurityController::class, 'usersIndex'])
Route::inertia('/port/security/users/-manage-access', 'security/users/ManageAccess')->name('port.security.manageAccess'); // or Route::get(..., [SecurityController::class, 'manageAccess'])
Route::inertia('/port/security/users/-user-form', 'security/users/UserForm')->name('port.security.userForm'); // or Route::get(..., [SecurityController::class, 'userForm'])
Route::inertia('/port/security/audit-trail/-detail', 'security/auditTrail/Detail')->name('port.security.detail'); // or Route::get(..., [SecurityController::class, 'detail'])
Route::inertia('/port/security/audit-trail/-show', 'security/auditTrail/Show')->name('port.security.auditTrailShow'); // or Route::get(..., [SecurityController::class, 'auditTrailShow'])

Route::inertia('/port/settings/-appearance', 'settings/Appearance')->name('port.settings.appearance'); // or Route::get(..., [SettingsController::class, 'appearance'])
Route::inertia('/port/settings/-delete-user-form', 'settings/DeleteUserForm')->name('port.settings.deleteUserForm'); // or Route::get(..., [SettingsController::class, 'deleteUserForm'])
Route::inertia('/port/settings/-mcp-tokens', 'settings/McpTokens')->name('port.settings.mcpTokens'); // or Route::get(..., [SettingsController::class, 'mcpTokens'])
Route::inertia('/port/settings/-password', 'settings/Password')->name('port.settings.password'); // or Route::get(..., [SettingsController::class, 'password'])
Route::inertia('/port/settings/-profile', 'settings/Profile')->name('port.settings.profile'); // or Route::get(..., [SettingsController::class, 'profile'])
Route::inertia('/port/settings/-two-factor', 'settings/TwoFactor')->name('port.settings.twoFactor'); // or Route::get(..., [SettingsController::class, 'twoFactor'])
Route::inertia('/port/settings/two-factor/-recovery-codes', 'settings/twoFactor/RecoveryCodes')->name('port.settings.recoveryCodes'); // or Route::get(..., [SettingsController::class, 'recoveryCodes'])

Route::inertia('/port/mail/-default-mail', 'mail/DefaultMail')->name('port.mail.defaultMail'); // or Route::get(..., [MailController::class, 'defaultMail'])
Route::inertia('/port/mail/-notification', 'mail/Notification')->name('port.mail.notification'); // or Route::get(..., [MailController::class, 'notification'])

Route::inertia('/port/pages/-changelog', 'pages/Changelog')->name('port.pages.changelog'); // or Route::get(..., [PagesController::class, 'changelog'])
Route::inertia('/port/pages/-documentation', 'pages/Documentation')->name('port.pages.documentation'); // or Route::get(..., [PagesController::class, 'documentation'])
Route::inertia('/port/pages/-support', 'pages/Support')->name('port.pages.support'); // or Route::get(..., [PagesController::class, 'support'])

Route::inertia('/port/welcome', 'Welcome')->name('port.welcome.welcome'); // or Route::get(..., [WelcomeController::class, 'welcome'])
