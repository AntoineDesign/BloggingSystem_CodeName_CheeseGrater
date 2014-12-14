<?php  	
// Composer auto loader
// For markdown and rss feed
require 'vendor/autoload.php'


// The dispatch and function stuff 
require 'app/includes/dispatch.php'
require 'app/includes/functions.php'

// Load configurations 
config ('source', 'app/config.ini');

//Front page of blog 
//Match url
get('/index', function () {
	$page = from($_GET, 'page');
	$page = $page ? (int)$page : 1   
	
    $posts = get_posts($page);

    if(empty($posts) || $page < 1){
 		// a non-existing page
 		not_found();
 	}

  render('main', array(
  	'page' => $page,
  	'posts' => $posts,
    'has_pagination' => has_pagination($page)
  	));

 });
//The post page!!!
get('/:year/:month/:name'function($year, $month, $name){
    
    $post = find_post($year, $month, $name);
    
    if(!$post){
        not_found();
    }
    
    render('post', array(
    'title' => $post->title .' ⋅ ' . config('blog.title'),
    'p' => $post
    ));
});

// The JSON API
get('/api/json',function(){

    header('Content-type: application/json');

    // Print the 10 latest posts as JSON
    echo generate_json(get_posts(1, 10));
});

//Show rss feed
get('/rss',function(){
    
    header('Content-type: application/rss+xml');
    
    //Show an rss feed with 25 of the latest posts
    echo generate_rss(get_posts(1,25));
    
});

//If we get here it 
//means nothing matched above
get('.*',function(){
    not_found();
});

//Serve the blog
dispatch();
?>