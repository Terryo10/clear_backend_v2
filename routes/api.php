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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RequestProposalController;
use App\Http\Controllers\User\KeyFactorController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ProjectOfferController;
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

Route::middleware(['Auth:sanctum', AdminMiddleware::class])->group(function () {
    //admin-only routes here
    Route::resource('/admin/frequency', FrequencyController::class);
    Route::post('/update-service', [ServiceController::class, 'update']);
    Route::resource('/admin/service', ServiceController::class);
    Route::resource('/admin/projects', ProjectController::class);
    Route::post('/admin/update-project-status', [ProjectController::class, 'updateStatus']);
    Route::get('/admin/requests', [ProjectController::class, 'getRequests']);
    Route::post('admin/project-images', [ProjectImagesController::class, 'uploadImagesFromAdmin']);
    Route::get('admin/key_factors',[KeyFactorsController::class, 'index']);
    Route::put('admin/key_factors/{id}',[KeyFactorsController::class, 'update']);
    Route::post('admin/key_factors',[KeyFactorsController::class, 'store']);
    Route::delete('admin/key_factors/{id}',[KeyFactorsController::class, 'destroy']);
    Route::resource('admin/users', UserController::class);
    Route::post('admin/remove-contractor-request-for-proposal', [ProjectController::class,'removeContractorRequestForProposal']);
    Route::post('admin/send-proposal-to-contractor',[ProjectController::class, 'sendProjectToContractors']);
    Route::get('admin/contractors',[UserController::class, 'getContractors']);
    Route::prefix('admin/offers')->group(function () {
        Route::post('/', [ProjectOfferController::class, 'store']);
        Route::get('/{projectOffer}', [ProjectOfferController::class, 'show']);
        Route::post('/sign-offer-admin', [ProjectOfferController::class, 'accept']);
        Route::delete('/delete/{id}', [ProjectOfferController::class, 'delete']);
        Route::put('/update/{id}', [ProjectOfferController::class, 'update']);
    });
    Route::get('admin/project-search', [ProjectController::class, 'search']);
    Route::resource('/sliders', SliderController::class);
    Route::post('/admin/payment-status', [ProjectController::class, 'changePaymentStatus']);
    Route::get('admin-dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])->name('admin');
});

//USER
Route::middleware(['Auth:sanctum'])->group(function () {
    Route::get('services',[ServiceController::class, 'index']);
    Route::get('key_factors',[KeyFactorController::class, 'getKeyFactors']);
    Route::get('frequencies',[KeyFactorController::class, 'getFrequencies']);
    Route::get('updateProfilePicture',[ProfileController::class, 'updateProfilePicture']);
    Route::get('updatePassword',[ProfileController::class, 'updateUserPassword']);
    Route::post('request_service', [\App\Http\Controllers\User\ProjectController::class, 'createProject']);
    Route::get('user_projects',[\App\Http\Controllers\User\ProjectController::class, 'clientProjects']);
    Route::get('user_requests',[\App\Http\Controllers\User\ProjectController::class, 'clientRequests']);
    Route::post('upload-images',[ProjectController::class, 'uploadImagesFromAdmin']);
    Route::post('/sign-offer/{id}', [ProjectOfferController::class, 'sign']);
    Route::get('notifications', [NotificationsController::class, 'getNotifications']);
    Route::post('project-rating', [ProjectController::class, 'rate']);
    Route::get('user-dashboard', [\App\Http\Controllers\DashboardController::class, 'user'])->name('user');


});

//Contractor

Route::middleware(['Auth:sanctum', ContractorMiddleware::class])->group(function () {
    Route::post('contractor/sent-proposal-to-user', [ProjectController::class,'sendProposal']);
    Route::get('contractor/projects', [ContractorProjectController::class,'contactorProjects']);
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
            Route::patch('/update-proposal', [ProposalController::class, 'update']);
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

Route::group(['prefix' => 'chatRequests', 'middleware' => 'auth:sanctum'], function () {
    Route::post('/', [ManagerChatController::class, 'store']);
    Route::get('/{managerChat}', [ManagerChatController::class, 'show']);
    Route::get('/', [ManagerChatController::class, 'index']);
    Route::post('/{managerChat}/messages', [ManagerChatMessageController::class, 'store']);
});
