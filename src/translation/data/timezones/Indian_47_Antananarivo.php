<?php

/**
 * Data file for Indian/Antananarivo timezone, compiled from the olson data.
 *
 * Auto-generated by the phing olson task on 10/25/2008 11:59:58
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
      'rawOffset' => 10800,
      'dstOffset' => 0,
      'name' => 'EAT',
    ),
    1 => 
    array (
      'rawOffset' => 10800,
      'dstOffset' => 3600,
      'name' => 'EAST',
    ),
  ),
  'rules' => 
  array (
    0 => 
    array (
      'time' => -1846293004,
      'type' => 0,
    ),
    1 => 
    array (
      'time' => -499924800,
      'type' => 1,
    ),
    2 => 
    array (
      'time' => -492062400,
      'type' => 0,
    ),
  ),
  'finalRule' => 
  array (
    'type' => 'static',
    'name' => 'EAT',
    'offset' => 10800,
    'startYear' => 1955,
  ),
  'name' => 'Indian/Antananarivo',
);

?>