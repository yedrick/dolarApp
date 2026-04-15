<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Controllers\Web;

use App\Domain\Offer\Repositories\OfferRepositoryInterface;
use App\Infrastructure\Http\Controllers\BaseController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileWebController extends BaseController
{
    public function __construct(
        private readonly OfferRepositoryInterface $offerRepository,
    ) {}

    public function index(): View
    {
        $allOffers   = $this->offerRepository->findByUser(auth()->id());
        $activeCount = collect($allOffers)->where('status', 'activa')->count();
        $totalCount  = count($allOffers);

        return view('profile.index', compact('activeCount', 'totalCount'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        auth()->user()->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect()->route('home')
            ->with('success', 'Tu cuenta ha sido eliminada.');
    }
}
