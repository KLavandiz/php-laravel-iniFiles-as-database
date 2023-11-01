<?php

use App\CoreDB\FileDBEnginee;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

  
  
    $user = new User();

$user ->FileDB()->add([
    'name' => 'Kemal',
    'Surname' => 'Berkan',
    'email' => 'kemal@kemal.com',
    'phone' => '077777'
]);

    $getcustomer = $user->FileDB()->getById(26);

    

 

    return view('welcome',["customers"=>$getcustomer]);
});
