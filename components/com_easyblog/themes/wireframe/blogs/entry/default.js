
EasyBlog.require()
.script('posts/posts')
.done(function($) {

    // Implement post library
    $('[data-blog-post]').implement(EasyBlog.Controller.Posts);

});
