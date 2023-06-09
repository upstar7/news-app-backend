<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Auth;
use Illuminate\Http\Request;
use ResponseHelper;

class NewsController extends Controller
{
    public function __construct()
    {
        // enable use the same routes as authenticated or guest
        if(request()->headers->has('Authorization') && !empty(trim(request()->header('Authorization')))){
            $this->middleware('auth:sanctum');
        }
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();
        $news = News::query();

        /* Apply search */
        if($request->has('search') && !empty($request['search'])){
            $news->where(function ($query) use($request){
                $query->where('title', 'LIKE', '%' . $request['search'] . '%');
                $query->orWhere('body', 'LIKE', '%' . $request['search'] . '%');
            });
        }
        /***************/

        /* Apply filters */
        if($request->has('source_id') && !empty($request['source_id'])){
            $sources = explode(',', $request['source_id']);
            $news->whereIn('source_id', $sources);
            if($authUser)
                $this->saveUserPreferredSources($authUser, $sources);
        } else if ($authUser) {
            $this->saveUserPreferredSources($authUser, []);
        }

        if($request->has('category') && !empty($request['category'])){
            $categories = explode(',', $request['category']);
            $news->whereIn('category', $categories);
            if($authUser)
                $this->saveUserPreferredCategories($authUser, $categories);
        } else if ($authUser) {
            $this->saveUserPreferredCategories($authUser, []);
        }

        if($request->has('author') && !empty($request['author'])){
            $authors = explode(',', $request['author']);
            $news->whereIn('author', $authors);
            if($authUser)
                $this->saveUserPreferredAuthors($authUser, $authors);
        } else if ($authUser) {
            $this->saveUserPreferredAuthors($authUser, []);
        }

        if($request->has('date') && !empty($request['date'])){
            $news->whereDate('published_at', $request['date']);
        }
        /* End filters */

        $news = $news->paginate(10);
        return ResponseHelper::sendResponse($news, 200);
    }

    public function getFilters(Request $request)
    {
        $filters = [
            'categories'                => [],
            'authors'                   => [],
            'preferred_sources'         => '',
            'preferred_categories'      => '',
            'preferred_authors'         => '',
        ];
        $newsQuery = News::query();
        $sourceId = $request->has('sourceId') && !empty($request['sourceId']) ? $request['sourceId'] : null;
        $filters['categories'] = (clone $newsQuery)->whereNotNull('category')
                                                ->where('category', '!=', '')
                                                ->select('category')
                                                ->distinct('category')
                                                ->pluck('category')
                                                ->toArray();
        if($sourceId)
            $filters['categories'] = (clone $newsQuery)->whereNotNull('category')
                                                ->where('category', '!=', '')
                                                ->where('source_id', '=', $sourceId)
                                                ->select('category')
                                                ->distinct('category')
                                                ->pluck('category')
                                                ->toArray();

        $filters['authors'] = (clone $newsQuery)->whereNotNull('author')
                                                ->where('author', '!=', '')
                                                ->select('author')
                                                ->distinct('author')
                                                ->pluck('author')
                                                ->toArray();
        if($sourceId)
            $filters['authors'] = (clone $newsQuery)->whereNotNull('author')
                                                    ->where('author', '!=', '')
                                                    ->where('source_id', '=', $sourceId)
                                                    ->select('author')
                                                    ->distinct('author')
                                                    ->pluck('author')
                                                    ->toArray();

        $authUser = Auth::user();
        if($authUser){
            $filters['preferred_sources'] = $authUser->preferred_sources;
            $filters['preferred_categories'] = $authUser->preferred_categories;
            $filters['preferred_authors'] = $authUser->preferred_authors;
        }
        return ResponseHelper::sendResponse($filters, 200);
    }

    public function saveUserPreferredSources($authUser, $preferredSources)
    {
        $authUser->update(['preferred_sources' => implode(',', $preferredSources)]);
    }

    public function saveUserPreferredCategories($authUser, $preferredCategories)
    {
        $authUser->update(['preferred_categories' => implode(',', $preferredCategories)]);
    }

    public function saveUserPreferredAuthors($authUser, $preferredAuthors)
    {
        $authUser->update(['preferred_authors' => implode(',', $preferredAuthors)]);
    }

}
