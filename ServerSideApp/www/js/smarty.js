
window.smarty = {
  
  /**
   * Removes the default value from a form field.
   * @param   object  DOM node reference to a text field or input.
   * @param   string  The default value
   * @return  void
   */
  removeDefaultValue : function (el, defaultVal) {
    if (el.value == defaultVal) {
      el.value = "";
    } else {
      el.select();
      el.focus();
    }
  },
  
  /**
   * Restores the default value in a form field.
   * @param   object  DOM node reference to a text field or input.
   * @param   string  The default value
   * @return  void
   */
  restoreDefaultValue : function(el, defaultVal) {
    if (el.value == "") {
      el.value = defaultVal;
    }
  }
  
};