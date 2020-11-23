<?php

namespace Tests\Browser;

use App\Date;
use App\Person;
use Laravel\Dusk\Browser;

class ScrapePeopleLinkedIn extends AbstractTest
{

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function testCrawl()
    {
        $params = array(
            "view"       => "People To Scrape",
            "maxRecords" => 10,
        );
        $people = (new Person())->getRecords($params);
        if (!$people) {
            $this->output("None found");
            return;
        }

        $this->browse(function (Browser $browser) use ($people) {
            $this->login($browser);

            foreach ($people as $person) {
                $this->handlePerson($browser, $person);
            }
        });
    }

    protected function handlePerson(Browser $browser, Person $person)
    {
        $url = $person->linkedInURL();

        $this->output("Scraping profile ($url)");

        $browser->visit($url)
            ->waitFor('.pv-top-card--list');

        $imageUrl = $browser->attribute(".pv-top-card--photo img", 'src');
        $name = $browser->text(".pv-top-card--list > li");
        $this->output(" - Saving image: $imageUrl");

        $person->save([
            'Avatar'           => [
                [
                    'url' => $imageUrl,
                ]
            ],
            'Name'             => $name,
            'LinkedIn Scraped' => Date::now()->toDateTimeString(),
        ]);

        $person->saveToSearchIndex();
    }
}
