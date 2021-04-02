<php>$banner_flag = 'channel_'.$channel['id'];</php>
<extendtag:advs var="banners" flag="$banner_flag"/>
<div class="swiper-container subbanner">
    <div class="swiper-wrapper">
        <volist name="banners" id="item" key="k">
            <div class="swiper-slide" style="background-image:url({$item.image});height:100%;">
                <img class="mainbg" src="{$item.image}" alt="{$image.title}">
                <if condition="!empty($item['elements'])">
                    <volist name="item['elements']" id="ele">
                        <if condition="$ele['type']=='image'">
                        <img class="ani" src="{$ele.image}" style="{$ele.style}" swiper-animate-effect="{$ele.effect}" swiper-animate-duration="{$ele.duration}s" swiper-animate-delay="{$ele.delay}s" />
                        <else/>
                            <p class="ani" style="{$ele.style}" swiper-animate-effect="{$ele.effect}" swiper-animate-duration="{$ele.duration}s" swiper-animate-delay="{$ele.delay}s">{$ele.text}</p>
                        </if>
                    </volist>
                </if>
            </div>
        </volist>
    </div>
    
    <div class="swiper-pagination"></div>
</div>