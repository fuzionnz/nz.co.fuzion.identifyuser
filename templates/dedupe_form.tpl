<script type="text/javascript">
{literal}
CRM.$(function($) {
  var rule_id = {/literal}{$rule_id}{literal};
  var event_id = {/literal}{$event_id}{literal};
  args = {reset: 1, rule_id: rule_id, event_id: event_id};
  CRM.loadForm(CRM.url('civicrm/dedupeidentifier', args), {autoClose: true})
    // Attach an event handler
    .on('crmFormSuccess', function(event, data) {
      // do something after the form is submitted
      // data includes everything returned by the server
    });

  });
{/literal}
</script>