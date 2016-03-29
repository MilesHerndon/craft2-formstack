<?php

namespace Craft;

class FormstackVariable
{
  public function getForms($options = array())
  {
    return craft()->formstack->getForms($options);
  }

  public function getFormById($options = array())
  {
    return craft()->formstack->getFormById($options);
  }

    public function getWholeFormById($options = array())
  {
    return craft()->formstack->getWholeFormById($options);
  }
}