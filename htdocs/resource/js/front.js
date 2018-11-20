function setNav(nav) {
    var items=$('.main-nav .nav-item');
    var finded=false;
    for(var i=0;i<items.length;i++){
        if(items.eq(i).data('model')===nav){
            items.eq(i).addClass('active');
            finded=true;
            break;
        }
    }
    if(!finded && nav.indexOf('-')>0){
        nav=nav.substr(0,nav.lastIndexOf('-'));
        setNav(nav);
    }
}

jQuery(function($){
    if($(window).width()>=991){
        $('.main-nav>.dropdown').hover(
            function () {
                $(this).find('.dropdown-menu').stop(true,false).slideDown();
            },
            function () {
                $(this).find('.dropdown-menu').stop(true,false).slideUp();
            }
        );
    }else{
        $('.main-nav>.dropdown>.dropdown-toggle').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            var opened=$(this).data('opened');
            var p = $(this).parents('.dropdown');
            if(opened){
                p.find('.dropdown-menu').stop(true, false).slideUp();
            }else {
                p.siblings().children('.dropdown-menu').stop(true, false).slideUp();
                p.siblings().children('.dropdown-toggle').data('opened',false);
                p.find('.dropdown-menu').stop(true, false).slideDown();
            }
            $(this).data('opened',!opened);
        })
    }
});