<?php

namespace Lordvlad\EventEmitter;

class EventEmitter implements EventEmitterInterface {

  private $_listeners = [];
  private $_maxListeners = 10;

  const NOTASTRING = '%s expects its first parameter to be an String. Instead, a %s was given';

  const NOTANINT = '%s expects its first parameter to be an Integer. Instead, a %s was given';

  const TOOMANY = 'Maximum number of attached event listeners (%d) exeeded.';

  public function addListener($event, callable $listener) {
    if (!is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    if (array_reduce($this->_listeners, function($c, $i){return $c += count($i);}, 0) > $this->_maxListeners)
      throw new RuntimeException(sprintf(self::TOOMANY, $this->_maxListeners));

    if (!isset($this->_listeners[$event]))
      $this->_listeners[$event] = [];

    array_push($this->_listeners[$event], $listener);
    $this->emit('newListener', $listener);

    return $this;
  }

  public function on($event, callable $listener) {
    if (!is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    return $this->addListener($event, $listener);
  }

  public function once($event, callable $listener){
    if (!is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    $me = $this;
    $fn = function($d)use($me, $event, $listener){
      $listener($d);
      $me->removeEventListener($event, $fn);
    };
    $this->addListener($event, $fn);

    return $this;
  }

  public function removeListener($event, callable $listener){
    if (!is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    if (isset($this->_listeners[$event])) {
      $k = array_search($listener, $this->_listeners[$event]);
      if ($k !== false)
        unset($this->_listeners[$event][$k]);
    }

    return $this;
  }

  public function removeAllListeners($event = null){
    if (isset($event) && !is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    if (isset($event))
      unset($this->_listeners[$event]);
    else
      $this->_listeners = [];

    return $this;
  }

  public function setMaxListeners($n) {
    if (!is_int($n))
      throw new InvalidArgumentException(sprintf(self::NOTANINT, __METHOD__, gettype($n)));

    $this->_maxListeners = $n;
  }

  public function listeners($event){
    if (!is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    return isset($this->_listeners[$event]) ? $this->_listeners[$event] : [];
  }

  public function emit($event, $data = null) {
    if (isset($event) && !is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    $data = func_get_args();
    array_shift($data);
    if (isset($this->_listeners[$event])) {
      foreach ($this->_listeners[$event] as $listener) {
        call_user_func_array($listener, $data);
      }
    }
    return $this;
  }

  public static function listenerCount(EventEmitterInterface $emitter, $event) {
    if (isset($event) && !is_string($event))
      throw new InvalidArgumentException(sprintf(self::NOTASTRING, __METHOD__, gettype($event)));

    return count($emitter->listeners($event));
  }
}
