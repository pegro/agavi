<?php

// +---------------------------------------------------------------------------+
// | This file is part of the Agavi package.                                   |
// | Copyright (c) 2003-2006 the Agavi Project.                                |
// |                                                                           |
// | For the full copyright and license information, please view the LICENSE   |
// | file that was distributed with this source code. You can also view the    |
// | LICENSE file online at http://www.agavi.org/LICENSE.txt                   |
// |   vi: set noexpandtab:                                                    |
// |   Local Variables:                                                        |
// |   indent-tabs-mode: t                                                     |
// |   End:                                                                    |
// +---------------------------------------------------------------------------+

/**
 * AgaviDateValidator verifies a parameter is of a date format.
 *
 * @package    agavi
 * @subpackage validator
 *
 * @author     Sean Kerr <skerr@mojavi.org>
 * @author     Agavi Project <info@agavi.org>
 * @copyright  (c) Authors
 * @since      0.9.0
 *
 * @version    $Id$
 */
class AgaviDateValidator extends AgaviValidator
{

	/**
	 * Execute this validator.
	 *
	 * @param      mixed A file or parameter value/array.
	 * @param      error An error message reference.
	 *
	 * @return     bool true, if this validator executes successfully, otherwise
	 *                  false.
	 *
	 * @author     Bob Zoller <bzoller@agavi.org>
	 * @since      1.0
	 */
	public function execute (&$value, &$error)
	{
		if(empty($value) || strtotime($value) === false || strtotime($value) === -1) {
			$error = $this->getParameter('error');
			return false;
		}
		return true;
	}

	/**
	 * Initialize this validator.
	 *
	 * @param      AgaviContext The current application context.
	 * @param      array        An associative array of initialization parameters.
	 *
	 * @author     Bob Zoller <bob@agavi.org>
	 * @since      1.0
	 */
	public function initialize(AgaviContext $context, $parameters = array())
	{
		$this->setParameter('error', 'Date is not valid.');
		parent::initialize($context, $parameters);
	}

}

?>