<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_checkbox extends rex_xform_abstract
{
    function enterObject()
    {
        ## set default value attribute
        if ($this->getElement('values') == '') {
            $v = 1; // gecheckt
            $w = 0; // nicht gecheckt

        } else {
            $values = explode(',', $this->getElement('values'));

            if (count($values) == 1) {
                $v = $values[0];
                $w = '';

            } else {
                $v = $values[1];
                $w = $values[0];

            }

        }

        // first time and default is true -> checked
        if ($this->params['send'] != 1 && $this->getElement('default') == 1 && $this->getValue() === '') {
            $this->setValue($v);

        // if check value is given -> checked
        } elseif ($this->getValue() == $v) {
            $this->setValue($v);

        // not checked
        } else {
            $this->setValue($w);
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.checkbox.tpl.php', array('value' => $v));

        ## set values
        $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
        if ($this->getElement(5) != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
    }

    function getDescription()
    {
        return 'checkbox -> Beispiel: checkbox|name|label|Values(0,1)|default clicked (0/1)|[no_db]|cssclassname';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'checkbox',
            'values' => array(
                'name'    => array( 'type' => 'name', 'label' => 'Name' ),
                'label'   => array( 'type' => 'text', 'label' => 'Bezeichnung'),
                'values'  => array( 'type' => 'text', 'label' => 'Werte (0,1) (nicht angeklickt,angeklickt)', 'default' => '0,1'),
                'default' => array( 'type' => 'boolean', 'label' => 'Defaultstatus', 'default' => 0),
                'no_db'   => array( 'type' => 'no_db', 'label' => 'Datenbank', 'default' => 0),
                'css_class' => array('type' => 'text', 'label' => 'cssclassname'),
            ),
            'description' => 'Eine Checkbox mit fester Definition.',
            'dbtype' => 'varchar(255)',
            'famous' => true
        );
    }

    public static function getSearchField($params)
    {
        if ($params['field']->getElement('values') == '') {
            $v = 1; // gecheckt
            $w = 0; // nicht gecheckt

        } else {
            $values = explode(',', $params['field']->getElement('values'));

            if (count($values) == 1) {
                $v = $values[0];
                $w = '';

            } else {
                $v = $values[1];
                $w = $values[0];

            }

        }

        $options = array();
        $options[$v] = 'checked';
        $options[$w] = 'not checked';
        $options[''] = '---';

        $params['searchForm']->setValueField('select', array(
            'name' => $params['field']->getName(),
            'label' => $params['field']->getLabel(),
            'options' => $options
        ));

    }

    public static function getSearchFilter($params)
    {
      $value = $params['value'];
      $field =  $params['field']->getName();

      return ' `' . mysql_real_escape_string($field) . "` =  '" . mysql_real_escape_string($value) . "'";

    }



}
