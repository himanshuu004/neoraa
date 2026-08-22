<?php

namespace App\Http\Controllers;

use App\Models\HiringApplication;
use App\Models\Review;
use App\Models\SessionBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublicApiController extends Controller
{
    public function bookSession(Request $request)
    {
        $this->ensureBookingsTable();

        $name = trim((string) $request->input('name', ''));
        $phone = trim((string) $request->input('phone', ''));
        $service = trim((string) $request->input('service', ''));
        $message = trim((string) $request->input('message', ''));

        if ($name === '' || $phone === '') {
            return response()->json(['success' => false, 'message' => 'Name and phone number are required.']);
        }

        $phoneDigits = preg_replace('/\D/', '', $phone);
        if (strlen((string) $phoneDigits) < 7) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid phone number.']);
        }

        $exists = SessionBooking::query()
            ->get(['id', 'phone'])
            ->first(fn ($row) => preg_replace('/\D/', '', (string) $row->phone) === $phoneDigits);

        if ($exists) {
            return response()->json([
                'success' => false,
                'duplicate' => true,
                'message' => 'You have already submitted a booking request with this contact number. We will reach out to you soon, please wait for our call!',
            ]);
        }

        SessionBooking::create([
            'name' => $name,
            'phone' => $phone,
            'service' => $service ?: null,
            'message' => $message ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your booking request has been submitted! We will get back to you within 24 hours to confirm your appointment.',
        ]);
    }

    public function submitReview(Request $request)
    {
        $text = trim((string) $request->input('text', ''));
        $author = trim((string) $request->input('author', ''));
        $location = trim((string) $request->input('location', ''));
        $photoPath = null;

        if ($text === '' || $author === '') {
            return response()->json(['success' => false, 'message' => 'Name and review content are required']);
        }

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file = $request->file('photo');
            $ext = strtolower($file->getClientOriginalExtension());
            if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                return response()->json(['success' => false, 'message' => 'Only JPG, JPEG, PNG images are allowed']);
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                return response()->json(['success' => false, 'message' => 'File size exceeds 5MB limit']);
            }

            $dir = public_path('uploads/reviews');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $name = 'review_'.time().'_'.uniqid().'.'.$ext;
            $file->move($dir, $name);
            $photoPath = 'uploads/reviews/'.$name;
        }

        try {
            $review = Review::create([
                'text' => $text,
                'author' => $author,
                'location' => $location ?: null,
                'photo_path' => $photoPath,
                'display_order' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your review! It has been submitted successfully.',
                'id' => $review->id,
            ]);
        } catch (\Throwable $e) {
            if ($photoPath && is_file(public_path($photoPath))) {
                @unlink(public_path($photoPath));
            }

            return response()->json(['success' => false, 'message' => 'Failed to save review. Please try again.']);
        }
    }

    public function submitApplication(Request $request)
    {
        $name = trim((string) $request->input('name', ''));
        $mobile = trim((string) $request->input('mobile', ''));
        $email = trim((string) $request->input('email', ''));

        if ($name === '' || $mobile === '' || $email === '') {
            return response()->json(['success' => false, 'message' => 'Name, mobile, and email are required.']);
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid email address.']);
        }
        if (! preg_match('/^[0-9+\-\s]{7,15}$/', $mobile)) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid mobile number.']);
        }

        [$resumePath, $resumeErr] = $this->uploadDoc($request, 'resume', 'resume');
        if ($resumeErr) {
            return response()->json(['success' => false, 'message' => $resumeErr]);
        }
        [$certificatePath, $certErr] = $this->uploadDoc($request, 'certificate', 'cert');
        if ($certErr) {
            if ($resumePath && is_file(public_path($resumePath))) {
                @unlink(public_path($resumePath));
            }

            return response()->json(['success' => false, 'message' => $certErr]);
        }

        try {
            $app = HiringApplication::create([
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'city' => trim((string) $request->input('city')) ?: null,
                'applying_for' => trim((string) $request->input('applying_for')) ?: null,
                'qualification' => trim((string) $request->input('qualification')) ?: null,
                'college' => trim((string) $request->input('college')) ?: null,
                'year' => trim((string) $request->input('year')) ?: null,
                'experience_type' => trim((string) $request->input('experience_type')) ?: null,
                'experience_years' => trim((string) $request->input('experience_years')) ?: null,
                'current_place' => trim((string) $request->input('current_place')) ?: null,
                'areas_specialization' => trim((string) $request->input('areas_specialization')) ?: null,
                'languages' => trim((string) $request->input('languages')) ?: null,
                'joining_time' => trim((string) $request->input('joining_time')) ?: null,
                'resume_path' => $resumePath,
                'certificate_path' => $certificatePath,
                'why_join_neora' => trim((string) $request->input('why_join_neora')) ?: null,
                'status' => 'New',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your application has been submitted successfully!',
                'id' => $app->id,
            ]);
        } catch (\Throwable $e) {
            if ($resumePath && is_file(public_path($resumePath))) {
                @unlink(public_path($resumePath));
            }
            if ($certificatePath && is_file(public_path($certificatePath))) {
                @unlink(public_path($certificatePath));
            }

            return response()->json(['success' => false, 'message' => 'Database error. Please try again.']);
        }
    }

    public function storeApplyForm(Request $request)
    {
        $generatedBy = $request->query('ref') ? (int) $request->query('ref') : null;
        $error = '';

        $name = trim((string) $request->input('name', ''));
        $mobile = trim((string) $request->input('mobile', ''));
        $email = trim((string) $request->input('email', ''));

        if (strlen($name) < 2) {
            $error = 'Please enter a valid name.';
        } elseif (strlen($mobile) < 10) {
            $error = 'Please enter a valid mobile number.';
        } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        }

        $resumePath = null;
        $certificatePath = null;

        if ($error === '') {
            [$resumePath, $resumeErr] = $this->uploadDoc($request, 'resume', 'resume', 'uploads/hiring');
            if ($resumeErr) {
                $error = $resumeErr;
            }
        }
        if ($error === '') {
            [$certificatePath, $certErr] = $this->uploadDoc($request, 'certificate', 'cert', 'uploads/hiring');
            if ($certErr) {
                $error = $certErr;
            }
        }

        if ($error !== '') {
            return view('landing.apply', [
                'error' => $error,
                'generatedBy' => $generatedBy,
            ]);
        }

        $areas = $request->input('areas_specialization', []);
        $areasStr = is_array($areas) ? implode(', ', array_map('trim', $areas)) : trim((string) $areas);

        HiringApplication::create([
            'name' => $name,
            'mobile' => $mobile,
            'email' => $email,
            'city' => trim((string) $request->input('city')),
            'applying_for' => trim((string) $request->input('applying_for')),
            'qualification' => trim((string) $request->input('qualification')),
            'college' => trim((string) $request->input('college')),
            'year' => trim((string) $request->input('year')),
            'experience_type' => trim((string) $request->input('experience_type')),
            'experience_years' => trim((string) $request->input('experience_years')),
            'current_place' => trim((string) $request->input('current_place')),
            'areas_specialization' => $areasStr,
            'languages' => trim((string) $request->input('languages')),
            'joining_time' => trim((string) $request->input('joining_time')),
            'resume_path' => $resumePath,
            'certificate_path' => $certificatePath,
            'why_join_neora' => trim((string) $request->input('why_join_neora')),
            'generated_by' => $generatedBy,
            'status' => 'New',
        ]);

        return redirect()->route('apply.confirmation');
    }

    /**
     * @return array{0:?string,1:?string}
     */
    private function uploadDoc(Request $request, string $key, string $prefix, string $relativeDir = 'uploads/applications'): array
    {
        if (! $request->hasFile($key) || ! $request->file($key)->isValid()) {
            return [null, null];
        }

        $file = $request->file($key);
        $ext = strtolower($file->getClientOriginalExtension());
        $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        if (! in_array($ext, $allowed, true)) {
            return [null, 'Only PDF, DOC, DOCX, JPG, PNG files are accepted for '.$key];
        }
        if ($file->getSize() > 5 * 1024 * 1024) {
            return [null, 'File size for '.$key.' exceeds 5 MB limit.'];
        }

        $dir = public_path($relativeDir);
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return [null, 'Could not create upload folder. Check server permissions.'];
        }

        $name = $prefix.'_'.time().'_'.uniqid().'.'.$ext;
        $file->move($dir, $name);

        return [$relativeDir.'/'.$name, null];
    }

    private function ensureBookingsTable(): void
    {
        if (Schema::hasTable('session_bookings')) {
            return;
        }

        DB::statement("CREATE TABLE IF NOT EXISTS session_bookings (
            id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name         VARCHAR(255) NOT NULL,
            phone        VARCHAR(50)  NOT NULL,
            service      VARCHAR(255) DEFAULT NULL,
            message      TEXT         DEFAULT NULL,
            status       ENUM('new','reached_out','talked','closed') NOT NULL DEFAULT 'new',
            created_at   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
}
