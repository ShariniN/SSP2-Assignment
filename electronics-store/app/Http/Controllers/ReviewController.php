<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display all reviews for a product
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('reviews.index', compact('product', 'reviews'));
    }

    /**
     * Show the form for creating a new review
     */
    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        
        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()
                ->route('product.show', $productId)
                ->with('error', 'You have already reviewed this product.');
        }

        return view('reviews.create', compact('product'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $productId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return redirect()
                ->route('product.show', $productId)
                ->with('error', 'You have already reviewed this product.');
        }

        // Create the review
        Review::create([
            'product_id' => $productId,
            'user_id' => Auth::id(),
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'is_verified' => false, // Can be set to true if user purchased the product
        ]);

        return redirect()
            ->route('product.show', $productId)
            ->with('success', 'Thank you for your review!');
    }

    /**
     * Show the form for editing a review
     */
    public function edit($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $product = $review->product;

        return view('reviews.edit', compact('review', 'product'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validate the request
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        // Update the review
        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('product.show', $review->product_id)
            ->with('success', 'Your review has been updated!');
    }

    /**
     * Remove the specified review
     */
    public function destroy($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $productId = $review->product_id;
        $review->delete();

        return redirect()
            ->route('product.show', $productId)
            ->with('success', 'Your review has been deleted.');
    }

    /**
     * Load more reviews via AJAX
     */
    public function loadMore(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        $page = $request->get('page', 1);
        
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->paginate(5, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'reviews' => $reviews->items(),
            'hasMore' => $reviews->hasMorePages(),
        ]);
    }
}