<?PHP

class rex_xform_validate_compare extends rex_xform_validate_abstract
{

  function enterObject()
  {
    if($this->params["send"]=="1")
    {
      $field_1 = $this->getElement(2);
      $field_2 = $this->getElement(3);
      foreach($this->obj as $o)
      {
        if ($o->getName() == $field_1)
        {
          $id_1 = $o->getId();
          $value_1 = $o->getValue();
        }
        if ($o->getName() == $field_2)
        {
          $id_2 = $o->getId();
          $value_2 = $o->getValue();
        }
      }
      if ($value_1 != $value_2)
      {
        $this->params["warning"][$id_1] = $this->params["error_class"];
        $this->params["warning"][$id_2] = $this->params["error_class"];
        $this->params["warning_messages"][$id_1] = $this->getElement(4);
      }
    }
  }

  function getDescription()
  {
    return "compare -> prüft ob leer, beispiel: validate|compare|label1|label2|warning_message ";
  }

  function getDefinitions()
  {
    return array(
          'type' => 'validate',
          'name' => 'compare',
          'values' => array(
            array( 'type' => 'select_name', 'label' => 'Name der 1. Felder' ),
            array( 'type' => 'select_name', 'label' => 'Name der 1. Felder'),
            array( 'type' => 'text', 	'label' => 'Fehlermeldung'),
          ),
          'description' => '2 Felder werden miteinander verglichen',
      );

  }
}

?>