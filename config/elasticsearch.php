<?php

// config/elasticsearch.php
return [
    'hosts' => [
        env('ELASTICSEARCH_HOST', 'edu-all.com:9200'),
    ],
];