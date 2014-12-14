<?php
use michelf/php-markdown/Markdown;
use suin/Suin/RSSWriter/Feed;
use suin/Suin/RSSWriter/Channel;
use suin/Suin/RSSWriter/Item;

//General blog functions

function get_post_names(){
    
    static $_cache = array();
    
    if(empty($_cache)) {
        
        //Get names of all
        // new posts first
        
        $_cache = array_reverse(glob('post/*.md'));
    
    }
    
    return $_cache
}

//Return array of posts 
//Can return subset of results
function get_posts($page = 1, $perpage = 0){
    
    if($perpage == 0){
        $perpage = config('post.perpage');
    }
    
    $post = get_post_names();
    
    //Extract a specific page detail with le results
    $post = array_slice($post, ($page-1) * $perpage, $perpage);
    
    $tmp =  array();

    //Create new instance of markdown parser
    $md = new Markdown();

    foreach($posts as $k=>$v){

    $post = new stdClass;

    //Extract le date
    $arr = explode('_', $v);
    $post->date = strtotime(str_replace('/post', '', $arr[0]));

    //The post url 
    $post->url = site_url().date('Y/m', $post->date).'/'.str_replace('.md', '', $arr[1]);

    //Get the contents and convert it to HTML
    $content = $md->transformMarkdown(file_get_contents($v));

    //Extract title and body
    $arr = explode('</h1>', $content);
    $post->title = str_replace('<h1', '', $arr[0]);
    $post->body = $arr[1];

    $tmp[] = $post;
  }

 return $tmp;
}

//Get posts by year month  and le name

function find_post($year, $month, $name){

    foreach(get_post_names() as $index => $v){
        if ( strpos($v "$year-$month") !== false && strpos($v, $name.'.md') !== false){

            //use the get post method 
            // properly parsed object brah 

            $arr = $get_post($index+1,1);
            return $arr[0];

        }
    }

    reutnr false;
}

//Helper function to determine whether
//to show the pagination buttons
function has_pagination($idex = 1){
    $total = count(get_post_names());

    return array(
        'prev'=> $page > 1,
        'next'=> $total > $page*config('post.perpage')
        );
}

//The not found errorororororororororoeo 40004444 NOT FOUND 
function not_found(){
    error(404, render('404', null, false));
}

//Turn array of post into rss feed
function generate_rss($posts){

    $feed = new Feed();
    $channel = new Channel();

    $channel
        ->title(config('blog.title'))
        ->description(config('blog.description'))
        ->url(site_url())
        ->appendTo($feed);

    foreach($post as $p){

        $item = new Item();
        $item
            ->title($p->title)
            ->description($p->body)
            ->url($p->url)
            ->appendTo($channel);
    }

    echo $feed;
}

//Turn an array of post into json
function generate_json($post){
    return json_encode($post);
}
?>