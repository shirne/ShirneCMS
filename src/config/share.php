<?php

return [
    'background'=>'./uploads/pshare/bg.png',
    'data'=>[
        'image'=>[
            'type'=>'image',
            'x'=>72,
            'y'=>76,
            'width'=>750,
            'height'=>750,
        ],
        'bg'=>['type'=>'background'],
        /* 'price'=>[
            'x'=>272,
            'y'=>915,
            'size'=>32,
            'color'=>'#333'
        ], */
        /* 'avatar'=>[
            'type'=>'image',
            'x'=>120,
            'y'=>896,
            'width'=>120,
            'height'=>120,
        ], */
        /* 'nickname'=>[
            'x'=>272,
            'y'=>915,
            'size'=>32,
            'color'=>'#333'
        ], */
        'qrcode'=>[
            'type'=>'image',
            'x'=>606,
            'y'=>870,
            'width'=>172,
            'height'=>172,
        ],
        'title'=>[
            'x'=>105,
            'y'=>878,
            'width'=>368,
            'maxline'=>2,
            'size'=>28,
            'color'=>'#111'
        ],
        /* 'prop_from'=>[
            'offset'=>[
                'field'=>'title',
                'type'=>'lb'
            ],
            'x'=>0,
            'y'=>20,
            'size'=>16,
            'color'=>'#333'
        ],
        'prop_alcohol'=>[
            'offset'=>[
                'field'=>'prop_from',
                'type'=>'lb'
            ],
            'x'=>0,
            'y'=>10,
            'size'=>16,
            'color'=>'#333'
        ],
        'prop_volume'=>[
            'offset'=>[
                'field'=>'prop_alcohol',
                'type'=>'lb'
            ],
            'x'=>0,
            'y'=>10,
            'size'=>16,
            'color'=>'#333'
        ], */
        'vice_title'=>[
            'offset'=>[
                'field'=>'prop_volume',
                'type'=>'lb'
            ],
            'x'=>0,
            'y'=>10,
            'width'=>400,
            'size'=>16,
            'color'=>'#333'
        ]
    ]
];