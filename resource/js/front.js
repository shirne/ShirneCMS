function setNav(nav) {
    
    var current=findCurrentNav(nav);
    setNavHover(current);

    var intval=0;
    $('.nav-box .navbar-collapse').hover(function(){
        clearInterval(intval)
    },function(){
        intval = setInterval(function(){
            setNavHover(current);
        },500)
    })
    $('.nav-box .nav-item').hover(function(){
        var index = $('.main-nav .nav-item').index(this);
        setNavHover(index);
    },function(){

    })
}

function setNavHover(index){
    var items=$('.main-nav .nav-item');
    items.removeClass('active prev-hover')
    items.eq(index).addClass('active');
    $('.main-nav .nav-item.active').prev().addClass('prev-hover');

    $('.nav-box .nav-bg').css('right',(items.length-index-1)*items.eq(0).outerWidth())
}

function findCurrentNav(nav){
    var items=$('.main-nav .nav-item');
    
    for(var i=0;i<items.length;i++){
        if(items.eq(i).data('model')===nav){
            return i;
        }
    }
    var pnav=nav.substr(0,nav.lastIndexOf('-'));
    if(pnav == nav || !pnav){
        return 0;
    }
    return findCurrentNav(pnav);
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