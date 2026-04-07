<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// En producción (build de Vite), Laravel sirve el index.html de la SPA para todas las rutas.
// El JS del frontend toma el control y el router de Vue maneja la navegación.
Route::get('/{any?}', function () {
    $spaFile = public_path('index.html');

    if (file_exists($spaFile)) {
        return file_get_contents($spaFile);
    }

    // Si aún no se ha hecho el build, muestra instrucciones claras
    return response(
        '<h2 style="font-family:sans-serif;padding:2rem">'
        . 'Falta correr el build de Vue. Ejecuta:<br><br>'
        . '<code>cd CODE_CHALLENGE-VERSION-1 && npm run build</code>'
        . '</h2>',
        200
    );
})->where('any', '.*');
