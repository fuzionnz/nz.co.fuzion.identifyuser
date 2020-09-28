<a id='user_lookup' class="action-item crm-hover-button" href='#'>{ts}Get Checksum Link in Email?{/ts}</a>

<script type="text/javascript">
{literal}
CRM.$(function($) {
  cj('#user_lookup').insertAfter('#page-title')

  var rule_id = {/literal}{if $rule_id} {$rule_id} {else} '' {/if}{literal};
  var event_id = {/literal}{if $event_id} {$event_id} {else} '' {/if}{literal};
  var page_id = {/literal}{if $page_id} {$page_id} {else} '' {/if}{literal};
  args = {reset: 1, rule_id: rule_id};
  if (event_id) {
    args.event_id = event_id;
  }
  else {
    args.page_id = page_id;
  }

  $("#user_lookup").click(function() {
    CRM.loadForm(CRM.url('civicrm/dedupeidentifier', args), {autoClose: true})
      // Attach an event handler
      .on('crmFormSuccess', function(event, data) {
        // do something after the form is submitted
        // data includes everything returned by the server
      });
    });
  });

{/literal}
</script>