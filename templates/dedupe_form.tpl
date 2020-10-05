<a id='user_lookup' class="action-item crm-hover-button" href='#'>{ts}Get OTP{/ts}</a>

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
    CRM.loadForm(CRM.url('civicrm/dedupeidentifier', args), {autoClose: false, refreshAction: true, openInline: true})
      // Attach an event handler
      .on('crmFormSuccess', function(event, data) {
        if (data.otp_sent && data.contact_id) {
          args.contact_id = data.contact_id;
          CRM.loadForm(CRM.url('civicrm/verifyotp', args), {autoClose: false})
             .on('crmFormSuccess', function(event, data) {
               if (data.checksum_url) {
                 window.location.href = data.checksum_url;
               }
             });
        }
      });
    });
  });

{/literal}
</script>