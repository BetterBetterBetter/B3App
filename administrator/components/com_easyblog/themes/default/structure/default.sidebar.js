EasyBlog.ready(function($){

    // Check for version
    EasyBlog.ajax('admin/views/easyblog/versionChecks')
        .done(function(contents, outdated, local, latest) {
            $('[data-eb-version-large]').html(contents).removeClass('hide');

            // Applicable only on dashboard
            <?php if (!$view) { ?>
            $('[data-online-version]').html(latest);
            $('[data-local-version]').html(local);

            if (outdated) {
                $('[data-version-checks]').toggleClass('require-updates');

                return;
            }

            $('[data-version-checks]').toggleClass('latest-updates');
            <?php } ?>
        });

    // Sidebar menu functions
    $('[data-sidebar-parent]').on('click', function() {
        var parent = $(this).parent();

        // Disable all open states
        $('[data-sidebar-item]').removeClass('active open');

        parent.toggleClass('active open');
    });


    // Fix the header for mobile view
    $('.container-nav').appendTo($('.header'));

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $('.header').addClass('header-stick');
        } else if ($(this).scrollTop() < 50) {
            $('.header').removeClass('header-stick');
        }
    });

    $('.nav-sidebar-toggle').click(function(){
        $('html').toggleClass('show-easyblog-sidebar');
        $('.subhead-collapse').removeClass('in').css('height', 0);
    });
});