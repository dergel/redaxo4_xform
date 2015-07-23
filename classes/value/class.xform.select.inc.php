<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_select extends rex_xform_abstract
{

    function enterObject()
    {
        $multiple = $this->getElement('multiple') == 1;

        $options = $this->getArrayFromString($this->getElement('options'));

        if ($multiple) {
            $size = (int) $this->getElement('size');
            if ($size < 2) {
                $size = count($options);
            }
        } else {
            $size = 1;
        }

        if (!$this->params['send'] && $this->getValue() == '' && $this->getElement('default') != '') {
            $this->setValue($this->getElement('default'));
        }

        // ---------- rex_xform_set
        if (isset($this->params['rex_xform_set'][$this->getName()]) && !is_array($this->params['rex_xform_set'][$this->getName()])) {
            $value = $this->params['rex_xform_set'][$this->getName()];
            $values = array();
            if (array_key_exists($value, $options)) {
                $values[] = $value;
            }
            $this->setValue($values);
            $this->setElement('disabled', true);
        }
        // ----------


        if (!is_array($this->getValue())) {
            $this->setValue(explode(',', $this->getValue()));
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.select.tpl.php', compact('options', 'multiple', 'size'));

        $this->setValue(implode(',', $this->getValue()));

        $this->params['value_pool']['email'][$this->getName()] = $this->getValue();
        $this->params['value_pool']['email'][$this->getName()."_NAME"] = isset($options[$this->getValue()]) ? $options[$this->getValue()] : null;

        if ($this->getElement('no_db') != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }
    }

    function getDescription()
    {
        return 'select -> Beispiel: select|name|label|Frau=w,Herr=m|[no_db]|defaultwert|multiple=1|selectsize';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'select',
            'values' => array(
                'name'     => array( 'type' => 'name',   'label' => 'Feld' ),
                'label'    => array( 'type' => 'text',    'label' => 'Bezeichnung'),
                'options'  => array( 'type' => 'text',    'label' => 'Selectdefinition, kommasepariert'),
                'no_db'    => array( 'type' => 'no_db',   'label' => 'Datenbank',          'default' => 0),
                'default'  => array( 'type' => 'text',    'label' => 'Defaultwert'),
                'multiple' => array( 'type' => 'boolean', 'label' => 'Mehrere Felder möglich'),
                'size'     => array( 'type' => 'text',    'label' => 'Höhe der Auswahlbox'),
            ),
            'description' => 'Ein Selectfeld mit festen Definitionen',
            'dbtype' => 'text'
        );

    }

    static function getListValue($params)
    {
        $return = array();

        $new_select = new rex_xform_select();
        $values = $new_select->getArrayFromString($params['params']['field']['options']);

        foreach (explode(',', $params['value']) as $k) {
            if (isset($values[$k])) {
                $return[] = rex_translate($values[$k]);
            }
        }

        return implode('<br />', $return);
    }

    public static function getSearchField($params)
    {
        $options = array();
        $options['(empty)'] = "(empty)";
        $options['!(empty)'] = "!(empty)";

        $new_select = new rex_xform_select();
        $options += $new_select->getArrayFromString($params['field']['options']);

        $params['searchForm']->setValueField('select', array(
                'name' => $params['field']->getName(),
                'label' => $params['field']->getLabel(),
                'options' => $options,
                'multiple' => 1,
                'size' => 5,
            )
        );
    }

    public static function getSearchFilter($params)
    {
        $field = $params['field']->getName();
        $values = (array) $params['value'];

        $where = array();
        foreach($values as $value) {
            switch($value){
                case("(empty)"):
                    $where[] = '`'.$field.'`=""';
                    break;
                case("!(empty)"):
                    $where[] = '`'.$field.'`!=""';
                    break;
                default:
                    $where[] = ' ( FIND_IN_SET("' . mysql_real_escape_string($value) . '", `' . mysql_real_escape_string($field) . '`) )';
                    break;
            }
        }

        if (count($where) > 0) {
            return ' ( ' . implode(" or ", $where) . ' )';

        }

    }
}
