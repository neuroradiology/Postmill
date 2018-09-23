<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Simple availability tests to ensure the application isn't majorly screwed up.
 */
class ApplicationAvailabilityTest extends WebTestCase {
    /**
     * @dataProvider publicUrlProvider
     *
     * @param string $url
     */
    public function testCanAccessPublicPages($url) {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider authUrlProvider
     *
     * @param string $url
     */
    public function testCanAccessPagesThatNeedAuthentication($url) {
        $client = self::createClient([], [
            'PHP_AUTH_USER' => 'emma',
            'PHP_AUTH_PW' => 'goodshit',
        ]);
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * @dataProvider authUrlProvider
     *
     * @param string $url
     */
    public function testCannotAccessPagesThatNeedAuthenticationWhenNotAuthenticated($url) {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isRedirect());
        $this->assertStringEndsWith('/login', $client->getResponse()->headers->get('Location'));
    }

    /**
     * @dataProvider redirectUrlProvider
     *
     * @param string $expectedLocation
     * @param string $url
     */
    public function testRedirectedUrlsGoToExpectedLocation($expectedLocation, $url) {
        $client = self::createClient();
        $client->followRedirects();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(
            "http://localhost{$expectedLocation}",
            $client->getCrawler()->getUri()
        );
    }

    /**
     * Public URLs that should exist when fixtures are loaded into a fresh
     * database.
     */
    public function publicUrlProvider() {
        yield ['/'];
        yield ['/hot'];
        yield ['/new'];
        yield ['/top'];
        yield ['/controversial'];
        yield ['/most_commented'];
        yield ['/all/hot'];
        yield ['/all/new'];
        yield ['/all/top'];
        yield ['/all/controversial'];
        yield ['/all/most_commented'];
        yield ['/featured/hot'];
        yield ['/featured/new'];
        yield ['/featured/top'];
        yield ['/featured/controversial'];
        yield ['/featured/most_commented'];
        yield ['/featured/hot.atom'];
        yield ['/featured/new.atom'];
        yield ['/featured/top.atom'];
        yield ['/featured/controversial.atom'];
        yield ['/featured/most_commented.atom'];
        yield ['/news/hot'];
        yield ['/news/new'];
        yield ['/news/top'];
        yield ['/news/controversial'];
        yield ['/news/most_commented'];
        yield ['/news/hot.atom'];
        yield ['/news/new.atom'];
        yield ['/news/top.atom'];
        yield ['/news/controversial.atom'];
        yield ['/news/most_commented.atom'];
        yield ['/news/1/comment/1'];
        yield ['/news/bans'];
        yield ['/news/moderation_log'];
        yield ['/forums'];
        yield ['/forums/by_name'];
        yield ['/forums/by_title'];
        yield ['/forums/by_subscribers'];
        yield ['/forums/by_submissions'];
        yield ['/forums/by_name/1'];
        yield ['/forums/by_title/1'];
        yield ['/forums/by_subscribers/1'];
        yield ['/forums/by_submissions/1'];
        yield ['/login'];
        yield ['/login/reset_password'];
        yield ['/registration'];
        yield ['/user/emma'];
    }

    public function redirectUrlProvider() {
        yield ['/cats', '/f/cats/'];
        yield ['/news', '/f/NeWs/hot'];
        yield ['/news/new', '/f/NeWs/new'];
        yield ['/news/top', '/f/NeWs/top'];
        yield ['/news/controversial', '/f/NeWs/controversial'];
        yield ['/news/most_commented', '/f/NeWs/most_commented'];
        yield ['/news/1/comment/1', '/f/NeWs/1/comment/1'];
        yield ['/news/hot.atom', '/f/news/hot/1.atom'];
        yield ['/news/new.atom', '/f/news/new/1.atom'];
    }

    /**
     * URLs that need authentication to access.
     */
    public function authUrlProvider() {
        yield ['/subscribed/hot'];
        yield ['/subscribed/new'];
        yield ['/subscribed/top'];
        yield ['/subscribed/controversial'];
        yield ['/subscribed/most_commented'];
        yield ['/moderated/hot'];
        yield ['/moderated/new'];
        yield ['/moderated/top'];
        yield ['/moderated/controversial'];
        yield ['/moderated/most_commented'];
        yield ['/forums/create'];
        yield ['/news/edit'];
        yield ['/news/add_moderator'];
        yield ['/news/delete'];
        yield ['/inbox'];
        yield ['/submit'];
        yield ['/submit/news'];
        yield ['/user/emma/block_list'];
    }
}
