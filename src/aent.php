#!/usr/bin/env php
<?php
/*

                    _   _____             _              _____
    /\             | | |  __ \           | |            / ____|
   /  \   ___ _ __ | |_| |  | | ___   ___| | _____ _ __| |     ___  _ __ ___  _ __   ___  ___  ___
  / /\ \ / _ \ '_ \| __| |  | |/ _ \ / __| |/ / _ \ '__| |    / _ \| '_ ` _ \| '_ \ / _ \/ __|/ _ \
 / ____ \  __/ | | | |_| |__| | (_) | (__|   <  __/ |  | |___| (_) | | | | | | |_) | (_) \__ \  __/
/_/    \_\___|_| |_|\__|_____/ \___/ \___|_|\_\___|_|   \_____\___/|_| |_| |_| .__/ \___/|___/\___|
                                                                                      | |
                                                                                      |_|

 */

require __DIR__ . '/../vendor/autoload.php';

use TheAentMachine\AentApplication;
use TheAentMachine\AentTraefik\Command\AddEventCommand;
use TheAentMachine\AentTraefik\Command\NewVirtualHostEventCommand;

$application = new AentApplication();

$application->add(new AddEventCommand());
$application->add(new NewVirtualHostEventCommand());

$application->run();
