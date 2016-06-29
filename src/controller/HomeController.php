<?php
/**
 * Part of Schakel Marketeers Login
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 * @license Proprietary
 */

namespace Schakel\Controller;

use Schakel\Core\Twig;

/**
 * Returns a simple homepage.
 *
 * @author Roelof Roos <roelof@schakelmarketeers.nl>
 */
class HomeController {

    /**
     * Renders the homepage. Takes some arguments as that's required.
     *
     * @param array $args
     * @return AbstractRenderer Twig renderer
     */
    public function renderHome(array $args) {
        return new Twig('home.twig');

    }

}
