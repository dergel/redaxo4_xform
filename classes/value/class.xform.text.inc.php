<?php

/**
 * XForm
 * @author jan.kristinus[at]redaxo[dot]org Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

class rex_xform_text extends rex_xform_abstract
{

    function enterObject()
    {

        $this->setValue((string) $this->getValue());

        if ($this->getValue() == '' && !$this->params['send']) {
            $this->setValue($this->getElement(3));
        }

        $this->params['form_output'][$this->getId()] = $this->parse('value.text.tpl.php');

        $this->params['value_pool']['email'][$this->getName()] = stripslashes($this->getValue());
        if ($this->getElement(4) != 'no_db') {
            $this->params['value_pool']['sql'][$this->getName()] = $this->getValue();
        }

    }

    function getDescription()
    {
        return 'text -> Beispiel: text|name|label|defaultwert|[no_db]|cssclassname|dbtype';
    }

    function getDefinitions()
    {
        return array(
            'type' => 'value',
            'name' => 'text',
            'values' => array(
                'name'      => array( 'type' => 'name',    'label' => 'Feld' ),
                'label'     => array( 'type' => 'text',    'label' => 'Bezeichnung'),
                'default'   => array( 'type' => 'text',    'label' => 'Defaultwert'),
                'no_db'     => array( 'type' => 'no_db',   'label' => 'Datenbank',  'default' => 0),
                'css_class' => array( 'type' => 'text',    'label' => 'cssclassname'),
                'dbtype'    => array( 'type' => 'select',  'label' => 'Datenbanktyp', 'default' => 'text', 'options' => array('text' => 'text', 'mediumtext' => 'mediumtext', 'longtext' => 'longtext', 'varchar(50)' => 'varchar(50)', 'varchar(100)' => 'varchar(100)', 'varchar(255)' => 'varchar(255)', 'date' => 'date', 'datetime' => 'datetime', 'int' => 'int')),
            ),
            'description' => 'Ein einfaches Textfeld als Eingabe',
            'dbtype' => 'text',
            'famous' => true,
            'hooks' => array(
              'preCreate' => 'rex_xform_text::getDBType'
            )
        );

    }

    public static function getDBType($params)
    {
      return $params['dbtype'] ?: 'text';
    }

    public static function getSearchField($params)
    {
        $params['searchForm']->setValueField('text', array('name' => $params['field']->getName(), 'label' => $params['field']->getLabel()));
    }

    public static function getSearchFilter($params)
    {
        $value = $params['value'];
        $field =  $params['field']->getName();

        if ($value == '(empty)') {
            return ' (`' . mysql_real_escape_string($field) . '` = "" or `' . mysql_real_escape_string($field) . '` IS NULL) ';

        } elseif ($value == '!(empty)') {
            return ' (`' . mysql_real_escape_string($field) . '` <> "" and `' . mysql_real_escape_string($field) . '` IS NOT NULL) ';

        }

        $pos = strpos($value, '*');
        if ($pos !== false) {
            $value = str_replace('%', '\%', $value);
            $value = str_replace('*', '%', $value);
            return ' `' . mysql_real_escape_string($field) . "` LIKE  '" . mysql_real_escape_string($value) . "'";
        } else {
            return ' `' . mysql_real_escape_string($field) . "` =  '" . mysql_real_escape_string($value) . "'";
        }

    }

}
