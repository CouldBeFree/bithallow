<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Categorie;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        # Категории которые отображаются в верху сайта
    	$categories_top = [1,2];
    	# Масив с категориями
    	$categories_top_data = [];
    	# Получение названий категорий
    	foreach ($categories_top as $item){
    		$categories_top_data[] = Categorie::where('id', $item)->first();
    	}
    	# Категории левого меню
    	$categories_left = [1,2];
    	# Масив с категориями
    	$categories_left_data = [];
    	# Получение названий категорий
    	foreach ($categories_left as $item){
    		$categories_left_data[] = Categorie::where('id', $item)->first();
    	}
    	# Передам данные во все шаблоны
    	view()->share('categories_top', $categories_top_data);
    	view()->share('categories_left', $categories_left_data);
    }
}
