<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Blogs;
use App\Models\Blog_slave;
use Illuminate\Http\Request;

class Web extends Controller
{
    public function blog_list()
    {
        $data = Blogs::join('blog_slave', 'blogs.id', '=', 'blog_slave.blogs_id')
            ->select('blogs.id','blogs.title','blogs.description','blogs.author','blogs.img','blogs.created_on','blog_slave.sub_title','blog_slave.sub_description')
            ->get()
            ->groupBy('id') // Group by the id to merge related data
            ->map(function ($item) {
                // Merge related data from blog_slave into the main array item
                $mergedItem = $item->first()->toArray();
                $mergedItem['blog_slave'] = $item->map(function ($subItem) {
                    return [
                        'sub_title' => $subItem['sub_title'],
                        'sub_description' => $subItem['sub_description']
                    ];
                })->toArray();
                return $mergedItem;
            })
            ->values() // Reset array keys
            ->toArray();

        // dd($data);
        
        return response()->json($data, 200);
    }
}
