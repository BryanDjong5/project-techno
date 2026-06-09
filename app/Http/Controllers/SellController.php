<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Listing;

class SellController extends Controller
{
    public function showSell()
    {
        return response()->file(public_path('sell.html'));
    }

    public function getMyListings()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Belum login'
            ], 401);
        }

        $listings = Listing::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $listings
        ]);
    }

    public function createListing(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Belum login'
            ], 401);
        }

        $request->validate([
            'game'     => 'required|string|max:255',
            'product'  => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'price'    => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'image'    => 'nullable|image|max:2048'
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')
                ->store('listings', 'public');
        }

        $listing = Listing::create([
            'user_id'     => $user->id,
            'game'        => $request->game,
            'product'     => $request->product,
            'category'    => $request->category,
            'price'       => $request->price,
            'description' => $request->description,
            'image'       => $imagePath,
            'status'      => 'active'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Listing berhasil dibuat',
            'data' => $listing
        ]);
    }

    public function deleteListing($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Belum login'
            ], 401);
        }

        $listing = Listing::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$listing) {
            return response()->json([
                'status' => false,
                'message' => 'Listing tidak ditemukan'
            ], 404);
        }

        if ($listing->image) {
            Storage::disk('public')->delete($listing->image);
        }

        $listing->delete();

        return response()->json([
            'status' => true,
            'message' => 'Listing berhasil dihapus'
        ]);
    }
}