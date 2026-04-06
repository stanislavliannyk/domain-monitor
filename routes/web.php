<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Each module owns its own routes file. This file simply includes them.
| The order matters: Auth first so the 'login' named route exists before
| other modules reference it via the 'auth' middleware redirect.
|--------------------------------------------------------------------------
*/

require app_path('Modules/Auth/routes.php');
require app_path('Modules/Dashboard/routes.php');
require app_path('Modules/Domain/routes.php');
