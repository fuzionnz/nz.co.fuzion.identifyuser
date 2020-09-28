
<span id="enable_lookup-div-html">{$form.enable_lookup.html}
<span id="enable_lookup-div-label">{$form.enable_lookup.label}</span><br />

<span class='description'>{ts}Enabling this will allow user to enter their details on the dedupe rule fields before filling the complete form.{/ts}</span>
</span>

<script type="text/javascript">
{literal}
CRM.$(function($) {
  var formName = {/literal}'{$formName}'{literal};
  if (formName == 'CRM_Contribute_Form_ContributionPage_Settings') {
    $('#is_active').closest('tr').after('<tr id="enable_lookup-tr"><td>&nbsp;</td><td id="enable_lookup_label"></td></tr>');
    $("#enable_lookup-div-html").detach().appendTo("#enable_lookup_label");
    $("#enable_lookup-div-html").appendTo("#enable_lookup_element");
  }
  else {
    $('#dedupe_rule_group_id').closest('tr').after('<tr id="enable_lookup-tr"><td id="enable_lookup_label"></td><td id="enable_lookup_element"></td></tr>');
    $("#enable_lookup-div-label").detach().appendTo("#enable_lookup_label");
    $("#enable_lookup-div-html").detach().appendTo("#enable_lookup_element");
  }
});
{/literal}
</script>