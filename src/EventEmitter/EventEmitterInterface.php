<?php

namespace Lordvlad\EventEmitter;

interface EventEmitterInterface {

  /**
   * attach new listener to emitter
   * must emit a 'newListener' event
   *
   * @throws InvalidArgumentException if $event is no string
   * @throws TooManyEvents if to many event
   *         listeners are attached
   * @param String $event
   * @param callable $listener
   * @return self
   */

  public function addListener($event, callable $listener);

  /*
   * @alias of addListener
   */

  public function on($event, callable $listener);

  /**
   * attach new listener which will detach itself after
   * its first call
   * must emit 'newListener' and 'removeListener' events
   *
   * @throws InvalidArgumentException if $event is no string
   * @throws TooManyEvents if to many event
   *         listeners are attached
   * @param String $event
   * @param callable $listener
   * @return self
   */

  public function once($event, callable $listener);

  /**
   * remove an event listener
   * must emit 'removeListener' event
   *
   * @throws InvalidArgumentException if $event is no string
   * @param String $event
   * @param callable $listener
   * @param self
   */

  public function removeListener($event, callable $listener);

  /**
   * remove all event listeners or at least those
   * specified with $event, if given
   *
   * @throws InvalidArgumentException if $event is no string
   * @param String [$event = null]
   * @return self
   */

  public function removeAllListeners($event = null);

  /**
   * set max number of listeners.
   *
   * @throws InvalidArgumentException if $event is no number
   * @param numeric $n
   * @return self
   */

  public function setMaxListeners($n);

  /**
   * Returns an array of listeners for the specified event.
   *
   * @throws InvalidArgumentException if $event is no string
   * @return callable[]
   * @return self
   */

  public function listeners($event);

  /**
   * Execute each of the listeners in order with the supplied arguments.
   *
   * @param String $event
   * @param mixed...
   * @throws InvalidArgumentException if $event is no string
   * @return self
   */

  public function emit($event, $data = null);

  /**
   * Return the number of listeners for a given event.
   *
   * @return int
   */

  public static function listenerCount(EventEmitterInterface $emitter, $event);
}
