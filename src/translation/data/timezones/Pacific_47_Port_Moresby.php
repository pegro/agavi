<?php

/**
 * Data file for Pacific/Port_Moresby timezone, compiled from the olson data.
 *
 * Auto-generated by the phing olson task on 07/21/2009 09:13:27
 *
 * @package    agavi
 * @subpackage translation
 *
 * @copyright  Authors
 * @copyright  The Agavi Project
 *
 * @since      0.11.0
 *
 * @version    $Id$
 */

return array (
  'types' => 
  array (
    0 => 
    array (
      'rawOffset' => 35312,
      'dstOffset' => 0,
      'name' => 'PMMT',
    ),
    1 => 
    array (
      'rawOffset' => 36000,
      'dstOffset' => 0,
      'name' => 'PGT',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -2840176120,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => -2366790512,
      'type' => 1,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'PGT',
    'offset' => 36000,
    'startYear' => 1895,
  ),
  'name' => 'Pacific/Port_Moresby',
);

?>