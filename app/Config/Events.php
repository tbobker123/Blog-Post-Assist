<?php

namespace Config;

use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static fn ($buffer) => $buffer);
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
    }
});

Events::on('register', static function($user) {

    $db = \Config\Database::connect();


    /***
     * Add entries to api keys table
     */
    $builder1 = $db->table('apikeys');

    $api_names = [
        'openai',
        'tinymce',
        'rapidapi',
        'serpapi',
    ];

    foreach($api_names as $name){
        $builder1->insert([
            'name' => $name,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Add entry for OpenAI prompts
     */
    
    $builder2 = $db->table('configuration');

    $prompts = [
        'user_id' => $user->id,
        'openAI_topic' => 'Create 15 blog topics or blog post titles about',
        'openAI_outline' => 'Create a detailed, interesting and informative blog post outline about',
        'openAI_section' => 'write a detailed blog post section about',
        'serp' => 30,
    ];

    $builder2->insert($prompts);



});
