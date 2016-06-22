<?php

/**
* @file
* Contains \Drupal\deepak\Controller\DeepakController.
*/

namespace Drupal\deepak\Controller;
use Drupal\Core\Controller\ControllerBase;

class DeepakController extends ControllerBase
{
  	public function content() {
    	return array
		( 
	        '#type' => 'markup',
	        '#markup' => $this->t('Deepak!'),
	    );
}
}
