<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix'=>'users'], function(){

    Route::group(['namespace' => 'Account'], function(){

        Route::group(['prefix'=>'passport'], function(){

            Route::get('/', 'PassportController@check');
            Route::post('/', 'PassportController@register');
            Route::put('/', 'PassportController@login');
            Route::patch('/', 'PassportController@verify');

        });

        Route::group(['prefix'=>'password'], function(){

            Route::get('/', 'PasswordController@forgot');
            Route::patch('/', 'PasswordController@verify');
            Route::put('/', 'PasswordController@reset');

        });

    });

});

Route::group(['prefix'=>'admin'], function(){

    // admin auth apis
    Route::group(['middleware'=>['auth:api','admin']], function(){

        Route::group(['prefix'=>'users', 'namespace'=>'User'], function () {
            Route::get('', 'UserController@users');
            Route::get('{user}', 'UserController@getUser');
            Route::get('{user}/programs', 'UserController@getUserPrograms');
        });

        Route::group(['prefix' => 'exercises', 'namespace' => 'Program'], function(){

            Route::get('modes', 'ExerciseController@modes');
            Route::get('types', 'ExerciseController@types');
            Route::get('types/{exerciseType}/attributes', 'ExerciseController@typeAttributes');

            Route::group(['prefix' => 'templates'], function(){

                Route::get('', 'ExerciseController@templates');
                Route::post('', 'ExerciseController@createTemplate');

                Route::group(['prefix' => '{exerciseTemplate}'], function () {
                    Route::get('', 'ExerciseController@getTemplate');
                    Route::put('', 'ExerciseController@updateTemplate');
                    Route::patch('', 'ExerciseController@publishTemplate');
                    Route::delete('', 'ExerciseController@deleteTemplate');
                });

            });


        });

        Route::group(['prefix' => 'bonus-exercises', 'namespace' => 'Program'], function(){

            Route::get('', 'BonusExerciseController@bonusExercises');
            Route::post('', 'BonusExerciseController@createBonusExercise');
            Route::get('{bonusExercise}', 'BonusExerciseController@getBonusExercise');
            Route::put('{bonusExercise}', 'BonusExerciseController@updateBonusExercise');
            Route::patch('{bonusExercise}', 'BonusExerciseController@publishBonusExercise');
            Route::delete('{bonusExercise}', 'BonusExerciseController@deleteBonusExercise');

        });

        Route::group(['prefix' => 'testings', 'namespace' => 'Testing'], function(){

            Route::group(['prefix' => 'testTemplates'], function(){

                Route::get('', 'TestingTemplateController@testTemplates');

                Route::group(['prefix' => '{testTemplate}'], function () {
                    Route::get('', 'TestingTemplateController@getTestTemplate');
                });

            });
        });


        Route::group(['prefix'=>'programs', 'namespace' => 'Program'], function(){
            Route::get('', 'ProgramController@programs');
            Route::post('', 'ProgramController@createProgram');

            Route::group(['prefix'=>'{program}'], function () {

                Route::get('', 'ProgramController@getProgram');
                Route::put('', 'ProgramController@updateProgram');
                Route::delete('', 'ProgramController@deleteProgram');

                Route::group(['prefix'=>'stages'], function (){
                    Route::post('', 'ProgramController@addStage');

                    Route::group(['prefix'=>'{stage}'], function () {

                        Route::delete('', 'ProgramController@deleteStage');

                        Route::group(['prefix'=>'sessions'], function () {

                            Route::post('', 'SessionController@createSession');
                            Route::get('{session}', 'SessionController@getSession');
                            Route::put('{session}', 'SessionController@updateSession');
                            Route::delete('{session}', 'SessionController@deleteSession');
                            Route::post('{session}/exercises', 'SessionController@addExercise');
                            Route::get('{session}/exercises/{exercise}', 'SessionController@getExercise');
                            Route::put('{session}/exercises/{exercise}', 'SessionController@updateExercise');
                            Route::delete('{session}/exercises/{exercise}', 'SessionController@deleteExercise');
                            Route::post('{session}/exercises/{exercise}/attributes', 'SessionController@addAttributes');
                            Route::delete('{session}/exercises/{exercise}/attributes/{exercisePrescription}', 'SessionController@deleteAttribute');


                            Route::post('{session}/topics/{topic}', 'SessionController@addQuestionnaire');
                            Route::delete('{session}/questionnaires/{questionnaire}', 'SessionController@deleteQuestionnaire');


                            // TestingupdateTestSetFields
                            Route::post('{session}/testings', 'TestingController@addTesting');
                            Route::post('{session}/testings/{test}', 'TestingController@createTestSets');
                            Route::patch('{session}/testings/{test}', 'TestingController@updateTestSetFields');
                            Route::put('{session}/testings/{test}', 'TestingController@updateTesting');
                            Route::delete('{session}/testings/{test}', 'TestingController@deleteTesting');
                            Route::get('{session}/testings/{test}', 'TestingController@getTesting');
                        });

                    });

                });
            });

            // under api/admin/programs
            Route::group(['prefix'=>'categories'], function () {

                Route::get('', 'ProgramController@categories');
                Route::post('', 'ProgramController@createCategory');
                Route::get('{programCategory}', 'ProgramController@getCategory');
                Route::put('{programCategory}', 'ProgramController@updateCategory');
                Route::delete('{programCategory}', 'ProgramController@deleteCategory');

            });

        });

        Route::group(['prefix' => 'topics', 'namespace' => 'Questionnaire'], function() {
            Route::get('', 'TopicController@topics');
            Route::post('', 'TopicController@createTopic');
            Route::put('{topic}', 'TopicController@updateTopic');
            Route::delete('{topic}', 'TopicController@deleteTopic');

            Route::get('{topic}/questions', 'QuestionController@questions');
            Route::post('{topic}/questions', 'QuestionController@createQuestion');
            Route::put('{topic}/questions/{question}', 'QuestionController@updateQuestion');
            Route::delete('{topic}/questions/{question}', 'QuestionController@deleteQuestion');
        });

        Route::group(['prefix' => 'functions', 'namespace' => 'Measurement'], function(){
            Route::get('', 'FunctionController@functions');
        });

        Route::group(['prefix' => 'fields', 'namespace' => 'Measurement'], function () {
            Route::get('', 'FieldController@fields');
            Route::get('{field}', 'FieldController@getField');
            Route::post('', 'FieldController@createField');
            Route::put('{field}', 'FieldController@updateField');
            Route::delete('{field}', 'FieldController@deleteField');
            Route::get('groups', 'FieldController@groups');
            Route::post('groups', 'FieldController@createGroup');
            Route::put('groups/{fieldGroup}', 'FieldController@updateGroup');
            Route::delete('groups/{fieldGroup}', 'FieldController@deleteGroup');
        });

    });

});
