<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author o.zgolich
 */

class Form_Admin_Coupons extends CM_Form_Abstract
{
    protected function init()
    {
        $this->add_plugin(new CM_Form_Plugin_ORM());
        $this->set_field('code', new CM_Field_String(), 1);
        $this->set_field('discount', new CM_Field_String(), 2);
        $this->set_field('time_end', new CM_Field_String(), 3);
        $this->set_field('active', new CM_Field_Boolean(), 4);


    }
}