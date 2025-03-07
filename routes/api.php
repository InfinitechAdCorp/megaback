<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\LocationController;

use App\Http\Controllers\SeminarController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ClosedDealController;
use App\Http\Controllers\RealEstateNewsController;
use App\Http\Controllers\RealEstateTipsController;
use App\Http\Controllers\OngoingInfrastructureController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PushNotificationController;


Route::post('/save-subscription', [PushNotificationController::class, 'saveSubscription']);
Route::post('/send-notification', [PushNotificationController::class, 'sendNotification']);

Route::prefix('count')->group(function () {
    Route::get('/property', [PropertyController::class, 'getPropertyStatistics']); // Get all seminars
    Route::get('/office', [OfficeController::class, 'getOfficeStatistics']);
    Route::get('/agent', [AgentController::class, 'getAgentStatistics']);
    Route::get('/seminar', [SeminarController::class, 'getSeminarStatistics']);
    Route::get('/meeting', [MeetingController::class, 'getMeetingStatistics']);
    Route::get('/event', [EventController::class, 'getEventStatistics']);
    Route::get('/closedDeal', [ClosedDealController::class, 'getClosedDealStatistics']);
    Route::get('/realEstateNews', [RealEstateNewsController::class, 'getRealEstateNewsStatistics']);
    Route::get('/realEstateTips', [RealEstateTipsController::class, 'getRealEstateTipsStatistics']);
    Route::get('/ongoingInfrastructure', [OngoingInfrastructureController::class, 'getOngoingInfrastructureStatistics']);
});
Route::prefix('property')->group(function () {
    Route::get('/', [PropertyController::class, 'index']); // Get all seminars
    Route::post('/', [PropertyController::class, 'store']); // Create a seminar
    Route::get('/{id}', [PropertyController::class, 'show']); // Get a single seminar
    Route::post('/updateProperty/{id}', [PropertyController::class, 'updateProperty']);
    Route::post('/updateFeature/{id}', [PropertyController::class, 'updateFeatures']); 
     Route::post('/updateAmenities/{id}', [PropertyController::class, 'updateAmenities']);
    Route::delete('/{id}', [PropertyController::class, 'destroy']); // Delete a seminar
});
Route::prefix('office')->group(function () {
    Route::get('/', [OfficeController::class, 'index']); // Get all seminars
    Route::post('/', [OfficeController::class, 'store']); // Create a seminar
    Route::get('/{id}', [OfficeController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [OfficeController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [OfficeController::class, 'destroy']); // Delete a seminar
});

Route::prefix('agent')->group(function () {
    Route::get('/', [AgentController::class, 'index']); // Get all seminars
    Route::post('/', [AgentController::class, 'store']); // Create a seminar
    Route::get('/{id}', [AgentController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [AgentController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [AgentController::class, 'destroy']); // Delete a seminar
});
/*                    CUSTOM SERVICES                    */

Route::prefix('career')->group(function () {
    Route::get('/', [CareerController::class, 'index']); // Get all seminars
    Route::post('/', [CareerController::class, 'store']); // Create a seminar
    Route::get('/{id}', [CareerController::class, 'show']); // Get a single seminar
    Route::put('/{id}', [CareerController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [CareerController::class, 'destroy']); // Delete a seminar
});
Route::prefix('testimonial')->group(function () {
    Route::get('/', [TestimonialController::class, 'index']); // Get all seminars
    Route::post('/', [TestimonialController::class, 'store']); // Create a seminar
    Route::get('/{id}', [TestimonialController::class, 'show']); // Get a single seminar
    Route::put('/{id}', [TestimonialController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [TestimonialController::class, 'destroy']); // Delete a seminar
});

/*                    WHATS NEW                     */
Route::prefix('seminar')->group(function () {
    Route::get('/', [SeminarController::class, 'index']); // Get all seminars
    Route::post('/', [SeminarController::class, 'store']); // Create a seminar
    Route::get('/{id}', [SeminarController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [SeminarController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [SeminarController::class, 'destroy']); // Delete a seminar
});
    Route::prefix('meeting')->group(function () {
        Route::get('/', [MeetingController::class, 'index']); // Get all seminars
        Route::post('/', [MeetingController::class, 'store']); // Create a seminar
        Route::get('/{id}', [MeetingController::class, 'show']); // Get a single seminar
        Route::post('/{id}', [MeetingController::class, 'update']); // Update a seminar (using POST)
        Route::delete('/{id}', [MeetingController::class, 'destroy']); // Delete a seminar
    });
Route::prefix('event')->group(function () {
    Route::get('/', [EventController::class, 'index']); // Get all seminars
    Route::post('/', [EventController::class, 'store']); // Create a seminar
    Route::get('/{id}', [EventController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [EventController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [EventController::class, 'destroy']); // Delete a seminar
});

Route::prefix('closedDeal')->group(function () {
    Route::get('/', [ClosedDealController::class, 'index']); // Get all seminars
    Route::post('/', [ClosedDealController::class, 'store']); // Create a seminar
    Route::get('/{id}', [ClosedDealController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [ClosedDealController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [ClosedDealController::class, 'destroy']); // Delete a seminar
});
Route::prefix('realEstateNews')->group(function () {
    Route::get('/', [RealEstateNewsController::class, 'index']); // Get all seminars
    Route::post('/', [RealEstateNewsController::class, 'store']); // Create a seminar
    Route::get('/{id}', [RealEstateNewsController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [RealEstateNewsController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [RealEstateNewsController::class, 'destroy']); // Delete a seminar
});
Route::prefix('realEstateTips')->group(function () {
    Route::get('/', [RealEstateTipsController::class, 'index']); // Get all seminars
    Route::post('/', [RealEstateTipsController::class, 'store']); // Create a seminar
    Route::get('/{id}', [RealEstateTipsController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [RealEstateTipsController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [RealEstateTipsController::class, 'destroy']); // Delete a seminar
});

Route::prefix('ongoingInfrastructure')->group(function () {
    Route::get('/', [OngoingInfrastructureController::class, 'index']); // Get all seminars
    Route::post('/', [OngoingInfrastructureController::class, 'store']); // Create a seminar
    Route::get('/{id}', [OngoingInfrastructureController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [OngoingInfrastructureController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [OngoingInfrastructureController::class, 'destroy']); // Delete a seminar
});
Route::prefix('video')->group(function () {
    Route::get('/', [VideoController::class, 'index']); // Get all seminars
    Route::post('/', [VideoController::class, 'store']); // Create a seminar
    Route::get('/{id}', [VideoController::class, 'show']); // Get a single seminar
    Route::post('/{id}', [VideoController::class, 'update']); // Update a seminar (using POST)
    Route::delete('/{id}', [VideoController::class, 'destroy']); // Delete a seminar
});



/*                    FORM FILLER                     */
Route::prefix('status')->group(function () {
    Route::get('/', [StatusController::class, 'index']); // Get all statuses
    Route::post('/', [StatusController::class, 'store']); // Create a status
    Route::get('/{id}', [StatusController::class, 'show']); // Get a single status
    Route::put('/{id}', [StatusController::class, 'update']); // Update a status
    Route::delete('/{id}', [StatusController::class, 'destroy']); // Delete a status
});
Route::prefix('location')->group(function () {
    Route::get('/', [LocationController::class, 'index']); // Get all statuses
    Route::post('/', [LocationController::class, 'store']); // Create a status
    Route::get('/{id}', [LocationController::class, 'show']); // Get a single status
    Route::put('/{id}', [LocationController::class, 'update']); // Update a status
    Route::delete('/{id}', [LocationController::class, 'destroy']); // Delete a status
});

?>