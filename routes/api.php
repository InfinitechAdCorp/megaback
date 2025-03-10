<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    StatusController,
    LocationController,
    SeminarController,
    MeetingController,
    EventController,
    ClosedDealController,
    RealEstateNewsController,
    RealEstateTipsController,
    OngoingInfrastructureController,
    VideoController,
    CareerController,
    TestimonialController,
    AgentController,
    OfficeController,
    PropertyController,
    PushNotificationController,
    AuthController
};


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Mail\AccountApprovedMail;
use App\Mail\VerificationMail;


Route::get('/verification/{id}', function ($id) {
    $user = User::find($id);

    if (!$user) {
        Log::error("Verification failed: User not found (ID: $id)");
        return response()->json(['message' => 'User not found'], 404);
    }

    // If already verified, notify admin
    if ($user->is_verified) {
        Log::info("User already approved: {$user->email}");
        return response()->json(['message' => 'User is already approved.'], 200);
    }

    // Approve user
    $user->update(['is_verified' => true]);
    Log::info("User approved: {$user->email}");

    try {
        // ✅ Send email to inform the user
        Mail::to($user->email)->send(new AccountApprovedMail($user));

        Log::info("Approval email sent to user: {$user->email}");

    } catch (\Exception $e) {
        Log::error("Approval email sending failed: " . $e->getMessage());
    }

    return response()->json(['message' => 'User has been approved successfully!']);
});


Route::post('/save-subscription', [PushNotificationController::class, 'saveSubscription']);
Route::post('/send-notification', [PushNotificationController::class, 'sendNotification']);

Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 

Route::prefix('count')->middleware('auth:sanctum')->group(function () {
    Route::get('/property', [PropertyController::class, 'getPropertyStatistics']);
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
        Route::get('/search', [PropertyController::class, 'searchProperties']);
    Route::get('/', [PropertyController::class, 'index']); // Public: Get all properties
    Route::get('/{id}', [PropertyController::class, 'show']); // Public: Get a single property

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [PropertyController::class, 'store']); // Protected: Create a property
        Route::post('/updateProperty/{id}', [PropertyController::class, 'updateProperty']); // Protected: Update property
        Route::post('/updateFeature/{id}', [PropertyController::class, 'updateFeatures']); // Protected: Update features
        Route::post('/updateAmenities/{id}', [PropertyController::class, 'updateAmenities']); // Protected: Update amenities
        Route::delete('/{id}', [PropertyController::class, 'destroy']); // Protected: Delete a property
    });
});

// Office routes with public GET and protected POST, PUT, DELETE
Route::prefix('office')->group(function () {
    Route::get('/', [OfficeController::class, 'index']); // Public: Get all offices
    Route::get('/{id}', [OfficeController::class, 'show']); // Public: Get a single office

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [OfficeController::class, 'store']); // Protected: Create a new office
        Route::post('/{id}', [OfficeController::class, 'update']); // Protected: Update an office
        Route::delete('/{id}', [OfficeController::class, 'destroy']); // Protected: Delete an office
    });
});

// Agent routes with public GET and protected POST, PUT, DELETE
Route::prefix('agent')->group(function () {
    Route::get('/', [AgentController::class, 'index']); // Public: Get all agents
    Route::get('/{id}', [AgentController::class, 'show']); // Public: Get a single agent

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [AgentController::class, 'store']); // Protected: Create an agent
        Route::post('/{id}', [AgentController::class, 'update']); // Protected: Update an agent
        Route::delete('/{id}', [AgentController::class, 'destroy']); // Protected: Delete an agent
    });
});

// Career routes with public GET and protected POST, PUT, DELETE
Route::prefix('career')->group(function () {
    Route::get('/', [CareerController::class, 'index']); // Public: Get all careers
    Route::get('/{id}', [CareerController::class, 'show']); // Public: Get a single career

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [CareerController::class, 'store']); // Protected: Create career
        Route::put('/{id}', [CareerController::class, 'update']); // Protected: Update career
        Route::delete('/{id}', [CareerController::class, 'destroy']); // Protected: Delete career
    });
});


Route::prefix('testimonial')->group(function () {
    Route::get('/', [TestimonialController::class, 'index']); // Public: Get all testimonials
    Route::get('/{id}', [TestimonialController::class, 'show']); // Public: Get a single testimonial

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [TestimonialController::class, 'store']); // Protected: Create
        Route::put('/{id}', [TestimonialController::class, 'update']); // Protected: Update
        Route::delete('/{id}', [TestimonialController::class, 'destroy']); // Protected: Delete
    });
});


/*                    WHATS NEW                     */
Route::prefix('seminar')->group(function () {
    Route::get('/', [SeminarController::class, 'index']); // Public: Get all seminars
    Route::get('/{id}', [SeminarController::class, 'show']); // Public: Get a single seminar

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [SeminarController::class, 'store']); // Protected: Create
        Route::post('/{id}', [SeminarController::class, 'update']); // Protected: Update
        Route::delete('/{id}', [SeminarController::class, 'destroy']); // Protected: Delete
    });
});

Route::prefix('meeting')->group(function () {
    Route::get('/', [MeetingController::class, 'index']);
    Route::get('/{id}', [MeetingController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [MeetingController::class, 'store']);
        Route::post('/{id}', [MeetingController::class, 'update']);
        Route::delete('/{id}', [MeetingController::class, 'destroy']);
    });
});

Route::prefix('event')->group(function () {
    Route::get('/', [EventController::class, 'index']);
    Route::get('/{id}', [EventController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [EventController::class, 'store']);
        Route::post('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
    });
});

Route::prefix('closedDeal')->group(function () {
    Route::get('/', [ClosedDealController::class, 'index']);
    Route::get('/{id}', [ClosedDealController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ClosedDealController::class, 'store']);
        Route::post('/{id}', [ClosedDealController::class, 'update']);
        Route::delete('/{id}', [ClosedDealController::class, 'destroy']);
    });
});

Route::prefix('realEstateNews')->group(function () {
    Route::get('/', [RealEstateNewsController::class, 'index']);
    Route::get('/{id}', [RealEstateNewsController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [RealEstateNewsController::class, 'store']);
        Route::post('/{id}', [RealEstateNewsController::class, 'update']);
        Route::delete('/{id}', [RealEstateNewsController::class, 'destroy']);
    });
});

Route::prefix('realEstateTips')->group(function () {
    Route::get('/', [RealEstateTipsController::class, 'index']);
    Route::get('/{id}', [RealEstateTipsController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [RealEstateTipsController::class, 'store']);
        Route::post('/{id}', [RealEstateTipsController::class, 'update']);
        Route::delete('/{id}', [RealEstateTipsController::class, 'destroy']);
    });
});

Route::prefix('ongoingInfrastructure')->group(function () {
    Route::get('/', [OngoingInfrastructureController::class, 'index']);
    Route::get('/{id}', [OngoingInfrastructureController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [OngoingInfrastructureController::class, 'store']);
        Route::post('/{id}', [OngoingInfrastructureController::class, 'update']);
        Route::delete('/{id}', [OngoingInfrastructureController::class, 'destroy']);
    });
});

Route::prefix('video')->group(function () {
    Route::get('/', [VideoController::class, 'index']);
    Route::get('/{id}', [VideoController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [VideoController::class, 'store']);
        Route::post('/{id}', [VideoController::class, 'update']);
        Route::delete('/{id}', [VideoController::class, 'destroy']);
    });
});
/*                    FORM FILLER                     */

Route::prefix('location')->group(function () {
    // Public Routes (No authentication needed)
    Route::get('/', [LocationController::class, 'index']); // Get all locations
    Route::get('/{id}', [LocationController::class, 'show']); // Get a single location

    // Protected Routes (Require Authentication)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [LocationController::class, 'store']); // Create a location
        Route::put('/{id}', [LocationController::class, 'update']); // Update a location
        Route::delete('/{id}', [LocationController::class, 'destroy']); // Delete a location
    });
});


?>