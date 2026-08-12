<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Services\ErrorLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct(private readonly ErrorLogService $errorLog)
    {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $contacts = Contact::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('full_name', 'like', '%'.$search.'%')
                        ->orWhere('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.contact', [
            'contacts' => $contacts,
            'search' => $search,
        ]);
    }

    public function show(Contact $contact): View
    {
        return view('admin.contact.show', [
            'contact' => $contact,
        ]);
    }

    public function file(Contact $contact)
    {
        $path = $contact->attachment ?: $contact->file_path;

        if (! $path || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $path,
            $contact->file_original_name ?: basename($path),
            $contact->file_mime_type ? ['Content-Type' => $contact->file_mime_type] : []
        );
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $path = $contact->attachment ?: $contact->file_path;

        try {
            $contact->delete();

            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->route('admin.contact')
                ->with('status', 'Contact deleted successfully.');
        } catch (\Throwable $exception) {
            $this->errorLog->record($exception, 'Admin contact delete failed');

            return back()->withErrors(['contact' => 'Unable to delete contact. Please try again.']);
        }
    }
}
