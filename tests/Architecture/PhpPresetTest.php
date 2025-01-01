<?php

use App\Console\Commands\GoogleSync;

arch()->preset()->php()->ignoring(GoogleSync::class);
