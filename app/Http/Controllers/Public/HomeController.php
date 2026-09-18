<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicContentService;

class HomeController extends Controller
{
    public function __construct(protected PublicContentService $contentService) {}

    public function index()
    {
        $data = $this->contentService->getHomepageData();

        return view('public.home', $data);
    }
}
