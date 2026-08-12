<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiJob;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function resources(): View
    {
        return view('admin.resources');
    }

    public function jobs(): View
    {
        return view('admin.career.jobs');
    }

    public function applications(Request $request): View
    {
        $selectedFilter = (string) $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));
        $jobs = AiJob::query()
            ->select(['id', 'job_title'])
            ->orderBy('id')
            ->get();

        $applications = JobApplication::query()
            ->when($selectedFilter === 'future_opening', function ($query): void {
                $query->where('application_type', 'future_opening');
            })
            ->when(is_numeric($selectedFilter), function ($query) use ($selectedFilter): void {
                $query->where('job_id', (int) $selectedFilter);
            })
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('city', 'like', '%'.$search.'%')
                        ->orWhere('major_experience', 'like', '%'.$search.'%')
                        ->orWhere('years_of_experience', 'like', '%'.$search.'%')
                        ->orWhere('present_salary', 'like', '%'.$search.'%')
                        ->orWhere('notice_period', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.career.applications', [
            'applications' => $applications,
            'jobs' => $jobs,
            'selectedFilter' => $selectedFilter,
            'search' => $search,
        ]);
    }

    public function contact(): View
    {
        return view('admin.contact');
    }

    public function bulkPurchases(): View
    {
        return view('admin.bulk-purchases');
    }

    public function ctaLeads(int $sourceType): View
    {
        abort_unless(array_key_exists($sourceType, CtaLeadController::sourceLabels()), 404);

        return view('admin.cta-leads', [
            'sourceType' => $sourceType,
            'sourceLabel' => CtaLeadController::sourceLabels()[$sourceType],
        ]);
    }

    public function advancedCtaLeads(int $menuType): View
    {
        abort_unless(array_key_exists($menuType, AdvancedCtaLeadController::menuLabels()), 404);

        return view('admin.advanced-cta-leads', [
            'menuType' => $menuType,
            'menuLabel' => AdvancedCtaLeadController::menuLabels()[$menuType],
            'groupLabel' => $menuType <= 8 ? 'AI Training' : 'AI Solution',
            'dataRouteName' => $menuType <= 8 ? 'admin.cta.ai-training.data' : 'admin.cta.ai-solution.data',
        ]);
    }
}
