<?php

use App\Http\Controllers\Admin\FrequencyController;
use App\Http\Controllers\Admin\KeyFactorsController;
use App\Http\Controllers\Admin\NotificationsController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectImagesController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\loginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Contractor\ContractorProjectController;
use App\Http\Controllers\ManagerChatController;
use App\Http\Controllers\ManagerChatMessageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentInstructionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RequestProposalController;
use App\Http\Controllers\User\KeyFactorController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ProjectOfferController;
use App\Http\Controllers\ProjectRatingController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\VerificationController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ContractorMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('Auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [loginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware(['Auth:sanctum'])->group(function () {
    Route::post('/logout', [loginController::class, 'logout']);
});



Route::prefix('auth')->group(function () {
    //auth routes
    Route::post('change-password', [LoginController::class, 'changePassword'])->middleware(['Auth:sanctum', 'verified']);
    Route::post('forgot-password', [LoginController::class, 'forgotPassword'])->name('passwords.sent');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/refresh', [LoginController::class, 'refreshUser'])->middleware('Auth:sanctum');
    Route::get('/profile', [UserProfileController::class, 'index'])->middleware(['Auth:sanctum', 'verified']);
    Route::post('/profile', [UserProfileController::class, 'update']);
    Route::post('/profile/password', [UserProfileController::class, 'updatePasswordApi'])->middleware(['Auth:sanctum', 'verified']);
    Route::post('/profile/username', [UserProfileController::class, 'updateEmailApi'])->middleware(['Auth:sanctum', 'verified']);
    Route::post('/profile/picture', [UserProfileController::class, 'updateProfilePictureApi'])->middleware(['Auth:sanctum', 'verified']);
    Route::get('/email/verify/{id}', [VerificationController::class, 'emailVerify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resendEmailVerification'])->middleware(['Auth:sanctum', 'throttle:6,1'])->name('verification.send');
});

Route::middleware(['Auth:sanctum', AdminMiddleware::class])->group(function () {
    //admin-only routes here
    Route::post('admin/payment-status', [ProjectController::class, 'changePaymentStatus']);
    Route::post('admin/add-payment-link', [ProjectController::class, 'setPaymentLink']);
    Route::resource('/admin/frequency', FrequencyController::class);
    Route::post('/update-service', [ServiceController::class, 'update']);
    Route::resource('/admin/service', ServiceController::class);
    Route::resource('/admin/projects', ProjectController::class);
    Route::post('/admin/update-project-status', [ProjectController::class, 'updateStatus']);
    Route::post('/admin/update-project', [ProjectController::class, 'updateProject']);
    Route::get('/admin/requests', [ProjectController::class, 'getRequests']);
    Route::post('admin/project-images', [ProjectImagesController::class, 'uploadImagesFromAdmin']);
    Route::get('admin/key_factors', [KeyFactorsController::class, 'index']);
    Route::put('admin/key_factors/{id}', [KeyFactorsController::class, 'update']);
    Route::post('admin/key_factors', [KeyFactorsController::class, 'store']);
    Route::delete('admin/key_factors/{id}', [KeyFactorsController::class, 'destroy']);
    Route::resource('admin/users', UserController::class);
    Route::post('admin/users/edit', [UserController::class, 'updateEdit']);
    Route::post('admin/users/password/{id}', [UserController::class, 'update']);
    Route::post('admin/remove-contractor-request-for-proposal', [ProjectController::class, 'removeContractorRequestForProposal']);
    Route::post('admin/send-proposal-to-contractor', [ProjectController::class, 'sendProjectToContractors']);
    Route::get('admin/contractors', [UserController::class, 'getContractors']);
    Route::prefix('admin/offers')->group(function () {
        Route::post('/', [ProjectOfferController::class, 'store']);
        Route::get('/{projectOffer}', [ProjectOfferController::class, 'show']);
        Route::post('/sign-offer-admin', [ProjectOfferController::class, 'accept']);
        Route::delete('/delete/{id}', [ProjectOfferController::class, 'delete']);
        Route::post('/update/{id}', [ProjectOfferController::class, 'update']);
    });
    Route::get('admin/project-search', [ProjectController::class, 'search']);
    Route::resource('/sliders', SliderController::class);
    Route::get('admin-dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('admin');


    //ADMIN CHAT ROUTES
    Route::post('admin/messages/{chat}', [MessageController::class, 'adminSendMessage']);
    Route::prefix('admin/chats')->group(function () {
        Route::get('/', [ChatController::class, 'adminIndex']);
        Route::post('/refresh', [ChatController::class, 'refresh']);
        Route::post('/join/{chat}', [ChatController::class, 'joinChat']);
        Route::post('/{chat}/users', [ChatController::class, 'addUsers']);
        Route::delete('/{chat}/users', [ChatController::class, 'removeUsers']);
        Route::delete('/{chat}', [ChatController::class, 'delete']);
    });

    Route::prefix('admin/messages')->group(function () {
        Route::post('/join/{chat}', [ChatController::class, 'joinChat']);
        Route::post('/delete/message', [ChatController::class, 'deleteMessage']);
    });

    Route::prefix('admin/notifications')->group(function () {
        Route::get('', [NotificationController::class, 'getAdminNotifications']);
        Route::get('getUserNotifications', [NotificationController::class, 'getUserNotifications']);
        Route::post('readAll', [NotificationController::class, 'readAllNotifications']);
        Route::post('readAllAdmin', [NotificationController::class, 'readAllAdminNotifications']);
        Route::post('read', [NotificationController::class, 'readNotification']);
    });

    Route::prefix('admin/chatRequests')->group(function () {
        Route::post('/{managerChat}', [ManagerChatController::class, 'acceptChatRequest']);
        Route::get('/{managerChat}', [ManagerChatController::class, 'show']);
    });
    Route::post('admin/chatRequests', [ManagerChatController::class, 'storeWeb']);
    Route::post('send-message-admin-manager', [ManagerChatMessageController::class, 'adminSendMessage']);
    Route::get('search-admin', [UserController::class, 'search']);
});

Route::get('messages/{chat}', [ChatController::class, 'getChatMessages'])->middleware('Auth:sanctum');
Route::get('messages-manager/{chat}', [ChatController::class, 'getManagerChatMessages'])->middleware('Auth:sanctum');

//USER
Route::middleware(['Auth:sanctum'])->group(function () {
    Route::get('refresh-user', [LoginController::class, 'refreshUser']);
    Route::post('delete-project-file/{id}', [ProjectImagesController::class, 'deleteProjectFile']);
    Route::get('paymentInstructions', [PaymentInstructionController::class, 'index']);
    Route::get('view_project/{id}', [ProjectController::class, 'show']);
    Route::post('paymentInstructions', [PaymentInstructionController::class, 'store']);
    Route::delete('paymentInstructions/{paymentInstruction}', [PaymentInstructionController::class, 'destroy']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::post('userProfile', [UserController::class, 'getUser']);
    Route::get('key_factors', [KeyFactorController::class, 'getKeyFactors']);
    Route::get('frequencies', [KeyFactorController::class, 'getFrequencies']);
    Route::prefix('properties')->group(function () {
        Route::get('/',  [KeyFactorController::class, 'all'])->name('properties');
    });
    Route::post('updateProfilePicture', [ProfileController::class, 'updateProfilePicture']);
    Route::post('updatePassword', [ProfileController::class, 'updateUserPassword']);
    Route::post('request_service', [\App\Http\Controllers\User\ProjectController::class, 'createProject']);
    Route::get('user_projects', [\App\Http\Controllers\User\ProjectController::class, 'clientProjects']);
    Route::get('user_requests', [\App\Http\Controllers\User\ProjectController::class, 'clientRequests']);
    Route::post('upload-images', [ProjectImagesController::class, 'uploadImagesFromAdmin']);
    Route::post('/sign-offer/{id}', [ProjectOfferController::class, 'sign']);
    Route::get('notifications', [NotificationsController::class, 'getNotifications']);
    Route::post('project-rating', [ProjectController::class, 'rate']);
    Route::get('user-dashboard', [\App\Http\Controllers\DashboardController::class, 'user'])->name('user');
    Route::post('user-profile', [ProfileController::class, 'update']);
    Route::post('project/feedback', [ProjectRatingController::class, 'store']);

    Route::group(['prefix' => 'chats',], function () {
        Route::post('/{project}', [ChatController::class, 'store']);
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/{chat}/messages', [MessageController::class, 'store']);
        Route::get('/{chat}', [ChatController::class, 'show']);
    });

    //for contractor and user
    Route::group(['prefix' => 'chatRequests'], function () {
        Route::post('/', [ManagerChatController::class, 'store']);
        Route::get('/{managerChat}', [ManagerChatController::class, 'show']);
        Route::get('/', [ManagerChatController::class, 'index']);
        Route::post('/{managerChat}/messages', [ManagerChatMessageController::class, 'store']);
    });
});

//Contractor

Route::middleware(['Auth:sanctum', ContractorMiddleware::class])->group(function () {
    Route::post('contractor/sent-proposal-to-user', [ProjectController::class, 'sendProposal']);
    Route::get('contractor/project/{id}', [ContractorProjectController::class, 'contactorProject']);
    Route::get('contractor/projects', [ContractorProjectController::class, 'contactorProjects']);

    Route::prefix('contractor')->group(function () {
        Route::get('/', [\App\Http\Controllers\DashboardController::class, 'contractor'])->name('contractor');

        Route::prefix('chats')->group(function () {
            Route::get('/', [ChatController::class, 'contractorIndex']);
            Route::post('/request-chat', [ManagerChatController::class, 'storeContractorChatRequest']);
            Route::post('/{managerChat}/messages', [ManagerChatMessageController::class, 'storeContractorMessage']);
            Route::post('/refresh', [ChatController::class, 'refresh']);
        });

        Route::prefix('messages')->group(function () {
            Route::post('/{chat}', [MessageController::class, 'contractorSendMessage']);
        });

        Route::prefix('requests')->group(function () {
            Route::get('/', [RequestProposalController::class, 'index'])->name('contractor.requests');
            Route::get('/{id}', [ProjectController::class, 'contactorRequest'])->name('contractor.request');
            Route::post('/send-proposal', [ProposalController::class, 'store']);
            Route::post('/update-proposal', [ProposalController::class, 'update']);
            Route::post('/withdraw-proposal', [ProposalController::class, 'destroy']);
        });
        Route::prefix('projects')->group(function () {
            Route::get('/', [ProjectController::class, 'contactorProjects'])->name('contractor.projects');
        });
        Route::prefix('notifications')->group(function () {
            Route::get('', [NotificationController::class, 'getNotifications']);
        });
    });
});
