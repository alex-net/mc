<?php

return [
    'adminEmail' => 'admin@example.com',
    'classes'=>[],
    'front-is'=>'front-page',
    'writeuns'=>[
        'to'=>'alexnet2004@gmail.com',
        'from'=>'site@mc-ruza.ru',
        'theme'=>'Сообщение с сайта mc-ruza.ru',
    ],
    // соцсети 
    'socnets'=>[
        [
            'link'=>'https://vk.com/mcentrruza',
            'class'=>'fa fa-vk'
        ],
        [
            'link'=>'https://www.instagram.com/molodezh_ruza',
            'class'=>'fa fa-instagram'
        ],
        [
            'link'=>'https://vk.com/rzmmc',
            'class'=>'fa fa-vk'
        ],
    ],
    // список пресетов для картинок ... 
    'presets'=>[
    	'thumb'=>[
    		'size'=>'100x100',
    		'crop'=>false,
    	],
    	'content'=>[
    		'size'=>'900xauto',
    		'crop'=>false,
    	],
    	'mews-block-teaser'=>[
    		'size'=>'263x351',
    		//'crop'=>true,
    	],
        'albums-images'=>[
            'size'=>'200xauto',
        ],
        'news-preset'=>[
            'size'=>'200x200',
            'crop'=>true,
        ],
        'albums-preset'=>[
            'size'=>'200x200',
            'crop'=>true,
        ],
        'news-slider'=>[
            'size'=>'425x355',
            'crop'=>true, 
        ],
    ],
];
