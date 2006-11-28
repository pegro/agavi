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
 * AgaviOperatorValidator
 * 
 * Operators group a couple if validators...
 * 
 * @package    agavi
 * @subpackage validator
 *
 * @author     Uwe Mesecke <uwe@mesecke.net>
 * @copyright  (c) Authors
 * @since      0.11.0
 *
 * @version    $Id$
 */
abstract class AgaviOperatorValidator extends AgaviValidator implements AgaviIValidatorContainer
{
	/**
	 * @var        array The child validators.
	 */
	protected $children = array();

	/**
	 * @var        array The errors of the child validators.
	 */
	protected $errors = array();
	
	/**
	 * @var        int The highest error severity in the container.
	 */
	protected $result = AgaviValidator::SUCCESS;
	
	/**
	 * constructor
	 * 
	 * @param      AgaviIValidatorContainer The parent ValidatorContainer
	 *                                      (mostly the ValidatorManager)
	 * @param      array                    The parameters from the config file.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function __construct(AgaviIValidatorContainer $parent, array $arguments, array $errors = array(), array $parameters = array())
	{
		parent::__construct($parent, $arguments, $errors, $parameters);
		
		if($this->getParameter('skip_errors')) {
			/*
			 * if the operator is configured to skip errors of the
			 * child validators, a new error manager is created
			 */
		} else {
			// else the parent's error manager is taken
		}
	}

	/**
	 * Method for checking the validity of child validators.
	 * 
	 * Some operators (XOR and NOT) need a specific quantity of child
	 * validators so they implement an algorithm that checks of the setup
	 * is valid. This method is run first when execute() is invoked and
	 * should throw an exception if the setup is invalid.
	 * 
	 * @throws     <b>AgaviValidatorException<b> If the  quantity of child 
	 *                                           validators is invalid
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	protected function checkValidSetup()
	{
	}
	
	/**
	 * Shutdown method, for shutting down the model etc.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function shutdown()
	{
		foreach($this->children as $child) {
			$child->shutdown();
		}
	}
	
	/**
	 * Submits an error to the error manager.
	 * 
	 * The stuff in the parameter specified in $index is submitted to the
	 * error manager. If there is no parameter with this name, then 'error'
	 * is tryed as an parameter and if even this fails, the stuff in
	 * $backupError is sent.
	 * 
	 * @param      string The name of the error parameter to fetch the message 
	 *                    from.
	 * @param      string An default error message to be used if the given error 
	 *                    has no message set.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	protected function throwError($index = 'error', $backupError = null)
	{
		if($index !== null && isset($this->errorMessages[$index])) {
			$error = $this->errorMessages[$index];
		} elseif(isset($this->errorMessages[''])) {
			// check if a default error exists.
			$error = $this->errorMessages[''];
		} else {
			$error = $backupError;
		}

		// if no error msg was supplied rethrow the child errors
		if($error === null) {
			foreach($this->errors as $childError) {
				$this->parentContainer->reportError($childError[0], $childError[1]);
			}
		} else {
			if($this->hasParameter('translation_domain')) {
				$error = $this->getContext()->getTranslationManager()->_($error, $this->getParameter('translation_domain'));
			}

			$this->parentContainer->reportError($this, $error);
		}
	}

	/**
	 * Reports an error to the parent container.
	 * 
	 * @param      AgaviValidator The validator where the error occured.
	 * @param      string         An error message.
	 * 
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 * @see        AgaviIValidatorContainer::reportError
	 */
	public function reportError(AgaviValidator $validator, $errorMsg)
	{
		$this->errors[] = array($validator, $errorMsg);
	}
	
	/**
	 * Adds new child validator.
	 * 
	 * @param      AgaviValidator The new child validator.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function addChild(AgaviValidator $validator)
	{
		$this->children[] = $validator;
	}
	
	/**
	 * Registers an array of validators.
	 * 
	 * @param      array The array of validators.
	 * 
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function registerValidators(array $validators)
	{
		foreach($validators AS $validator) {
			$this->addChild($validator);
		}
	}
	
	/**
	 * Gets the request from the parent.
	 * 
	 * @return     AgaviRequest The parent's request.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function getRequest()
	{
		return $this->parentContainer->getRequest();
	}
	
	/**
	 * Gets parent's dependency manager.
	 * 
	 * @return     AgaviDependencyManager The parent's dependency manager.
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function getDependencyManager()
	{
		return $this->parentContainer->getDependencyManager();
	}

	/**
	 * Returns the result from the error manager.
	 * 
	 * @return     int The result of the validation process.
	 * 
	 * @author     Dominik del Bondio <ddb@bitxtender.com>
	 * @since      0.11.0
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * Executes the validator.
	 * 
	 * Executes the operators validate()-Method after checking the quantity
	 * of child validators with checkValidSetup().
	 * 
	 * @param      AgaviParameterHolder The parameters which should be validated.
	 *
	 * @return     int The result of validation (SUCCESS, NONE, NOTICE, ERROR, CRITICAL).
	 *
	 * @author     Uwe Mesecke <uwe@mesecke.net>
	 * @since      0.11.0
	 */
	public function execute(AgaviParameterHolder $parameters)
	{
		// check if we have a valid setup of validators
		$this->checkValidSetup();
		
		$result = parent::execute($parameters);
		if($result != AgaviValidator::SUCCESS && !$this->getParameter('skip_errors') && $this->result == AgaviValidator::CRITICAL) {
			/*
			 * one of the child validators resulted with CRITICAL
			 * we change our operator's result to CRITICAL, too so the
			 * surrounding validator container is aware of the critical
			 * result and can abort further validation... 
			 */
			$result = AgaviValidator::CRITICAL;
		}
		
		return $result;
	}
}

?>