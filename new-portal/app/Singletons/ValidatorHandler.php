<?php
// Author: Davi Mendes Pimentel
// last modified date: 20/12/2019

namespace App\Singletons;

/**
 * General Purpose Class: ValidatorHandler
 *
 * Singleton Class that will be responsible
 * by handle with ValidatorHandlers around of the program
 *
 */

use Closure;
use Illuminate\Validation\Validator;

const NONE = null;  // represents nothing
const SUCCESS = true;   // represents success
const FAIL = false; // represents fail

class ValidatorHandler {
  private static $validatorHandler = null;

  private $last_validation_result = NONE;

  /**
   * Handle with this $validator instance
   *
   * @return $this
   */
  public function handleThis($validator) {

    /**
     * Verifies if the @param $validator is
     * a instance of \Illuminate\Validation\Validator
     */
    if($validator instanceof Validator)
      if($validator->fails())
        $this->last_validation_result = FAIL;
      else
        $this->last_validation_result = SUCCESS;

    // returns $this anyway
    return $this;
  }

  /**
   * Verify if the last validation has invalid inputs, if yes, redirects with given params
   * if not, returns null
   *
   * @param string $redirect_url
   * @param array $params
   * @return null|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
   */
  public function ifValidationFailsRedirect(string $redirect_url, array $params = []) {
    // verify if the last validation exists and if has fail
    if($this->last_validation_result !== NONE && $this->last_validation_result === FAIL) {
      return redirect($redirect_url)->with('params', $params);
    }

    // if has no failures occurred, returns null
    return null;
  }

  /**
   * Verify if the last validation has invalid inputs, if yes, executes the closure
   * if not, returns this ValidatorHandler object
   *
   * @param Closure $fail_function
   * @return null|\Closure
   */
  public function ifValidationFails(Closure $fail_function) {
    // verify if the last validation exists and if has fail
    if($this->last_validation_result !== NONE && ! $this->last_validation_result) {
      return $fail_function();
    }

    // if has no failures occurred, returns null
    return null;
  }

  /**
   * Returns $this or $other_to_return value if a fail has occurred,
   * if not, returns nullm like a "signal" of an error ocurred
   *
   * @param mixed $other_to_return
   * @return null|mixed|$this
   */
  public function ifValidationFailsReturnsThis($other_to_return = null) {

    $value_to_return = $this;
    if($other_to_return) {
        $value_to_return = $other_to_return;
    }

    // verify if the last validation exists and if has fail
    if($this->last_validation_result !== NONE && ! $this->last_validation_result) {
      return $value_to_return;
    }

    // if has no failures occurred, returns null
    return null;
  }

  /**
   * Gets the validator handler
   *
   * @return \App\Singletons\ValidatorHandler
   */
  public static function __callstatic($arg1, $arg2) {
    if(self::$validatorHandler === null)
      self::$validatorHandler = new ValidatorHandler();

    return self::$validatorHandler;
  }
}

?>
