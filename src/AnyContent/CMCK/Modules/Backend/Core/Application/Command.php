<?php

namespace AnyContent\CMCK\Modules\Backend\Core\Application;

abstract class Command extends \Knp\Command\Command
{

    const escapeGreen = "\033[0;32m";
    const escapeBlue  = "\033[1;34m";

    const escapeError = "\033[41;37m";

    const escapeReset = "\033[0m";
}