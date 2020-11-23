<?php

namespace Tests\Browser;

use App\Date;
use App\Util;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\TimeOutException;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;


abstract class AbstractTest extends DuskTestCase
{

    protected function output($line)
    {
        fwrite(STDERR, "$line\n");
    }

    /**
     * @param Browser $browser
     *
     * @throws TimeOutException
     */
    protected function login(Browser $browser)
    {
        $this->output("Logging in");

        $browser->visit('https://www.linkedin.com/login')
            ->assertSee('LinkedIn')
            ->type('input[id=username]', Util::linkedinUsername())
            ->type('input[id=password]', Util::linkedinPassword())
            ->click('button[type=submit]')
            ->waitForText('Start a post', 60);
    }
}
