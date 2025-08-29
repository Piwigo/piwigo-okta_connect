{if $id == "mbIdentification" and isset($U_LOGIN)}
  {html_style}
  dl#mbIdentification dd:first-of-type { padding-bottom:0 !important; }
  #mbIdentification .oktau { margin:0 1px; }
  img.oktau { padding: 0; margin: 0; width: 100%;}
  legend.oktau { font-size: 12px; }
  hr.oktau { padding: 0.5rem; }
  {/html_style}
  <dd>
    <form id="quickconnect" method="get" action="{$U_LOGIN}">
      <fieldset style="text-align:center;">
        <legend class="oktau">{'OKTA Authentication'|translate}</legend>
        {strip}
              <a href="{$OKTA_LOGIN_URL}" class="btn btn-raised btn-primary">{'Sign in'|translate}</a>
  {if $OKTA_CONNECT['altaccess']}
              <hr class="oktau"/>
              <a href="{$PIWIGO_LOGIN_URL}" class="btn btn-raised btn-secondary">{'Sign in using Piwigo'|translate}</a>
  {/if}
        {/strip}
      </fieldset>
  </form>
  </dd>
{/if}